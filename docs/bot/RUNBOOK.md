# Runbook pilota — OJCIEC 4.0

Ten dokument zbiera minimalną procedurę operatorską dla warstwy Discord + API przed pilotem bez dashboardu frontendowego.

## 1. Zakres

Runbook obejmuje:

- bota Discord uruchamianego lokalnie albo przez Docker Compose,
- API pod FastAPI,
- Redis jako event bus i cache,
- zewnętrzne MongoDB jako główne źródło prawdy dla runtime'u.

Nie obejmuje jeszcze pełnej automatyzacji CI/CD ani produkcyjnego monitoringu zewnętrznego. To jest minimum operacyjne pod pierwszy pilot.

## 2. Topologia runtime

- Bot: kontener `ojciec-bot`
- API: kontener `ojciec-api`
- Redis: kontener `ojciec-redis`
- MongoDB: zewnętrzne, wskazane przez `MONGO_URI`

Zasada operacyjna:

- MongoDB jest źródłem prawdy dla danych runtime i moderacji.
- Redis jest warstwą event bus/cache i może być odtwarzany; po awarii trzeba pozwolić botowi zrehydrować stan z Mongo.
- Logi kontenerowe i katalog `logs/` nie zastępują backupu danych.

## 3. Wymagane sekrety i konfiguracja

Przed startem muszą istnieć poprawne wartości w `.env` lub środowisku:

- `DISCORD_TOKEN`
- `MONGO_URI`
- `REDIS_URL`
- `JWT_SECRET` dla API

Minimalne zasady:

- nie trzymać sekretów w repo,
- po zmianie sekretu restartować odpowiedni proces,
- po incydencie tokenowym rotować sekret, a nie tylko restartować usługę.

## 4. Start i stop

### Lokalny smoke bota

```bash
timeout 90s ./.venv/bin/python main.py
```

To jest podstawowy smoke przed merge albo po lokalnym hotfixie.

### Compose — podniesienie usług

```bash
docker compose up -d redis bot api
docker compose ps
```

### Compose — logi

```bash
docker compose logs -f bot
docker compose logs -f api
docker compose logs -f redis
```

### Compose — restart pojedynczej usługi

```bash
docker compose restart bot
docker compose restart api
docker compose restart redis
```

### Compose — zatrzymanie środowiska

```bash
docker compose down
```

## 5. Minimalny smoke po starcie

Po każdym restarcie albo deployu sprawdź kolejno:

1. `docker compose ps` albo log lokalnego startu bota.
2. Czy bot pokazuje gotowość shardów i ładuje wszystkie cogi bez błędów.
3. Czy API odpowiada:

```bash
curl -s http://127.0.0.1:8000/health
curl -s http://127.0.0.1:8000/ready
```

4. Czy Redis odpowiada:

```bash
docker exec ojciec-redis redis-cli ping
```

5. Czy po starcie bota pojawiają się logi bootstrapu interakcji, a nie wyjątki przy rehydratacji widoków.

Minimalny sygnał sukcesu:

- bot dochodzi do stanu gotowości,
- API ma `status=ok` na `/health`,
- `/ready` nie pokazuje trwałej degradacji Mongo/Redis,
- nie ma pętli błędów przy LFG/Temp VC/InteractionRegistry.

## 6. Backup i restore

### MongoDB — backup

Minimalna procedura backupu:

```bash
mkdir -p backups
mongodump --uri "$MONGO_URI" --archive="backups/ojciec-$(date +%F-%H%M).archive.gz" --gzip
```

Zasady:

- backup musi mieć znacznik czasu,
- przed pilotem trzeba wykonać przynajmniej jeden test restore,
- sam fakt istnienia pliku backupu nie jest uznawany za gotowość DR.

### MongoDB — restore test

Najpierw odtwarzaj na izolowanej bazie testowej:

```bash
mongorestore --uri "$MONGO_URI" --nsFrom="*" --nsTo="ojciec_restore_test.*" --archive="backups/NAZWA.archive.gz" --gzip
```

Po restore sprawdź ręcznie minimum:

- `mod_cases`
- `bans`
- `audit_events`
- `temp_voice_channels`
- `active_voice_sessions`

Nie przywracaj backupu na aktywną produkcyjną bazę bez osobnego snapshotu bezpieczeństwa.

### Redis

W aktualnym modelu Redis nie jest głównym źródłem prawdy dla danych produktu.

Lokalne minimum:

```bash
docker exec ojciec-redis redis-cli SAVE
```

Uwagi:

- Compose używa AOF, więc Redis ma podstawową persystencję,
- po utracie Redis priorytetem jest przywrócenie usługi i restart bota/API,
- runtime powinien odtworzyć trwały stan z Mongo, zamiast traktować Redis jako jedyne źródło danych.

## 7. Scenariusze awaryjne

### Bot nie wstaje albo shard się nie łączy

1. Sprawdź logi bota.
2. Zweryfikuj `DISCORD_TOKEN` i połączenie z Mongo/Redis.
3. Zrestartuj tylko bota.
4. Jeśli problem wraca, odpal lokalny smoke `timeout 90s ./.venv/bin/python main.py` poza Compose.

### API ma `degraded` na `/ready`

1. Sprawdź, czy padł Mongo czy Redis.
2. Jeśli Redis padł, podnieś Redis, potem API, potem bota.
3. Jeśli Mongo padł, nie wykonuj akcji write-heavy do czasu przywrócenia połączenia.

### Token bota wyciekł

1. Natychmiast zrotuj token w panelu Discord Developer Portal.
2. Zaktualizuj `.env` / sekret runtime.
3. Zrestartuj bota.
4. Sprawdź logi pod kątem nietypowych akcji po czasie potencjalnego wycieku.

### Zły deploy / zła seria zmian

1. Zatrzymaj rollout na kolejnych guildach.
2. Przywróć poprzedni obraz lub poprzedni commit runtime.
3. Uruchom minimalny smoke bota i API.
4. Dopiero po czystym smoke wróć do ręcznego E2E.

## 8. Rollout pilota

Kolejność rolloutu:

1. Serwer testowy.
2. Jeden serwer pilotażowy.
3. Dwa serwery z realnym ruchem LFG sieciowym.
4. Dopiero potem szersza sieć.

Reguły:

- nie wdrażać szerokiego pilota bez świeżego backupu Mongo,
- nie mieszać rolloutu z równoległym dużym refaktorem,
- po każdym etapie rolloutu przejść skróconą checklistę z `docs/PILOT_CHECKLIST.md`.

## 9. Minimalny gate przed żywym E2E

Przed końcowym testem na żywym serwerze muszą być gotowe:

- świeży smoke boot bota,
- gotowy backup Mongo i zapis ścieżki do pliku,
- działające `/health` i `/ready` API,
- znany serwer testowy i pierwszy serwer pilotażowy,
- aktualne `docs/STATUS.md`, `docs/PILOT_CHECKLIST.md` i `docs/CHANGELOG.md`.

## 10. Operator notes

Uzupełnij przed pilotem:

- ID serwera testowego:
- ID pierwszego serwera pilotażowego:
- ścieżka do ostatniego backupu Mongo:
- data ostatniego testu restore:
- osoba odpowiedzialna za rollout: