# Chronologiczny Changelog: Overtime Infrastructure

Wszystkie istotne zmiany i wydania w ramach ekosystemu **Overtime Multiverse** są dokumentowane w tym pliku.

---

## [1.1.6] - 2026-06-06 (Portal Interactive Discord Reactions)
### Dodano
* **Magistrala zdarzeń Redis Pub/Sub:** Zaimplementowano metodę `publish_pubsub` w klasie `RedisEventBus` w celu przesyłania powiadomień w czasie rzeczywistym z API do bota Discord.
* **Reakcje bota na akcje z Portalu:**
  - W FastAPI (CT 111) zintegrowano endpointy `POST /profile/cyber-drink` oraz `POST /profile/ice-breach` zabezpieczone JWT, wysyłające zdarzenia na kanał Pub/Sub `matka:discord_reactions`.
  - W bramce bota Matka (CT 117) dodano asynchroniczny nasłuch Pub/Sub w tle. Bot wysyła powiadomienia o postawieniu drinka i udanym włamaniu ICE globalnie na dedykowany kanał tekstowy oraz lokalnie bezpośrednio do czatu tekstowego kanału głosowego (Voice Channel Chat), w którym przebywa cel.
  - Zintegrowano przyciski "Postaw Drinka" (Cyber-Drinks) i logikę hakowania (ICE Infiltration) w `community.html` z nowymi endpointami API.
### Zsynchronizowano
* **Synchronizacja Workspace & Monorepo:** Pomyślnie zsynchronizowano pełną strukturę bota Matka w workspace `/home/rf/Dokumenty/00. PS- Matka` z katalogiem `/home/rf/Dokumenty/Overtime-Infrastructure/matka` oraz pliki `community.html` w workspace portalu i monorepo, a następnie wypchnięto zmiany na zdalne repozytoria GitHub.

---

## [1.1.5] - 2026-06-06 (Request-URI Too Large & JWT Optimization)
### Naprawiono
* **Optymalizacja Payloadu JWT:** Przefiltrowano listę serwerów w tokenie logowania OAuth2 (w `auth_routes.py`), zachowując wyłącznie te, na których użytkownik posiada uprawnienia administratora, zarządcy lub jest właścicielem (`owner`). Zapobiega to generowaniu gigantycznych tokenów JWT (powyżej 10 KB) dla użytkowników należących do wielu gildii.
* **Rozszerzenie Buforów Nginx (CT 104):** Zwiększono parametry `large_client_header_buffers` (do `4 32k`) oraz `client_header_buffer_size` (do `16k`) w konfiguracji głównego serwera proxy Nginx na CT 104, całkowicie eliminując błąd `414 Request-URI Too Large` przy przekierowaniach z tokenem JWT.

---

## [1.1.4] - 2026-06-06 (SSO & Portal Economy Integration)
### Dodano
* **Zunifikowane Logowanie SSO:** Zintegrowano Discord OAuth2 (SSO) z backendem FastAPI (CT 111) oraz frontendem portalu (`community.html`), przekazując cel przekierowania przez parametr `state` (`csrf_token|redirect_to`).
* **Ekonomia Live w Portalu:** Wdrożono pobieranie (`GET /api/matka/profile`) i synchronizację/zapis (`PUT /api/matka/profile`) kredytów, poziomów decku, poziomów zabezpieczeń i statystyk w MongoDB (z fallbackiem na `localStorage` w trybie offline).
* **Obsługa Zakupów w Sklepie:** Dodano endpoint `POST /shops/{guild_id}/buy` dla zakupów ze sklepu globalnego i lokalnego z integracją z inwentarzem użytkownika.
* **Serwowanie Portalu przez FastAPI:** Zintegrowano pliki statyczne portalu (HTML, JS, CSS) bezpośrednio z FastAPI w kontenerze `api-cockpit` (CT 111), montując je na root path `/` (odp. `/index.html`, `/community.html` itp.). Pozwala to na pełne logowanie i grę bezpośrednio w sieci lokalnej (przez Nginx na CT 104) bez konieczności zewnętrznego hostingu na Vercelu ani problemów Mixed Content.
* **Integracja API Portalu z FastAPI:** Przetłumaczono endpointy Node.js Express (`/api/usercount`, `/api/userstats`, `/api/changelogs`) na natywny kod Python/FastAPI w [portal_stats.py](file:///home/rf/Dokumenty/Overtime-Infrastructure/ojciec/api/app/routes/portal_stats.py). Pozwala to portalowi na dynamiczne pobieranie liczby użytkowników, rankingów serwerów (Among Us, DBD, Overwatch itd.), synergii głosowych i changelogów bezpośrednio z lokalnej bazy MongoDB bez uruchamiania oddzielnego procesu Node.js/PM2.
### Naprawiono
* **Dockerfile API Pathing Resolution:** Naprawiono kopiowanie pakietu `matka/matka` do `/app/matka/matka` w `Dockerfile.api` w celu poprawnego działania `sys.path`.
* **Przeniesienie docs w Dockerfile:** Poprawiono ścieżkę kopiowania folderu `docs` ze starego `ojciec/docs` na zunifikowany nadrzędny folder `docs/`.
* **Zależności Pythona w API:** Dodano brakujące zależności bota Matka (`arq`, `apscheduler`, `python-json-logger`, `python-jose`, `PyNaCl`) do obrazu Docker API w celu uniknięcia `ImportError` przy ładowaniu tras Matki.

---

## [1.1.3] - 2026-06-05 (Godmode Profile & Space Recovery)
### Dodano
* **Włączenie Profilu Produkcyjnego (OVERTIME_PROFILE=prod):** Ustawiono zmienną środowiskową na wszystkich kontenerach Ojca (CT 106–112) w celu umożliwienia poprawnego ładowania MongoDB dla Godmode.
* **Auto-reload w Godmode Edit Tokens:** Dodano automatyczne rozgłaszanie zdarzenia `config.reload` przez Redis Stream w modalnej edycji tokenów.
* **Komenda `restart-soft` we Flotowym Control Panelu:** Zaimplementowano polecenie `restart-soft` w `fleet_control.sh`, które wykonuje lekki restart kontenerów w locie (`docker compose restart`) bez czasochłonnego i obciążającego dysk przebudowywania obrazów Docker (`--build`).
### Naprawiono
* **CT 117 (matka-gateway) Disk Space Recovery:** Oczyszczono przeciążony cache i obrazy Docker na kontenerze CT 117, odzyskując **1.327 GB** miejsca i umożliwiając poprawny start bramki Matki.

---

## [1.1.2] - 2026-06-05 (Godmode Cockpit Fix)
### Naprawiono
* **Godmode View Child Limit:** Rozwiązano błąd `ValueError: maximum number of children exceeded (40)` w `ui/views/godmode_view.py`. Przeniesiono dynamiczne przyciski konfiguracji profili ("Kanały i Serwery", "Tokeny i Prefix", "Flagi i Tryby") do dedykowanego podwidoku `GodmodeProfileSettingsView` w celu obniżenia liczby kontrolek głównego panelu pod dopuszczalny limit discord.py (40 elementów).

---

## [1.1.1] - 2026-06-05 (Fleet Stabilization & Path Fixes)
### Dodano
* **Ojciec API Integration Safety:** Dodanie try-except na top-level importach w `ojciec/api/main.py` chroniących API przed awarią przy braku modułu `matka` w obrazie Docker.
### Naprawiono
* **Compose File Pathing:** Skrypt `fleet_control.sh` został zaktualizowany o parametr `-f` wskazujący na odpowiednie pliki compose (dla start, stop, status i logs). Zapobiega to błędom "no configuration file provided".
* **Environment Pathing Resolution:** Rozwiązano niedopasowanie ścieżek `.env`. Skopiowano pliki konfiguracyjne do `/app/ojciec/.env` oraz `/app/matka/.env` na odpowiednich kontenerach.
* **Fleet Duplication & Orphan Cleanup:** Wyczyszczono zduplikowane, osierocone kontenery na CT 118 i CT 117 w celu zwolnienia miejsca i uniknięcia konfliktów.

---

## [1.1.0] - 2026-06-05 (Multiverse Integration & Orchestration)
### Dodano
* **Consolidated Docker Orchestration:** Utworzenie głównego pliku `docker/docker-compose.yml` umożliwiającego lokalny rozruch pełnego ekosystemu (3 bazy danych, boty, workery, api cockpit i frontend).
* **Dynamic Configuration & Godmode Modals:** Wdrożenie loaderów profilu `OVERTIME_PROFILE` i rozbudowa Godmode Cockpitu o modals do edycji tokenów, kanałów i trybu pasywnego w MongoDB z hot-reloadingiem bez restartu usług.
* **Event Telemetry Bridge:** Połączenie `activity_service.py` (Ojciec) ze strumieniem Redis `matka:events` i pasywnym workerem (Matka) do naliczania XP/ekonomii.
* **Proxmox Disk Optimizations:** Dodanie automatycznego wywołania `docker system prune -af` w skrypcie `fleet_control.sh` chroniącego małe dyski (4 GB / 6 GB) przed zapchaniem.
* **Architecture Documentation:** Utworzenie oficjalnego opisu multiversum w `docs/ARCHITECTURE.md`.

---

## [1.0.0] - 2026-06-05 (Monorepo Inception)
### Dodano (Monorepo)
* **Monorepo structure:** Konsolidacja projektów **Ojciec (v5.7.02)**, **Matka (v1.0.0)** i **WWW OT POLSKA** pod wspólnym dachem w `/home/rf/Dokumenty/Overtime-Infrastructure`.
* **Zunifikowane repozytorium:** Inicjalizacja pojedynczego, nadrzędnego repozytorium Git ułatwiającego synchronizację wydań i współdzielenie kodu.
* **Unified Documentation:** Połączenie instrukcji, checklist i statusów w centralnym folderze `docs/` monorepo.

---

## [OJCIEC v5.7.02] - 2026-06-03 (Asynchroniczny Logger & Fleet Lockout)
### Dodano
* **Asynchroniczny Logger:** Przebudowa [logger.py](ojciec/core/logger.py) na asynchroniczny, nieblokujący zapis I/O za pomocą bezpiecznej kolejki wątkowej `queue.Queue`.
* **Izolacja Logów:** Logi serwerowe są rozdzielane do katalogów o nazwach opartych na Snowflake `guild_id` (`logs/guilds/{guild_id}/application.log`), zapobiegając kolizjom plików.
* **Zarządzanie kontami RabbitMQ:** Utworzenie użytkownika administracyjnego `ojciec` na centralnym serwerze RabbitMQ (CT 103) i usunięcie błędów autoryzacji w workerach.
* **Direct Fallback w interakcjach:** Executor Discord REST (CT 107) otrzymał Direct Fallback w `respond_to_interaction()`, pozwalający na bezpośrednie wywołania HTTP w przypadku awarii sieciowej executora.

---

## [MATKA v1.0.0] - 2025-11-25 (Gamification & Economy 2.0)
### Dodano
* **Quest Engine:** System zadań dziennych i tygodniowych generowanych automatycznie przez Schedulera.
* **Achievement System:** Obsługa odznak globalnych, lokalnych, ukrytych i niemożliwych za aktywność.
* **Context Menus (Commandless UX):** Kliknięcie prawym przyciskiem myszy na profil pozwala natychmiastowo wywołać akcję *Pokaż Profil* oraz *Daj Reputację (+1)*.
* **Dwuwarstwowa Ekonomia:** Portfele globalne (Overcoins - OC) oraz portfele lokalne z dynamicznymi kursami wymiany per-serwer.
* **Giełda Serwerów:** Inwestycje w akcje serwerów oparte o rzeczywistą telemetrię czatu i voice.

---

## [PORTAL v1.5.5] - 2026-05-30
### Dodano
* **Centralny Maszt Metropolis:** Wektorowy punkt centralny mapy Metropolis — „PRIMARY MAINFRAME”, spajający cały ruch i dynamicznie kalkulujący statusy linków.
* **Dynamiczne Światłowody Neonowe:** Nakładka wektorowa SVG rysująca krzywe Beziera łączące strefy biesiadne z centralnym mainframem z animacją strumienia świetlnego na 60 FPS.
* **Opancerzone Drony Patrolowe:** Dwa automatycznie krążące drony wojskowe ze skanującym laserem; kliknięcie drona nagradza gracza kredytami.
* **Boczna Konsola Infiltracji Hakerskiej:** Panel operacyjny `cyberTerminalSide` dla Netrunnerów pokazujący listę biesiadników na kanale, umożliwiający hackowanie ICE oraz stawianie drinków.
### Naprawiono
* **Błąd Usuwania Mainframe'u:** Zabezpieczono centralny maszt przed usunięciem przy odświeżaniu danych przez selektor `:not(.pulsing-core)`.

## [PORTAL v1.5.4] - 2026-05-30
### Dodano
* **Dynamiczne Stoły Kanałowe:** Algorytm grupuje użytkowników na tych samych kanałach i automatycznie generuje stoły na Infinite Canvas.
* **Trasy Trygonometryczne:** Rozstawianie stołów radialnie w celu wyeliminowania nakładania się pokoi.
* **Discord Profile Importer:** Import rzeczywistych awatarów i nazw użytkowników Discorda na mapach.
* **Dymki Aktywności:** Prezentacja aktualnej gry biesiadnika w chmurce dialogowej.
* **Instant-loading v2.0:** Usunięcie sleepa 1.2s przy starcie i przyspieszenie pollingu do 3.5s.

## [PORTAL v1.5.3] - 2026-05-30
### Dodano
* **Commandless Cyber-Shop:** Wizualny sklep Czarnego Rynku zintegrowany z kartą ekwipunku bez wpisywania komend.
* **Hex-Keypad Decoder:** Klikalna neonowa matryca numeryczno-szesnastkowa do dotykowego hackowania ICE na urządzeniach mobilnych.
* **Infinite Canvas Engine:** Sprzętowa akceleracja 3D CSS do płynnego Pan & Zoom (dotykowa karuzela na mobile).
* **Alert Anomalii Sieciowych:** Globalne anomalie (Blackwall Breach, Netwatch Raid) wymuszające interwencję deszyfrującą całej społeczności.

## [PORTAL v1.5.2] - 2026-05-29
### Naprawiono
* **Błąd calculateOrbit:** Przywrócono funkcję obliczania orbit biesiadników eliminującą ReferenceError.
* **Buttery Smooth Drag & Drop:** Dynamiczne wyłączenie tranzycji CSS na czas dragowania mapy w celu eliminacji lagów.

## [PORTAL v1.5.1] - 2026-05-21
### Naprawiono
* **Błąd parsera JS:** Usunięto osierocony fragment kodu `charObj.addEventListener`.
* **Defensive Economy Initialization:** Bezpieczne wczytywanie i sanityzacja danych z localStorage (zapobieganie NaN/TypeError).

## [PORTAL v1.5.0] - 2026-05-21
### Dodano
* **Wandering Patrol State Engine:** Płynny spacer 35% postaci z kołysaniem pixelBob i zmianą kierunku zwrotu.
* **Korporacyjna Dominacja:** Obliczanie dominacji megakorporacji (Arasaka, Militech, Biotech, Network LLC) na podstawie statystyk serwerów.
* **HVT (High-Value Target):** Oznaczanie graczy z najdłuższą sesją głosową jako cele do hakowania o wyższej nagrodzie.

## [PORTAL v1.4.0] - 2026-05-21
### Dodano
* **Cyberpunkowe Profile:** Wyświetlanie szczegółów postaci, statusów i deterministycznie generowanych cyber-wszczepów.
* **Mini-Gra Hackerska:** Retro terminal CLI do łamania firewalli ICE; sukces generuje toast lub dymek.

## [PORTAL v1.3.0] - 2026-05-21
### Dodano
* **Integracja z MongoDB Atlas:** Wczytywanie prawdziwych użytkowników z bazy bezpośrednio do Metropolis i Tawerny na podstawie `voice_users_detailed`.
* **Top Partnerzy i Sesje:** Integracja ze statystykami `voice_analytics` (TOP 5 synergii i TOP 5 sesji).

## [PORTAL v1.2.0] - 2026-05-21
### Dodano
* **Metryki Sieciowe:** Wyświetlanie liczby serwerów, shardów, pingów oraz odznak premium (boostów, verification badges).

## [PORTAL v1.1.0] - 2026-05-21
### Dodano
* **Changelog Bota:** Dedykowana podstrona changelog.html oraz przycisk w menu głównym.

## [PORTAL v1.0.0] - 2026-05-20 (User Count & Landing Page)
### Dodano
* **Strona Wejściowa:** Czysty, autorski interfejs landing page zintegrowany z biblioteką `particles.js`.
* **Licznik Sieciowy:** Integracja serverless z MongoDB Atlas do zliczania użytkowników głosowych oraz serwerów w czasie rzeczywistym.
* **PM2 Deployment:** Dodanie skryptów rozruchu i integracji z menedżerem procesów PM2 dla bezpiecznej pracy w tle.
