# Status prac OJCIEC 4.0

Ten plik jest roboczą checklistą postępu, żeby dało się szybko wrócić do projektu poza samym changelogiem.

## Nadrzędna agenda

Te zasady obowiązują niezależnie od bieżącego tasku:

- Maksymalnie wykorzystywać realny potencjał `discord.py 2.7.1`, ale tylko tam, gdzie daje to stabilny i czytelny zysk UX.
- Pchać repo w model **interaction-first / commandless**: panel, modal, select, button, trigger voice i dashboard przed dokładaniem kolejnych komend tekstowych.
- Jeśli komenda jest potrzebna, ma być głównie **hybrydowym openerem workflow**, a nie docelowym interfejsem sterowania.
- Prefixy zostają przede wszystkim dla ownera, recovery, debugowania, migracji i awaryjnych operacji operatorskich.
- Po każdym etapie aktualizujemy `docs/CHANGELOG.md`, `docs/STATUS.md`, listę todo i odpowiedni dokument w `rozwój/` lub `docs/WALKTHROUGH.md`.
- Po każdym etapie dopisujemy też do tego pliku krótką ocenę: **realny procent całkowitego postępu** oraz **szacunek pozostałych roboczogodzin**.

## Snapshot projektu — 2026-05-21

- Linia wersji GitHub: **`v.58`** w ramach **OJCIEC 4.0**; rewizje **`v.1` → `v.58`** opisują kolejne aktualizacje tej samej linii produktu.
- Checklista wykonania z tego pliku: **123/145 = 84.8%**.
- Realny procent realizacji względem docelowego produktu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 96%**.
- Aktualny snapshot surface'ów po cleanupie: **około 33 surface'y komendowe**, **38 surowych użyć `discord.Embed(...)`**, **4 użycia `discord.ui.LayoutView`**, **6 widoków persistent** oraz **2 bezpośrednie call-site'y `ctx.send(...)` / `ctx.reply(...)`**; interaction-first jest już mocne w Daddy Voice`s, dossier, LFG i LFM, a pozostałe dwa surowe call-site'y siedzą już tylko w bazowym helperze `core/interaction_responses.py` dla prefix fallbacku.
- Nowy baseline telemetryczny: bot ma już osobny strumień `activity_events` do Mongo oraz JSONL w `logs/system/activity.jsonl` i `logs/guilds/{guild_id}/activity.jsonl`; obejmuje wiadomości, edycje/usunięcia, reakcje, interakcje, zmiany profilu i ról członka, kanały, wątki, role, zaproszenia, join/leave/ban/unban członków, voice join/switch/leave/session oraz runtime `BOT_LOG`. Dodatkowo telemetry ma już politykę capture/redakcji/retencji, a API wystawia profile osoby, top współobecnych, top serwery, overview widgety i grupowe summary ról przez `/api/activity` oraz `/api/social`.
- Powód rozjazdu między checklistą a realnym procentem: feature'y i główne panele coraz częściej są już realnie używalne, ale nadal najdroższe rzeczy są jakościowe i operacyjne: końcowe E2E continuity po restartach, regression/smoke, ops oraz ostatni detail/mobile-first pass w głównych powierzchniach Discorda.

### Szacowany nakład pozostałej pracy

- **Screenshot parity + standard paneli premium:** `1–2h`
- **Manualne recovery E2E + ostatnie continuity edge cases:** `2–4h`
- **Manualne E2E + smoke/regression + poprawki po testach:** `2–5h`
- **Ops / pilot readiness (backup, restore, retencja, runbook, rollout):** `2–4h`
- **Dashboard backend foundation:** `21–27h`

**Razem do szerokiego targetu OJCIEC 4.0:** `28–42h`.

**Razem do dopięcia warstwy Discordowej do sensownego pilota bez pełnego dashboardu:** `1–4h`.

### Ocena po etapie — 2026-05-21 / Release v.58: filtry dossier, kontenerowe potwierdzenia i hotfixy logów

- Zrobiony etap: kartoteka operatora dostała własne filtry historii po typie akcji, serwerze i operatorze oraz wspólny, kontenerowy ekran potwierdzeń dla `ostrzeżenia`, `notatki`, `kontaktu` i `wezwania`; ten sam model działa już zarówno w panelu dossier, jak i w bezpośrednich fallbackach komendowych.
- Dodatkowo: ownerowy `sync` działa już poprawnie z Godmode w kontekście interaction-backed, a join-to-create Daddy Voice`s nie produkuje już fałszywego tracebacku `Target user is not connected to voice`, gdy użytkownik rozłączy się zanim bot wykona `move_to`.
- Audyt załączonego `errors.log`: obecny HEAD domyka wskazane wcześniej klasy błędów dla `sync`, `mass_remove_warn`, `setup`, `daily_stats`, DM fallbacku `ban`, payloadu panelu TempVC oraz race condition `move_to`; historyczne wpisy można traktować jako zamknięte po aktualnym stanie kodu.
- Walidacja etapu: `./.venv/bin/python -m compileall cogs/admin/sync.py cogs/temp_vc/events.py cogs/moderation/ban.py cogs/tools/setup.py cogs/stats/daily_stats.py ui/views/godmode_view.py cogs/moderation/warn.py ui/builders/terminal_embed.py` przeszło poprawnie, `get_errors` nie zgłasza błędów dla modułów z loga, a aktualny builder TempVC potwierdza wyłącznie dozwolone typy komponentów kontenera.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 96%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`28–42h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`1–4h`**.
- Ocena jakościowa: warstwa Discord/moderation jest już bardzo blisko końcówki, ale literalne „wszystko” nadal nie jest skończone dla całego 4.0, bo otwarte zostają manualne E2E, operacyjny rollout/pilot oraz pełniejsza warstwa dashboardowa.

### Ocena po etapie — 2026-05-21 / Pełny telemetry slice, retencja i widgety

- Zrobiony etap: telemetry nie jest już tylko logowaniem wiadomości i voice, ale pełniejszym systemem aktywności i zmian na serwerze; dochodzą eventy struktury guilda, kanałów, wątków, ról, zaproszeń oraz zmiany profilu i ról członka, a sam zapis respektuje już politykę capture, redakcję treści i retencję.
- Dodatkowo: dashboard ma już gotowy read-side nie tylko dla pojedynczej osoby, ale też dla overview guild/network, top użytkowników, top kanałów, top serwerów, kandydatów do watchlisty i grupowego summary ról; front nie musi już składać wszystkiego sam z surowego event streamu.
- Walidacja etapu: `./.venv/bin/python -m compileall config/loader.py services/activity_service.py core/bot.py cogs/tracking/activity_maintenance.py cogs/events/guild_events.py cogs/events/member_events.py api/app/activity_analytics.py api/app/routes/activity.py` przeszedł poprawnie.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 95%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`33–54h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`2–9h`**.
- Ocena jakościowa: to nadal nie daje literalnie każdego możliwego eventu Discorda, ale zamyka większość praktycznych braków względem 2.0 i daje fundament pod analizę osoby, grupy, serwera i całej sieci bez rozjazdu między capture layerem a dashboardem.

### Ocena po etapie — 2026-05-21 / Telemetry coverage + profil osoby / social graph

- Zrobiony etap: telemetry aktywności został poszerzony z samego backbone'u do bardziej kompletnego coverage serwera: dochodzą reakcje Discorda oraz lepsza klasyfikacja interakcji komponentowych, więc Daddy Voice`s i inne panele można liczyć nie tylko jako surowe `custom_id`, ale też jako sensowne feature scope'y i akcje.
- Dodatkowo: API potrafi już złożyć gotowy profil osoby na serwerze i w całej sieci admina, razem z metryką aktywności, top kanałami, top serwerami i Top 5 współobecnych osób na podstawie pokrywających się sesji voice; read-side social graphu nie jest już prostym, otwartym endpointem bez RBAC.
- Walidacja etapu: `./.venv/bin/python -m compileall services/activity_service.py cogs/events/message_events.py cogs/tracking/global_voice.py api/app/activity_analytics.py api/app/routes/activity.py api/app/routes/social.py api/app/database.py` przeszedł poprawnie, `get_errors` nie zgłasza błędów dla touched files, import API działa, helper `build_user_activity_profile(...)` policzył poprawny wynik na realnych danych z Mongo, a smoke boot bota wystartował poprawnie z nowymi listenerami telemetrycznymi.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 95%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`33–54h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`2–9h`**.
- Ocena jakościowa: to nadal nie oznacza pełnej retencji, pełnego coverage absolutnie każdego eventu Discorda ani finalnego dashboardu frontowego, ale zamyka dużą część praktycznej luki względem 2.0: 4.0 ma już nie tylko zapis aktywności, ale też pierwszy sensowny read-side pod analizę osoby, serwera i sieci.

### Ocena po etapie — 2026-05-21 / Daddy Voice`s restart safety, lobby guard i v4 embeds modernizacja

- Zrobiony etap: wdrożono zautomatyzowane czyszczenie pustych kanałów w pętli reconcile po restarcie bota (oparte w 100% o bazę danych i bezpieczną weryfikację `fetch_channel` z wyjątkiem `NotFound`, co pozwoliło na całkowite wyeliminowanie skanowania kategorii i wyłączenie procedury `_recover_untracked_channels`, zapewniając całkowite bezpieczeństwo stałych i ad-hoc tworzonych kanałów przez adminów), awaryjny natychmiastowy transfer własności przy braku właściciela na serwerze (najdłuższy staż / random) oraz twardą blokadę zmiany prywatności dla kanałów typu Lobby. Dodatkowo wszystkie drobne operacje kartotekowe (wezwania, kontakty, notatki i potwierdzenia) zostały w pełni zintegrowane z nowoczesnym szablonem embedów standardu **Cyber-Terminal v4**.
- Walidacja etapu: `./.venv/bin/python -m compileall .` przeszedł bez błędów. Smoke test z logowaniem bota i połączeniem z MongoDB powiódł się całkowicie.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 95%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`33–54h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`2–9h`**.
- Ocena jakościowa: system Daddy Voice's osiągnął pełne samozaciskanie i odporność na awarie procesu. Ponowne wysyłanie paneli na dół chatu oraz automatyczne transfery i oczyszczanie pustych pokoi całkowicie rozwiązują problem martwych kanałów po restartach.

### Ocena po etapie — 2026-05-21 / Telemetry backbone aktywności i runtime

- Zrobiony etap: 4.0 dostał wreszcie realny ślad aktywności użytkownika i runtime bota zamiast samego operacyjnego loggera procesu; wiadomości, edycje, usunięcia, interakcje Discorda, lifecycle członków, voice join/switch/leave/session oraz `BOT_LOG` są zapisywane strukturalnie do Mongo i równolegle do JSONL per system/per guild.
- Dodatkowo: API wystawia już overview globalny dla admina oraz szczegółowe/syntetyczne endpointy `/api/activity` per guild, więc przyszły dashboard nie musi parsować plain-textowych logów ani opierać się wyłącznie na wąskim `audit_events` i `voice_sessions`.
- Walidacja etapu: `./.venv/bin/python -m compileall services/activity_service.py core/logger.py core/bot.py cogs/events/message_events.py cogs/events/member_events.py cogs/tracking/global_voice.py api/app/database.py api/app/routes/activity.py api/main.py` przeszedł poprawnie, `get_errors` nie zgłasza błędów dla touched files, import `from api.main import app` działa, smoke boot `timeout 90s ./.venv/bin/python main.py` wystartował poprawnie, a na dysku pojawił się realny `logs/system/activity.jsonl` z runtime eventami.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 94%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`37–56h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`4–13h`**.
- Ocena jakościowa: to nie domyka jeszcze pełnego dashboardu ani polityki retencji/analizy logów, ale zamyka jeden z najważniejszych braków względem 2.0: 4.0 ma już wspólny, rozszerzalny telemetry backbone zamiast tylko kolorowych logów procesu i rozproszonych skrawków statystyk.

### Ocena po etapie — 2026-05-21 / Pilot runbook foundation

- Zrobiony etap: repo dostało wreszcie konkretny runbook operatorski dla pilota Discord bez dashboardu, z realnymi komendami start/stop, smoke po starcie, backupem Mongo, restore testem, zasadami dla Redis, rollbackiem i kolejnością rollout per guild.
- Dodatkowo: README i walkthrough wskazują już `docs/RUNBOOK.md` jako obowiązkowy dokument operacyjny przy zmianach backup/restore, rolloutu i incydentów; otwarty checkbox operacyjny nie jest już pustym hasłem bez miejsca wykonania.
- Walidacja etapu: runbook został oparty o realny `docker-compose.yml`, istniejący endpoint `api/app/routes/health.py` oraz aktualny lokalny smoke `timeout 90s ./.venv/bin/python main.py`, więc nie opisuje fikcyjnych usług ani nieistniejących probe'ów.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 94%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`37–56h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`4–13h`**.
- Ocena jakościowa: to nie zastępuje żywego E2E ani testu restore, ale domyka ważną lukę operacyjną przed pilotem i przesuwa końcówkę pracy jeszcze wyraźniej z "braku procedur" na "wykonanie checklisty i testów".

### Ocena po etapie — 2026-05-21 / Shared response cleanup dla fallbacków i Fortune

- Zrobiony etap: owner fallbacki `list_servers`, `leave_server`, `sync`, `sc` oraz preview `ye_ban`, a także user-facing `fortune_cookie`, przeszły na wspólne helpery odpowiedzi zamiast lokalnych `ctx.send(...)`; interaction-backed ścieżki odpalane z Godmode nie trzymają już osobnych mini-modeli odpowiedzi.
- Dodatkowo: po tej serii zmian surowe `ctx.send(...)` / `ctx.reply(...)` zostały wycięte z repo praktycznie do zera poza samym foundation helperem; w kodzie użytkowym zostały już tylko dwa wewnętrzne call-site'y w `core/interaction_responses.py` obsługujące prefix fallback.
- Walidacja etapu: `./.venv/bin/python -m compileall cogs/admin/server_mgmt.py cogs/admin/sync.py cogs/admin/cleanup.py cogs/fun/fortune_cookie.py cogs/moderation/mass_ban.py` przeszedł poprawnie, lokalne testy kontraktu potwierdziły edytowalną wiadomość dla `sync` oraz poprawny status/delete flow `cleanup`, a końcowy grep potwierdził spadek do `2` surowych call-site'ów `ctx.send(...)` / `ctx.reply(...)`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 93–94%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`38–58h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`5–14h`**.
- Ocena jakościowa: to nie robi nowego feature'u, ale mocno czyści warstwę wykonawczą i redukuje ryzyko drobnych rozjazdów między prefixem, Godmode i interaction-backed fallbackami; największy ciężar końcówki siedzi już niemal wyłącznie w manualnym E2E, pilot ops i końcowym polishu.

### Ocena po etapie — 2026-05-21 / Godmode warn adapter + summon public ping

- Zrobiony etap: sztuczny kontekst wykonywania komend z panelu Godmode dostał brakujący `channel`, więc ownerowe `remove_warn` i `mass_remove_warn` nie wywracają się już na `InteractionCommandContext` przy wejściu w ten sam shared runtime warnów co normalne komendy i dossier.
- Dodatkowo: publiczne `wezwanie` przestało klonować duży embed z logów na kanał moderacyjny; na kanale zostaje już tylko krótki ping z prośbą o sprawdzenie DM, a pełny, gęsty zapis nadal trafia do logów i confirmów operatorskich.
- Walidacja etapu: `./.venv/bin/python -m compileall ui/views/godmode_view.py ui/views/operator_dossier_view.py` przeszedł poprawnie, lokalny test kontraktu potwierdził obecność `ctx.channel` w adapterze Godmode oraz nowy minimalny format publicznego wezwania, a `get_errors` nie zgłasza błędów dla touched files.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 93%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`39–60h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`6–16h`**.
- Ocena jakościowa: to jest czyste domknięcie dwóch lokalnych rozjazdów runtime/UX, nie nowa fala funkcjonalna; nie zmienia szerokiego planu, ale usuwa kolejny crash ownerowego fallbacku i ucina zbędny hałas na kanale moderacyjnym.

### Ocena po etapie — 2026-05-21 / Godmode private fallback hardening

- Zrobiony etap: ownerowy opener `godmode` został dociągnięty do spójnego modelu prywatnych odpowiedzi; hybryda używa już wspólnego helpera prywatnej odpowiedzi, a prefixowy fallback nie wycieka już całym cockpittem na kanał, jeśli owner ma wyłączone DM.
- Dodatkowo: po błędzie DM owner dostaje już tylko zwykły komunikat o konieczności włączenia prywatnych wiadomości, bez publikowania widoku administracyjnego w guildzie; liczba bezpośrednich call-site'ów `ctx.send(...)` / `ctx.reply(...)` spadła przy okazji do `15`.
- Walidacja etapu: `./.venv/bin/python -m compileall cogs/admin/godmode.py` przeszedł poprawnie, `get_errors` nie zgłasza błędów dla touched file, a licznik surowych `ctx.send(...)` / `ctx.reply(...)` po zmianie spadł do `15`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 93%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`39–60h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`6–16h`**.
- Ocena jakościowa: to jest mały, ale właściwy fix bezpieczeństwa i prywatności owner fallbacku; nie skraca istotnie godzin do końca, ale czyści jeden z ostatnich przypadków, gdzie nowy cockpit operatorski mógł zachować się jak stary publiczny fallback.

### Ocena po etapie — 2026-05-21 / Restart-safe dossier report cards

- Zrobiony etap: karta dossier wysyłana na `report_channel_ids` przestała być pół-trwałym widokiem zależnym od RAM procesu; przyciski `Kartoteka`, `Ostrzeż`, `Notatka`, `Kontakt` i `Wezwanie` działają już jako `DynamicItem` oparte o `custom_id`, więc po restarcie bota nadal prowadzą do tego samego shared runtime moderacji zamiast zamierać po timeoutcie widoku.
- Dodatkowo: szybkie modale kart raportowych nie polegają już na starej instancji `ModerationDossierCardView`; target jest rozwiązywany leniwie z `guild cache` / `bot cache` / `fetch_user(...)`, a bootstrap bota rejestruje dynamiczne przyciski dossier już w `setup_hook`.
- Walidacja etapu: `./.venv/bin/python -m compileall ui/views/operator_dossier_view.py core/bot.py` przeszedł poprawnie, serializacja komponentów potwierdziła komplet `custom_id` dla wszystkich przycisków karty, `get_errors` nie zgłasza błędów dla touched files, a smoke boot `timeout 90s ./.venv/bin/python main.py` wystartował poprawnie z nową rejestracją UI.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 93%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`39–60h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`6–16h`**.
- Ocena jakościowa: to nie jest wielki feature produktowy, ale domyka widoczny i realny dług restart safety w pionie moderacji; największy otwarty ciężar siedzi już bardziej w ręcznym E2E, pilot ops i końcowym polishu cross-module niż w brakach samego runtime dossier.

### Ocena po etapie — 2026-05-21 / Stats backbone + dashboard comparison hooks

- Zrobiony etap: ownerowa komenda `statystyki`, hourly tracker i dzienne snapshoty korzystają już ze wspólnej logiki sieciowej, więc nie ma dalszego rozjazdu między tym, co widzi owner na Discordzie, co ląduje w Mongo i co będzie później czytał dashboard; poprawione zostało też źródło liczenia aktywności głosowej, które wcześniej opierało się na martwym `Guild.voice_states`.
- Dodatkowo: API dostało osobne endpointy `network/latest`, `network/range` i `network/live`, heartbeat bota zapisuje ostatni status do Redis, a stats backend ma już magazyn zewnętrznych snapshotów porównawczych, do którego można odkładać ręcznie zebrane dane np. z panelu deweloperskiego Discorda do późniejszego zestawienia w dashboardzie.
- Walidacja etapu: logika snapshotu została sprawdzona lokalnym testem na atrapach guild/channel przez `./.venv/bin/python`, zmienione moduły kompilują się poprawnie, `get_errors` nie zgłasza błędów dla touched files, a `git diff --check -- cogs/stats/daily_stats.py services/stats_service.py` pozostał czysty.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 92–93%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`40–62h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`7–18h`**.
- Ocena jakościowa: to jest mały etap produktowo, ale duży architektonicznie — porządkuje ostatni ważny pion danych przed dashboardem i przygotowuje sensowne miejsce pod późniejsze porównania zewnętrzne bez mieszania tego z core runtime bota.

### Ocena po etapie — 2026-05-20 / Godmode cockpit + szybkie akcje z kart raportowych

- Zrobiony etap: karta dossier w logach moderacyjnych przestała być reply i leci już jako osobna wiadomość pod raportem, a jej przyciski zostały przepięte z ogólnych skrótów na realne akcje `Ostrzeż`, `Notatka`, `Kontakt` i `Wezwanie` wykonywane w tym samym shared runtime co pełna kartoteka; równolegle dodany został owner-only panel `godmode`, który spina recovery/admin komendy w jeden komponentowy cockpit z modalami i przyciskami.
- Dodatkowo: `banlist`, `banupdate`, `ye_ban`, `gr`, `sr`, `sync`, `stats_range`, `zapros`, `leave_server`, `list_servers`, `remove_warn`, `mass_remove_warn` i `metrics` zostały schowane z normalnej powierzchni komend; hybrydy nie wystawiają już slashy, a entrypointy zostają tylko jako ukryty backend dla panelu Godmode, który ma już także własny pakiet warn-recovery i snapshot metryk.
- Walidacja etapu: `python -m compileall` dla dotkniętych modułów przeszedł poprawnie, importy modułów są czyste, `get_errors` nie zgłasza błędów, a smoke boot `timeout 90s ./.venv/bin/python main.py` wystartował poprawnie z nowym cogiem `Godmode`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 91–92%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`45–71h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`10–23h`**.
- Ocena jakościowa: to nie jest wielki nowy feature, tylko domknięcie dwóch ważnych powierzchni operatorskich — logi moderacyjne przestały mieć „martwe” przyciski, a owner dostał realny cockpit zamiast kolejnej paczki pamiętanych komend; największy otwarty ciężar pozostaje już bardziej w E2E i dalszym ujednolicaniu UI niż w brakującym sterowaniu.

### Ocena po etapie — 2026-05-20 / Dossier neon consistency + Daddy Voice`s reconcile runtime

- Zrobiony etap: `notatka`, `wezwanie` i `kontakt` w kartotece korzystają już ze spójnych, nowszych embedów, a confirm workflow pokazuje nowoczesne stany wykonania i anulowania zamiast gołych komunikatów tekstowych; równolegle Daddy Voice`s dostał runtimeowy sync właściwości kanału, pętlę reconcile, auto-odtwarzanie paneli oraz twarde wymuszenie publicznego lobby przy create, adopt i recovery.
- Walidacja etapu: pełny `python3 -m compileall .` przeszedł bez błędów, a smoke boot `timeout 60s python main.py` wstał poprawnie; w logu runtime odzyskał też istniejący Daddy Voice`s podczas reconcile po starcie.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 91%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`47–73h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`11–25h`**.
- Ocena jakościowa: to jest mocniejszy krok jakościowy niż zwykły polish — modernizuje ostatnie niespójne wejścia lekkiej moderacji i przesuwa Daddy Voice`s z „jest recovery” w stronę „sam się leczy po przerwaniu i restarcie”; największy otwarty ciężar siedzi już bardziej w ręcznym E2E i runbookach niż w samych brakach runtime.

### Ocena po etapie — 2026-05-20 / Pre-ban DM + MODCO visibility hardening

- Zrobiony etap: ban i ban MODCO zakładają teraz sprawę oraz wysyłają użytkownikowi prywatną wiadomość z referencją i planowanym zakresem jeszcze przed fizycznym wykonaniem bana, a dopiero potem aktualizują rekord o realny wynik fan-outu; równolegle `Ban MODCO` został zawężony do samej grupy `modco` i zniknął z warstwy slashowej, więc niższe role nie powinny go już widzieć.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 90%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`49–75h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`13–27h`**.
- Ocena jakościowa: to jest czysta poprawka operatorska i deliverability — nie zmienia polityki sankcji, ale domyka ważny błąd UX/runtime, w którym pełny ban odcinał użytkownika od jedynej wiadomości wyjaśniającej decyzję; dług główny dalej siedzi w manualnym E2E i pilot ops.

### Ocena po etapie — 2026-05-20 / Dossier opener po ID + confirm hardening

- Zrobiony etap: panel operatora pozwala już otworzyć kartotekę po dowolnym ID użytkownika pobranym z API Discorda, także jeśli celu nie ma na aktualnym serwerze, a confirm view ciężkich akcji przechwytuje nieoczekiwane wyjątki i zwraca operatorowi kontrolowany komunikat zamiast zostawiać sam traceback w logu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 90%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`50–76h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`14–28h`**.
- Ocena jakościowa: to jest mały, ale bardzo praktyczny slice operatorski — zamyka realny brak workflow dla targetów spoza guilda i ogranicza ślepe wywrotki confirm view; największy ciężar dalej siedzi w E2E i pilot ops, nie w samym panelu kartoteki.

### Ocena po etapie — 2026-05-20 / Moderation guard datetime normalization

- Zrobiony etap: rate-limit moderacyjny przestał zakładać, że wszystkie timestampy w `moderation_policy.events` są UTC-aware; odczyt starego Mongo z naiwnymi albo tekstowymi datetime nie wywraca już confirm workflow `ban` / `unban` na odejmowaniu `offset-naive` vs `offset-aware`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 90%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`51–77h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`15–29h`**.
- Ocena jakościowa: to jest czysty fix stabilnościowy warstwy enforcement, nie nowy feature; otwarty ciężar dalej siedzi w manualnym E2E i domknięciu pilotowego recovery, a nie w samym runtime confirmów.

### Ocena po etapie — 2026-05-20 / Board guardrails + rate limiter + orphan recovery

- Zrobiony etap: shared runtime dossier i ciężkiej moderacji respektuje już centralny wyjątek `Board Equivalent` oraz rolę `Board/Emperor`, wspólny `RateLimiter` nie ma już wyścigu współbieżnego przy `acquire()`, a startup recovery Daddy Voice`s potrafi adoptować więcej niż jeden orphan dla tego samego triggera.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 90%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`52–78h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`16–30h`**.
- Ocena jakościowa: to nie są nowe feature'y, tylko spłata trzech realnych luk runtime z audytu; największy otwarty ciężar przesuwa się jeszcze wyraźniej z core security/stability na manualne E2E, ops i finalny pass produktu.

### Ocena po etapie — 2026-05-14 / O użytkowniku, etap 1

- Zrobiony etap: `ch_status` stał się prywatnym openerem panelu operatora z podglądem kartoteki oraz akcjami `warn`, `notatka`, `wezwanie`; dopisany został też odczyt spraw z `mod_cases`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready**: **około 67%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`126–198h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`49–78h`**.
- Ocena jakościowa: to jest pierwszy realny krok w pionie `warn + dossier + ch_status`, ale stare komendy nadal istnieją jako fallback i pełna migracja kartoteki nie jest jeszcze zakończona.

### Ocena po etapie — 2026-05-14 / O użytkowniku, etap 2

- Zrobiony etap: `warn`, `warnings`, `notatka` i `wezwanie` zostały przepięte na wspólne helpery workflow kartoteki; brak treści w komendzie otwiera teraz panel zamiast odpalać osobny, legacy flow.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready**: **około 69%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`121–190h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`45–74h`**.
- Ocena jakościowa: pion kartoteki ma już jeden wspólny runtime dla lekkiej moderacji, ale nadal brakuje context menu i pełnego przeniesienia cięższych akcji (`ban/Unban/przerwa`) do tego samego standardu.

### Ocena po etapie — 2026-05-14 / O użytkowniku, etap 3

- Zrobiony etap: panel kartoteki dostał context menu użytkownika, grupowane ciężkie akcje (`ban`, `Unban`, `przerwa`, `zdejmij przerwę`) oraz zarządzanie warnami (`edycja`, `archiwizacja`, `masowa archiwizacja`), a dostęp do akcji opiera się już na rozbitej macierzy ról i profilu zabezpieczeń celu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready**: **około 72%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`112–180h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`38–68h`**.
- Ocena jakościowa: lekka moderacja i pierwsza warstwa cięższej moderacji są już zebrane w jeden runtime, a panel pokazuje też zabezpieczenia celu; nadal do domknięcia zostaje dalsza redukcja starych komend ciężkiej moderacji i pełniejsze, wielowymiarowe modele uprawnień.

### Ocena po etapie — 2026-05-14 / Spójność działań moderacyjnych

- Zrobiony etap: perm dostał osobny audit trail, liczniki działań oraz `previous ban` uwzględniają już `modco_ban`, `b_ban`, `autoban`, `dashboard_ban` i `mass_ban`, a Temp VC rozdziela teraz triggery normalne od triggerów głosowania.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready**: **około 73%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`108–176h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`36–64h`**.
- Ocena jakościowa: warstwa „działań” jest znacznie spójniejsza technicznie, ale nadal nie wszystkie ciężkie akcje mają docelowe wejście commandless z jednego panelu i nadal brakuje pełnego manualnego E2E.

### Ocena po etapie — 2026-05-14 / Bezpieczny workflow b_ban

- Zrobiony etap: `b` dostało jawne potwierdzenie przed wykonaniem, dodatkowe DM-notyfikacje do ownera bota i Kanclerza ds. Bezpieczeństwa i Administracji, realny guard odwracający ręczny unban przy aktywnym `b_ban`, owner-only toggle perma pod tą samą komendą `b` oraz owner-only akcję `Nadaj Perm` / `Zdejmij Perm` bezpośrednio w dossier operatora przed właściwym `unban`.
- Dodatkowo: `ban` i `unban` bez powodu przestały wykonywać od razu legacy flow i otwierają teraz dossier operatora jako wspólny commandless opener.
- Dodatkowo: `przerwa` i `zprzerwy` przy niepełnym wejściu również otwierają już dossier operatora zamiast osobnego legacy flow.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready**: **około 81%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`82–142h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`22–40h`**.
- Ocena jakościowa: ciężka moderacja jest już bardzo blisko jednolitego runtime commandless, ale nadal przyda się ręczne E2E dla scenariuszy `perm -> unban`, manual Discord unban, openerów dossier z komend `ban` / `unban` / `przerwa` / `zprzerwy` oraz DM-notyfikacji przy niepełnych cache członków.

### Ocena po etapie — 2026-05-15 / Daddy Voice`s parity + recovery audit

- Zrobiony etap: panel Daddy Voice`s dostał blok `STATUS KANAŁU`, pola `GRAMY W` / `KOD DO GRY`, commandless zatwierdzanie/odrzucanie próśb o dostęp i akcję `Pozwól wejść ponownie` dla zbanowanych kanałowo; powierzchnia panelowa audio mute/restore została celowo usunięta.
- Dodatkowo: recovery dostał pierwszy praktyczny slice ponad samą rehydratację z Mongo — join handler i startup potrafią odzyskać istniejący Daddy Voice`s po domyślnej nazwie kanału i obecności właściciela, zamiast od razu produkować duplikat.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 77%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`84–122h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`44–68h`**.
- Ocena jakościowa: parity względem screenshotów jest już blisko używalnego produktu, a recovery przestał być wyłącznie zależny od jednego rekordu w Mongo, ale rdzeń awaryjny nadal jest niepełny: pełna continuity właściciela, rename-safe adopcja kanału i aktywny voice tracker poza RAM wymagają kolejnych iteracji.

### Ocena po etapie — 2026-05-15 / Daddy Voice`s continuity foundation

- Zrobiony etap: recovery Daddy Voice`s przestał zależeć wyłącznie od nazwy domyślnej i samego rekordu sesji; właściciel ma już trwały marker w overwrite kanału, a Temp VC zapisuje w Mongo aktualny skład kanału, kolejność obecności i ostatni moment widzianego właściciela.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 78%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`80–118h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`40–64h`**.
- Ocena jakościowa: fundament continuity jest już gotowy pod realną politykę nieobecności właściciela, ale nadal trzeba dopiąć samą decyzję runtime: kiedy przekazujemy kanał najdłużej obecnemu, a kiedy sprzątamy go po progu nieobecności.

### Ocena po etapie — 2026-05-15 / Owner absence + persistent voice trackers

- Zrobiony etap: Daddy Voice`s dostał konfigurowalną politykę nieobecności właściciela (`transfer`, `cleanup`, `off`) z timerem opartym o trwały snapshot obecności, a oba voice trackery (`global_voice`, `voice_tracker`) zapisują aktywne sesje w Mongo i rehydrują je po restarcie procesu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 80%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`76–110h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`36–56h`**.
- Ocena jakościowa: największa luka continuity po awarii została domknięta na poziomie kodu; na stole zostały głównie manualne testy restartowe, ostatnie edge case’y recovery oraz finalny polish UI względem referencji.

### Ocena po etapie — 2026-05-15 / Trigger type runtime toggles

- Zrobiony etap: setup Daddy Voice`s dostał przełączniki typu normalnego i głosującego bez kasowania list triggerów, join-to-create i recovery respektują teraz aktywny typ runtime, a vote kick/ban jest rozstrzygany po sesji kanału zamiast po jednym guild-wide switchu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 81%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`75–109h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`35–55h`**.
- Ocena jakościowa: technicznie zamknięty został ostatni brakujący kawałek runtimeowej kontroli nad typami pokoi; największa wartość z kolejnego kroku będzie już płynęła z ręcznych testów E2E i mobile/premium polish, a nie z kolejnych podstawowych feature’ów Temp VC.

### Ocena po etapie — 2026-05-15 / Audio abuse guard dla Daddy Voice`s

- Zrobiony etap: owner marker recovery nie daje już natywnego `manage_channels`, a ręczny `server mute/deafen` na aktywnym Daddy Voice`s jest cofany przez guard moderacyjny z tym samym policy strike flow, który repo stosuje już dla ręcznych banów i timeoutów.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 82%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`74–108h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`34–54h`**.
- Ocena jakościowa: ważna luka bezpieczeństwa Daddy Voice`s została zamknięta u źródła i na warstwie enforcement; największe ryzyko tego pionu przesuwa się teraz z nadużyć właściciela na zwykłe E2E restartowe i finalny polish UI.

### Ocena po etapie — 2026-05-15 / Zarządowy wyjątek operacyjny

- Zrobiony etap: ID `1127523682164690966` jest traktowane centralnie jak zarząd w helperach uprawnień i ochrony, a wejścia `ban / unban / przerwa / warn / kontakt / notatka / wezwanie / ch_status` korzystają już z jednej warstwy checków zamiast z rozproszonych porównań ról.
- Dodatkowo: naprawiony został uszkodzony blok `on_ready` w `core/bot.py`, który przestał się parsować po błędnym wklejeniu surowego ID do kodu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 82%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`74–108h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`34–54h`**.
- Ocena jakościowa: wyjątek operatorski został domknięty u źródła, więc kolejne ścieżki moderacyjne nie powinny już wymagać ręcznego dopisywania tego samego ID; największe ryzyko pozostaje już w ręcznych E2E i polishu, a nie w samym modelu uprawnień.

### Ocena po etapie — 2026-05-15 / Temp VC stability hotfixes

- Zrobiony etap: panel Daddy Voice`s potwierdza dłuższe interakcje przed odświeżeniem widoku, a odczyt sesji Temp VC normalizuje datetime z Mongo do UTC-aware zanim owner-absence zacznie liczyć elapsed time.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 82%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`74–108h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`34–54h`**.
- Ocena jakościowa: to nie jest nowy feature, tylko domknięcie dwóch runtime edge case’ów, które wychodziły dopiero na żywym serwerze; główne ryzyko pozostaje już w manualnym E2E i dalszym polishu, a nie w bazowej logice owner-absence.

### Ocena po etapie — 2026-05-15 / Voice tracking UTC hotfix

- Zrobiony etap: `active_voice_sessions` dla `global_voice` i `voice_tracker` przechodzą przez normalizację datetime przy odczycie z Mongo, więc rehydratowane sesje nie wywracają już późniejszego odejmowania `datetime.now(UTC) - start_time`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 82%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`74–108h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`34–54h`**.
- Ocena jakościowa: to jest porządny fix warstwy persistence, nie miejscowa łatka w jednym cogu; największe niezamknięte ryzyko nadal siedzi w manualnym E2E i dalszym polishu, a nie w samym modelu czasu aktywnych sesji.

### Ocena po etapie — 2026-05-15 / Pakiet feedbacku użytkowników dla Daddy Voice`s i LFG

- Zrobiony etap: LFG zamyka się już razem z kanałem, przechodzi na nowego ownera Daddy Voice`s z DM-notyfikacją, blokuje linki w treści, wspiera predefiniowane tagi oraz gry z bannerem i nie gubi już globalnego listenera przez startup race z Redis; Daddy Voice`s dostał per-trigger kategorie i template nazw, domyślnie publiczny start kanału z możliwością świadomego przełączenia na prywatny tryb `Poproś o dostęp`, debounce auto-refresh panelu oraz widoczne ostatnie logi kanału bezpośrednio w UI. Nowe pola konfiguracyjne obu modułów są też dostępne z panelu Setup, bez schodzenia do YAML.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 84%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`68–100h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`30–48h`**.
- Ocena jakościowa: to jest duży pakiet jakościowy, który zamyka sporą część realnych uwag użytkowników bez rozwalania architektury; po stronie kodu największe ryzyko zostało już przesunięte z oczywistych bugów runtime na ręczne E2E globalnego LFG i całego Temp VC.

### Ocena po etapie — 2026-05-15 / Hotfixy DM bana i modalów setupu

- Zrobiony etap: `ban` przestał wywracać się przy uruchomieniu z DM, a nowe modale setupu dla LFG oraz Daddy Voice`s respektują już limit długości etykiet Discorda i nie wpadają w `50035 Invalid Form Body`.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 84%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`68–100h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`30–48h`**.
- Ocena jakościowa: to są ważne hotfixy stabilności i ergonomii, ale nie zmieniają jeszcze makro-obrazu projektu; nadal największy ciężar siedzi w E2E, polishu i operacyjnej gotowości pilota.

## Co to oznacza praktycznie

Największy dług projektu nie leży już w brakujących feature'ach, tylko w tych obszarach:

- zbyt duża liczba powierzchni komendowych względem modelu commandless,
- zbyt wiele zwykłych embedów i mało paneli interaction-first,
- nadal brak pełnego wspólnego standardu dla wszystkich głównych ekranów,
- zbyt mało testów i ręcznych checklist pilotażowych względem poziomu złożoności systemu.

## Czy wszystko jest już odprymitywnione?

Nie.

Na dziś odprymitywnione są najmocniej te piony, które przeszły już pełny loop: wspólny runtime LFG, Daddy Voice`s z recovery i continuity, operator dossier oraz setup jako realny runtime surface.

Nadal prymitywne albo pół-prymitywne pozostają:

- część owner/admin fallbacków i legacy command surface'ów, których wciąż jest około `32`,
- spora część zwykłych embedów poza głównymi flow (`52` surowe użycia `discord.Embed(...)`),
- brak jednego naprawdę twardego standardu premium UI dla wszystkich ekranów, nie tylko dla Terminala,
- ręczne E2E i operacyjna warstwa pilota, które są wciąż bardziej backlogiem niż zamkniętym procesem,
- część setupu ma już pełną parity runtime, ale nadal wymaga twardego ręcznego E2E zamiast zakładania, że sam zapis z UI oznacza gotowość pilota.

Wniosek: produkt przestał być prostym zlepkiem komend i lokalnych hacków, ale nie można jeszcze uczciwie powiedzieć, że cały repo został odprymitywniony. Rdzeń jest już dużo dojrzalszy niż na starcie sprintu, natomiast ogony legacy, testowe i operacyjne nadal ważą zauważalnie.

## Nowy realny plan działania

### Fala A — ostatnie długi commandless i stabilnościowe (`6–10h`)

1. Zamknąć ostatnie wolniejsze ścieżki interaction-backed na wzorzec `defer + shared response helper`, zaczynając od pozostałych paneli i owner/admin fallbacków.
2. Nie otwierać nowych szerokich feature'ów, dopóki stability pass nie będzie spójny z resztą repo.
3. Trzymać owner fallbacki jako ścieżki recovery i operacji awaryjnych, a nie jako konkurencyjny interfejs sterowania.

### Fala B — pełne E2E pilota Discord (`10–16h`)

1. Przejść ręczne scenariusze z [docs/PILOT_CHECKLIST.md](PILOT_CHECKLIST.md) dla Setup, Daddy Voice`s, recovery po restarcie, LFG lokalnego, LFG globalnego, LFM, Info Panelu i owner/admin fallbacków.
2. Naprawić wyłącznie to, co wyjdzie z E2E: restart safety, recovery, Redis/LFG network, DM/interaction pathy i edge case'y konfiguracji.
3. Utrzymać zasadę: zero nowej szerokiej funkcjonalności, dopóki pilot Discord nie przejdzie bez ręcznego ratowania.

### Fala C — finalny premium polish cross-module (`6–10h`)

1. Dociągnąć detail/mobile-first pass dla LFG, LFM i Info Panelu, a Setup domknąć już tylko drobnym passsem szczegółów.
2. Ujednolicić język wizualny głównych ekranów zamiast dalej rozbudowywać liczbę kontrolek i losowych bloków tekstowych.
3. Zostawić `LayoutView` tam, gdzie daje realny zysk, a resztę paneli budować według jednego standardu premium i interaction-first.

### Fala D — pilot ops i twardnienie operacyjne (`8–12h`)

1. Spisać runbook, backup/restore i retencję danych pod pierwszy pilot.
2. Dodać minimalny smoke/regression dla krytycznych flow bota i API.
3. Ustalić serwer testowy, serwer pilotażowy i kolejność rollout per guild.

### Fala E — dashboard backend dopiero po pilocie Discord (`36–48h`)

1. Scope/RBAC, cursor+limit, cache invalidation, indeksy i kontrakty OpenAPI.
2. Async export i pełniejszy audit dashboardu.
3. Dopiero po stabilizacji backendu myśleć o szerszym froncie dashboardowym.

## Priorytet na najbliższy ruch

Jeśli celem jest realny postęp, a nie tylko dokładanie kolejnych bajerów, następny sprint powinien wyglądać tak:

1. Manualne E2E całego pilota Discord.
2. Poprawki po E2E.
3. Finalny detail/mobile-first pass UI.
4. Następnie pilot ops.
5. Dopiero potem backend dashboardu.

### Ocena po etapie — 2026-05-16 / Setup takeover roles parity

- Zrobiony etap: setup Daddy Voice`s pozwala już edytować `takeover_role_ids` z Discord UI przez osobny podwidok `RoleSelect`, zamiast tylko pokazywać zapisany stan w embeddzie modułu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 85%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`66–98h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`28–46h`**.
- Ocena jakościowa: to zamyka ostatnią oczywistą lukę setup/runtime w Daddy Voice`s; od tego momentu największy zwrot daje już ręczne E2E i poprawki po żywych scenariuszach, nie kolejne drobne feature'y panelu.

### Ocena po etapie — 2026-05-16 / Kontakt workflow parity

- Zrobiony etap: `kontakt` został wciągnięty do tego samego workflow operatora co `notatka` i `wezwanie`; panel dostał brakującą akcję `Kontakt`, a komenda bez treści otwiera już kartotekę zamiast utrzymywać osobny legacy flow.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 85%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`65–97h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`27–45h`**.
- Ocena jakościowa: lekki pion operatora jest już znacznie spójniejszy i mniej prymitywny; następne największe cięcia powinny iść w cięższą moderację (`ban` / `Unban` / `przerwa`) albo w dalszą redukcję surowych owner/admin fallbacków.

### Ocena po etapie — 2026-05-16 / Unban i Przerwa na wspólnym runtime

- Zrobiony etap: fallbackowe komendy `unban`, `przerwa` i `zprzerwy` korzystają już z tych samych helperów co panel operatora, więc cięższa moderacja ma mniej duplikacji i mniej rozjazdów między komendą a workflow kartoteki.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 85%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`64–96h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`26–44h`**.
- Ocena jakościowa: to nie zamyka jeszcze całej ciężkiej moderacji, ale wycina sporą część technicznego długu; najbardziej prymitywnym dublem w tym pionie zostaje teraz zwykły `ban` / `modco` i owner-only `ye_ban`.

### Ocena po etapie — 2026-05-16 / Setup hardening + premium polish Daddy Voice`s

- Zrobiony etap: `/setup` deferuje odpowiedź przed budową panelu i korzysta ze wspólnej ścieżki response/followup, a główny panel Daddy Voice`s dostał bogatszą hierarchię wizualną zamiast płaskiego, technicznego układu.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 86%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`60–90h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`24–42h`**.
- Ocena jakościowa: najdroższy dług tej chwili nie leży już w samym wyglądzie Daddy Voice`s ani w podstawowym setupie, tylko w ostatnich legacy moderation fallbackach, ręcznym E2E i rozciągnięciu standardu premium UI na resztę głównych paneli.

### Ocena po etapie — 2026-05-16 / Compact polish, bitrate i lobby guard dla Daddy Voice`s

- Zrobiony etap: panel Daddy Voice`s dostał bardziej kompaktowy układ bliższy docelowemu konceptowi, runtimeową zmianę bitrate oraz twardą zasadę produktową, że kanały lobby / voting nie pokazują ręcznej zmiany prywatności.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 86%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`59–89h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`23–41h`**.
- Ocena jakościowa: Daddy Voice`s jest już bliżej dojrzałego produktu niż technicznego panelu roboczego; największe otwarte ryzyka zostały przesunięte z samego widoku na ciężką moderację, ręczne E2E i spójny premium pass w pozostałych głównych modułach.

### Ocena po etapie — 2026-05-20 / Ban MODCO na wspólnym runtime + Setup Runtime cockpit

- Zrobiony etap: `ban` i `modco` korzystają już z jednego wspólnego runtime bana w pionie operatora, dossier dostało brakującą akcję `Ban MODCO`, a główny ekran Setup przestał być płaskim dumpem modułów i stał się gęstszym cockpittem runtime.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 87%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`57–87h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`21–39h`**.
- Ocena jakościowa: największy prymitywny dubel ciężkiej moderacji zniknął, a Setup przestał odstawać tak mocno od standardu Daddy Voice`s; główne otwarte ryzyka siedzą teraz w `ye_ban`, ręcznym E2E i dociągnięciu LFG/LFM/Info do tego samego poziomu.

### Ocena po etapie — 2026-05-20 / ye_ban safe fallback + premium polish LFG/LFM/Info

- Zrobiony etap: `ye_ban` przestał być gołym masowym banem i działa teraz jako owner-only fallback z preview, confirm, deduplikacją kandydatów i osobnym audytem `EVT_MASS_BAN`; równolegle LFG, LFM i Info Panel dostały gęstszy, bardziej produktowy baseline wizualny, a `/info-panel` został utwardzony na wspólne helpery odpowiedzi.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 88%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`56–84h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`20–36h`**.
- Ocena jakościowa: najgroźniejszy owner fallback został wreszcie przycięty do bezpiecznej ścieżki recovery, a główne powierzchnie Discorda mają już wspólniej brzmiący język produktu; otwarty został już głównie manualny E2E, restart safety i finalny detail pass, nie brak podstawowych feature'ów.

### Ocena po etapie — 2026-05-20 / Interaction hardening dla LFM, ban export i statystyk

- Zrobiony etap: opener `lfm`, ownerowe `banlist` / `banupdate` oraz hybrydy `statystyki` / `stats_range` zostały dociągnięte do wspólnego modelu odpowiedzi interaction-backed; wolniejsze ścieżki deferują, a `banupdate` przestał mieć martwy błąd z numerem sprawy.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 88%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`55–83h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`19–35h`**.
- Ocena jakościowa: interaction debt w owner/admin fallbackach i pobocznych hybrydach jest już wyraźnie mniejszy; największe ryzyko nie leży teraz w podstawowej mechanice odpowiedzi, tylko w ręcznym E2E i końcowym mobile/detail passie.

### Ocena po etapie — 2026-05-20 / Fortune cookie bez twardego runtime hardcode

- Zrobiony etap: `fortune_cookie` przestało opierać gating działania wyłącznie na stałych z `config/settings.py`; runtime potrafi teraz scalić globalną konfigurację z dokumentu Fortune oraz override per guild z `guild_settings`, więc kanały, cooldown i role exempt da się zmieniać bez dalszego twardego kodowania.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 88%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`55–83h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`19–35h`**.
- Ocena jakościowa: społecznościowy moduł `wr` przestał odstawać architektonicznie od reszty runtime'ów, ale nadal nie ma jeszcze setup panelu, telemetryki claimów ani bogatszego commandless wejścia.

### Ocena po etapie — 2026-05-20 / Ostatni raw send wycięty z hybridowej ciężkiej moderacji

- Zrobiony etap: ownerowy `b` przestał mieć dwa końcowe confirmy oparte na gołym `ctx.send`; hybrydowa ścieżka `Perm`/`Zdejmij Perm` kończy się już tym samym interaction-safe helperem co reszta nowego runtime moderacji.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 88%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`55–83h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`19–35h`**.
- Ocena jakościowa: ciężka moderacja jest już praktycznie domknięta pod kątem prymitywnego interaction debt; otwarte pozostają głównie E2E, restart safety i końcowy pass produktu, a nie surowe ścieżki odpowiedzi.

### Ocena po etapie — 2026-05-20 / Fortune w Setupie + detail pass Setup/Info

- Zrobiony etap: `fortune_cookie` dostało już nie tylko runtime override, ale pełny moduł Setup z kanałami, cooldownem, rolami exempt, resetem do profilu globalnego i telemetryką claimów; równolegle główny cockpit Setup oraz Info Panel dostały gęstszy, bardziej mobile-first detail pass.
- Realny procent całkowitego postępu względem targetu **premium + commandless + pilot-ready + crash-safe voice continuity**: **około 89%**.
- Szacowany pozostały nakład do szerokiego targetu OJCIEC 4.0: **`54–82h`**.
- Szacowany pozostały nakład do sensownego pilota warstwy Discord bez pełnego dashboardu: **`18–34h`**.
- Ocena jakościowa: Fortune przestało być już architektonicznym wyjątkiem na obrzeżach projektu, a Setup/Info wyglądają bardziej jak produktowe wejście do ekosystemu niż techniczny panel roboczy; największe otwarte ryzyko to nadal ręczne E2E i recovery, nie brakujące runtime foundations.

## Zrobione

- [x] Ustalony nadrzędny plan architektoniczny OJCIEC 4.0 w [rozwój/10-masterplan-commandless-roadmap.md](../rozwój/10-masterplan-commandless-roadmap.md)
- [x] Uporządkowany backlog faz w [rozwój/05-fazy-wdrozenia-backlog.md](../rozwój/05-fazy-wdrozenia-backlog.md)
- [x] Dodany root [README.md](../README.md)
- [x] Dodany [docs/CHANGELOG.md](CHANGELOG.md)
- [x] Dodany [docs/WALKTHROUGH.md](WALKTHROUGH.md)
- [x] Dodany foundation interakcji w `core/interaction_responses.py`
- [x] Dodany foundation rehydracji persistent views w `core/interaction_registry.py`
- [x] Usunięty martwy wpis `cogs.events.error_handler` z bootstrapa bota
- [x] Naprawione główne błędy `ctx.send(..., ephemeral=True)` w aktywnych hybrydach
- [x] Dodana trwała normalizacja configu pod `features`, `temp_vc` i `lfg`
- [x] Dodany accessor bazy `temp_voice_channels`
- [x] Dodany foundation serwisu `services/temp_vc_service.py`
- [x] Dodany minimalny cog `cogs/temp_vc/events.py` z join-to-create i cleanupem pustych kanałów
- [x] Temp VC podpięty do bootstrapa bota
- [x] Dodany minimalny embed Terminala i persistent view
- [x] Dodana rehydracja paneli Terminala po restarcie
- [x] Zmiana limitu użytkowników z modalu w Terminalu
- [x] Transfer właścicielstwa przez UserSelect w Terminalu
- [x] Zmiana nazwy kanału z modalu w Terminalu
- [x] Stub przycisku LFG w panelu (disabled, placeholder Fazy 4)
- [x] Reconcile sierot przy starcie (sesje bez kanału lub bez panelu)
- [x] Dodany `config.yaml.example` z kompletną sekcją Temp VC
- [x] Faza 2.2 — Redis lock `tempvc:create:{guild}:{user}` w join-to-create (NX EX 10s)
- [x] Funkcje list w serwisie: `add/remove_allowed_user`, `add/remove_banned_user`, `add/accept/reject_access_request`
- [x] Faza 3.3 — Kick użytkownika z VC przez UserSelect (Row 2 Terminala)
- [x] Embed pokazuje podsumowanie list dostępu (uprawnieni / zbanowani / prośby)
- [x] Faza 3.2a — AllowSelect (Row 3): nadawanie dostępu + Discord `connect/speak` overwrite sync
- [x] Faza 3.2b — BanSelect (Row 4): ban + Discord overwrite sync + kick z kanału
- [x] Serwis: `grant_access` (atomowe: allow + clear banned + clear pending), `set_visitor_panel_message`
- [x] Faza 3.4 — `TerminalVisitorView` (oddzielna wiadomość w VC chat) + przycisk "Poproś o dostęp" dla nie-właścicieli
- [x] Flow: DM-notyfikacja właściciela + odświeżenie panelu po wysłaniu prośby (best-effort)
- [x] Rehydracja `TerminalVisitorView` po restarcie bota

## Faza 4 (LFG lokalne) — Zrobione

- [x] `core/database.py` — `lfg_sessions_collection()`
- [x] `services/lfg_service.py` — pełny CRUD + ensure_indexes (STATUS_ACTIVE/CLOSED/EXPIRED)
- [x] `ui/builders/lfg_embed.py` — `build_lfg_embed`, `build_lfg_closed_embed`
- [x] `ui/views/lfg_view.py` — `LfgStartModal` (tytuł + opis) + ogłoszenie + odświeżenie panelu
- [x] `ui/views/terminal_view.py` — LFG button odblokowany; `_lfg_callback` (start/stop toggle)
- [x] `cogs/lfg/__init__.py` + `cogs/lfg/tasks.py` — `LfgTasksCog` z `tasks.loop(minutes=5)` TTL cleanup
- [x] `core/bot.py` — `cogs.lfg.tasks` w `COG_EXTENSIONS`
- [x] `config.yaml.example` — `lfg.announcement_channel_id`, `lfg.session_ttl_minutes`
- [x] `config/loader.py` — domyślne wartości `announcement_channel_id` i `session_ttl_minutes`

## Faza 5 (LFG sieciowe) — Zrobione

- [x] `services/lfg_service.py` — stałe `LFG_CHANNEL_STARTED`, `LFG_CHANNEL_CLOSED`; pola `game_tags`, `network_visible`, `announcements` w dokumencie; `update_network_announcement`
- [x] `ui/views/lfg_view.py` — `lfg_tags` TextInput (opcjonalny) w `LfgStartModal`; parsowanie tagów; obliczanie `network_visible` z configa; publish `lfg:session_started`
- [x] `ui/views/terminal_view.py` — publish `lfg:session_closed` (expired=False) po ręcznym zatrzymaniu LFG
- [x] `cogs/lfg/tasks.py` — publish `lfg:session_closed` (expired=True) przy auto-wygaśnięciu TTL
- [x] `cogs/lfg/network.py` (nowy) — `LfgNetworkCog`: psubscribe `lfg:*`, on_started → hub-ogłoszenia zdalne, on_closed → edycja embedów na zdalnych serwerach
- [x] `core/bot.py` — `cogs.lfg.network` w `COG_EXTENSIONS`
- [x] `config.yaml.example` — `lfg.hub_channels`, `lfg.network_visible_default`
- [x] `config/loader.py` — domyślne wartości `hub_channels: {}`, zachowane `network_visible_default`, `network_guild_ids`

## Faza 6 (LFM) — Zrobione

- [x] `core/database.py` — `lfm_posts_collection()`
- [x] `services/lfm_service.py` — pełny CRUD: `build_post_document`, `create_post`, `get_post_by_id`, `get_active_post_by_author`, `list_active_posts`, `set_announcement`, `update_post_content`, `delete_post`, `mark_expired`, `list_expired_active_posts`
- [x] `ui/builders/lfm_embed.py` — `build_lfm_embed` (zielony, autor, tytuł, opis, wygasa-R, kontakt), `build_lfm_expired_embed` (szary, przekreślony tytuł)
- [x] `ui/views/lfm_view.py` — `LfmPostModal` (3 pola: tytuł+opis+kontakt), `LfmEditModal` (pre-filled defaults), `LfmPostView` (persistent, [Ed’ytuj] + [Usuń], tylko autor)
- [x] `cogs/lfm/__init__.py` + `cogs/lfm/events.py` — `LfmEventsCog`: hybridowa komenda `/lfm` (slash → modal, tekst → button), rehydracja widoków po restarcie
- [x] `cogs/lfm/tasks.py` — `LfmTasksCog`: `tasks.loop(minutes=30)` TTL cleanup, auto-expire embedów + usunięcie widoku
- [x] `core/bot.py` — `cogs.lfm.events` i `cogs.lfm.tasks` w `COG_EXTENSIONS`
- [x] `config.yaml.example` — sekcja `lfm` z `announcement_channel_id`, `post_ttl_hours`, `max_posts_per_user`
- [x] `config/loader.py` — domyślne wartości sekcji `lfm`

## Faza 7 (UI premium) — Zrobione

- [x] **7.3** `ui/builders/terminal_embed.py` — kolory: `brand_red` (prywatny) / `brand_green` (publiczny)
- [x] **7.3** `ui/builders/lfg_embed.py` — kolory: `og_blurple` (aktywne) / `greyple` (zamknięte)
- [x] **7.3** `ui/builders/lfm_embed.py` — kolor: `brand_green` (aktywne)
- [x] **7.2** `ui/views/terminal_view.py` — limit kanału obsługiwany przez kompatybilny modal; eksperyment z `RadioGroup` został wycofany po błędzie `50035 Invalid Form Body`, więc presety mają wracać tylko w formie wspieranej przez Discord UI
- [x] **7.1** `ui/views/info_view.py` (nowy) — persistent `InfoPanelView(LayoutView)` z Container, Separator, TextDisplay; sekcje: TempVC, LFG, LFM
- [x] **7.1** `cogs/tools/info.py` (nowy) — `InfoCog` z hybridową komendą `/info-panel`; sprawdza `features.info_panel_enabled`
- [x] `core/bot.py` — `cogs.tools.info` w `COG_EXTENSIONS`
- [x] `config/loader.py` — `features.info_panel_enabled` (domyślnie False)

## Faza Setup / 5.3 — Zrobione

- [x] `core/database.py` — dodane kolekcje `guild_settings` i `lfg_subscriptions`
- [x] `services/guild_settings_service.py` — zapis i odczyt konfiguracji per-guild z fallbackiem do globalnego `config.yaml`
- [x] `services/lfg_subscribe_service.py` — toggle subskrypcji tagów LFG + pobieranie subskrybentów
- [x] `ui/views/setup_view.py` — pełny panel setup z modułami: Temp VC, LFG, LFM, Info Panel, Logi moderacji
- [x] `cogs/tools/setup.py` — hybridowa komenda `/setup` dla administratorów serwera
- [x] `core/bot.py` — `cogs.tools.setup` dodany do bootstrapa
- [x] Temp VC runtime czyta override’y z Mongo: `enabled`, `trigger_channel_id`, `category_id`, `default_user_limit`, `cleanup_grace_seconds`
- [x] LFG runtime czyta override’y z Mongo: `enabled`, `announcement_channel_id`, `hub_channel_id`, `session_ttl_minutes`, `network_visible_default`
- [x] LFM runtime czyta override’y z Mongo: `enabled`, `announcement_channel_id`, `post_ttl_hours`
- [x] `cogs/lfg/network.py` — `LfgBellView`, DM-y do subskrybentów tagów, rehydracja bell views po restarcie
- [x] `ui/views/lfg_view.py` — lokalne ogłoszenia LFG dostają przycisk bell, jeśli sesja ma tagi

## Priorytet teraz — Backend hardening + UI parity

Najbliższy sprint powinien być podporządkowany nie dodawaniu kolejnych luźnych komend, tylko domykaniu interaction-first layer dla istniejących feature'ów.

- [x] Naprawić regresje startowe po refaktorze UI (`utils/embeds.py` duplicate tail, `setup_view.py` ChannelSelect API, payload `TerminalView` pod `LayoutView`)
- [x] Dodać opcję per-serwer dla większościowego kick/ban w Temp VC oraz wpiąć ją w setup i panel kanału
- [x] Naprawić błąd `Invalid Form Body` przy modalu limitu w panelu kanału
- [x] Rozszerzyć Temp VC o commandless akcje z panelu: takeover, szkic LFG i dynamiczne blokowanie/odblokowanie
- [x] Dodać pełniejszy setup Temp VC pod rekonfigurację: role takeover są już edytowalne z poziomu setupu
- [x] Wyciąć legacy `cogs.tools.menu` i sezonowy `cogs.fun.seasonal`, żeby ograniczyć zbędne command surface'y
- [x] Spisać audyt komend i plan migracji do workflow w [docs/COMMANDLESS_AUDIT.md](COMMANDLESS_AUDIT.md)
- [x] Spisać checklistę pilota / E2E w [docs/PILOT_CHECKLIST.md](PILOT_CHECKLIST.md)
- [x] Dodać voice audit, status kanału i bogatszy runtime Daddy Voice`s zgodny z najnowszym feedbackiem
- [x] Dodać commandless obsługę próśb o dostęp, ponownego wpuszczania zbanowanych oraz pola `GRAMY W` / `KOD DO GRY` w panelu Daddy Voice`s
- [x] Dodać rename-safe marker właściciela i trwały snapshot obecności do sesji Daddy Voice`s

- [ ] Zrobić audyt wszystkich głównych interfejsów Discord i wskazać, które komendy mają zniknąć, a które zostać tylko jako hybrid opener / admin fallback
- [ ] Wyciągnąć wspólny standard paneli premium dla Terminal / Setup / Info / LFG / LFM z rozróżnieniem: `LayoutView` tylko tam, gdzie daje realny zysk, `Embed + View` dla paneli dynamicznych
- [ ] Ograniczyć stare lub prymitywne powierzchnie sterowania (`menu.py`, legacy mod/stats/fun/admin commands) na rzecz flow interaction-first albo context menu
- [ ] Domknąć pełną migrację `warn` + `dossier` + `ch_status` do jednego workflow kartoteki/operatora zgodnie z [docs/COMMANDLESS_AUDIT.md](COMMANDLESS_AUDIT.md)
- [x] Etap 1 migracji kartoteki/operatora: `ch_status` otwiera prywatny panel z podglądem spraw i akcjami `warn` / `notatka` / `wezwanie`
- [x] Etap 2 migracji kartoteki/operatora: `warn`, `warnings`, `notatka`, `wezwanie` korzystają ze wspólnych helperów i otwierają panel, gdy operator nie poda treści od razu
- [x] Etap 3 migracji kartoteki/operatora: context menu użytkownika, ciężkie akcje panelu i warn management są już spięte z macierzą uprawnień oraz profilem zabezpieczeń celu
- [x] Etap 4 migracji kartoteki/operatora: `kontakt` korzysta już ze wspólnego runtime, a panel dostał akcję `Kontakt` obok `Notatka` i `Wezwanie`
- [x] Etap 5 migracji kartoteki/operatora: fallbacki `unban`, `przerwa` i `zprzerwy` korzystają już z tych samych helperów co panel operatora
- [x] Etap 6 migracji kartoteki/operatora: `ban` i `modco` korzystają już z jednego runtime bana, a dossier dostało osobną akcję `Ban MODCO`
- [x] Domknąć `ye_ban` do owner-only preview/confirm z deduplikacją kandydatów i osobnym audytem `EVT_MASS_BAN`
- [x] Utwardzić slashowy flow `/setup`: defer przed Mongo/UI i wspólna ścieżka response/followup dla hybryd interaction-backed
- [x] Przebudować bieżący panel Daddy Voice`s na bogatszą, mniej prymitywną hierarchię wizualną `LayoutView`
- [x] Dodać runtimeową zmianę bitrate i wyciąć ręczną zmianę prywatności z kanałów lobby / voting w panelu Daddy Voice`s
- [x] Odprymitywnić główny ekran Setup do zwartego cockpitu runtime zamiast płaskiego dumpa modułów
- [x] Podnieść LFG, LFM i Info Panel do gęstszego premium baseline oraz przepiąć `/info-panel` na wspólne helpery odpowiedzi
- [x] Dociągnąć opener `lfm`, ownerowe `banlist` / `banupdate` i hybrydy `statystyki` / `stats_range` do wspólnego modelu `defer + shared response helper`
- [x] Rozbudować `fortune_cookie` do bogatszej konfiguracji per guild/global zamiast opierać flow na twardych stałych z `settings.py`
- [x] Dodać pełny moduł Setup dla `fortune_cookie` z kanałami, cooldownem, rolami exempt i claim telemetryką
- [x] Dociągnąć detail/mobile-first pass głównego cockpitu Setup i Info Panelu z onboardingiem Fortune
- [x] Zamknąć Board/Emperor guardrails w shared moderation runtime zamiast trzymać je tylko w rozproszonych checkach komendowych
- [x] Uszczelnić współbieżny `RateLimiter` przed burstami podczas sieciowych sweepów moderacyjnych
- [x] Naprawić orphan recovery Daddy Voice`s tak, żeby startup adoptował wszystkie pasujące kanały, a nie tylko pierwszy na trigger
- [x] Znormalizować datetime w `moderation_guard_service`, żeby stare wpisy `moderation_policy.events` nie wywracały confirm workflow na naive/aware mismatch
- [x] Dodać w panelu operatora opener `Otwórz po ID`, żeby kartoteka obsługiwała także cele spoza aktualnego serwera, oraz utwardzić confirm view na nieoczekiwane wyjątki
- [x] Wysyłać DM z referencją i planowanym zakresem przed fizycznym wykonaniem bana oraz zawęzić widoczność `Ban MODCO` do samego MODCO/owner
- [x] Przepiąć `notatka`, `wezwanie`, `kontakt` i stany confirm workflow kartoteki na spójny, nowocześniejszy styl embedów
- [x] Dodać w Daddy Voice`s synchronizację fizycznego stanu kanału, reconcile loop z auto-odtwarzaniem paneli oraz wymuszenie publicznego lobby przy create/adopt/recovery
- [x] Domknąć crash-safe recovery Daddy Voice`s: ręcznie potwierdzić cały recovery pass po restartach i niespójnościach sesji oraz dopracować ostatnie edge case’y adopcji/cleanupu
- [x] Utrwalić aktywny stan voice poza RAM dla trackerów i heurystyk Daddy Voice`s
- [x] Wprowadzić politykę nieobecności właściciela kanału: timer, auto-transfer do najdłużej obecnego albo cleanup
- [x] Dodać enable/disable triggerów lub typów pokoi z setup/runtime bez ręcznej edycji `config.yaml`
- [ ] Dokończyć screenshot parity Terminala względem referencji: ostatnie różnice w gęstości akcji i mobile-first polish

- [ ] Test manualny `/setup`: zapisać ustawienia Temp VC / LFG / LFM i potwierdzić, że runtime reaguje bez restartu
- [ ] Test manualny Temp VC: 2+ kanały trigger z różnymi nazwami i takeover przez rolę wyższą od właściciela
- [ ] Test manualny LFG lokalnego: start → ogłoszenie → stop → embed zamknięcia → TTL
- [ ] Test manualny Fazy 5: propagacja LFG na 2+ serwerach
- [ ] Test manualny dzwonka LFG: zasubskrybować tag, wystartować nową sesję z tagiem, potwierdzić DM
- [ ] Test manualny LFM: włączyć `lfm.enabled`, ustawić `announcement_channel_id`, sprawdzić edycję/usunięcie ogłoszenia
- [ ] Włączyć `features.info_panel_enabled = true` i wywołać `/info-panel`
- [ ] Dodać minimalne testy regresyjne dla krytycznych flow bota i API
- [x] Domknąć anti-spam / cooldown dla LFG start, bell i DM sieciowych (5-min per-user cooldown DM + guard przed duplikatem sesji na VC)
- [x] Ujednolicić audit i logi dla akcji krytycznych user/staff/dashboard (`audit_service.py` + integracja: ban/Unban/warn/timeout/mass_ban)
- [x] Domknąć spójność działań moderacyjnych: perm audit/lift, pełne liczenie akcji banowych i odczyt tych akcji w API
- [x] Rozdzielić Temp VC na dwa typy triggerów: normalny oraz głosowania większościowego
- [ ] Dopić operacje: backup + restore, alerty, retencja, runbook, serwer pilotażowy
- [x] Przebudować Terminal do targetu wizualnego z referencyjnych screenów (`LayoutView` + `Container`, full-width bloki, accessory refresh, selecty wewnątrz panelu)
- [x] Ujednolicić styl wszystkich embedów i paneli: hierarchia, status block, sekcje, grouped actions (LFG/LFM + wszystkie embedy mod)
- [ ] Zrobić mobile-first UX pass dla Terminal / LFG / LFM / Setup / Info Panel

## Potem — Dashboard backend

- [ ] Testy scope/RBAC 403 i walidacja zakresu `guild_id`
- [ ] Cursor + limit + cache invalidation na hot path API
- [ ] Async export + pełniejszy audit zdarzeń dashboardu
- [ ] OpenAPI i kontrakty pod późniejszy frontend

## Otwarte decyzje operacyjne

- [ ] Ustalić docelowy okres retencji danych
- [x] Spisać runbook awaryjny
- [ ] Wskazać serwer testowy i pierwszy serwer pilotażowy
- [x] Dodany panel `/setup` dla administratora serwera do wygodnej konfiguracji modułów

## Zasada aktualizacji

Po każdym etapie aktualizujemy ten plik razem z [docs/CHANGELOG.md](CHANGELOG.md). Changelog opisuje historię zmian, a ten dokument pokazuje stan wykonania i najbliższy punkt wejścia. Każdy etap kończymy także krótką oceną realnego procentu postępu i pozostałych roboczogodzin.
