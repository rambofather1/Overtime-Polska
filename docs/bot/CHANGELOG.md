# Changelog

Ten plik śledzi większe zmiany architektoniczne i wdrożeniowe w drodze z **OJCIEC 1.07** do **OJCIEC 4.0**.

Aktualna linia wersji GitHub: **`v.58`** w ramach produktu **OJCIEC 4.0**. Historyczne rewizje **`v.1` → `v.58`** traktujemy jako kolejne iteracje tej samej linii rozwojowej 4.0.

## 2026-05-21 — Sprint 3 (cd.): v.58 dossier filters, kontenerowe potwierdzenia i hotfixy runtime

### Moderation cockpit — filtrowanie historii i potwierdzenia kontenerowe

- **`services/case_service.py`** i **`ui/views/operator_dossier_view.py`** — kartoteka operatora dostała stan filtrów historii spraw po typie akcji, serwerze i operatorze; filtrowanie działa bez wychodzenia z panelu `user` / dossier, a liczniki i lista spraw pokazują teraz wynik filtrowany względem pełnego wolumenu spraw użytkownika
- **`ui/views/operator_dossier_view.py`**, **`cogs/moderation/warn.py`**, **`cogs/tools/contact.py`** i **`cogs/tools/dossier.py`** — lekkie akcje `ostrzeżenie`, `notatka`, `kontakt` i `wezwanie` przestały kończyć się zwykłym embedem potwierdzenia; operator dostaje wspólny kontenerowy ekran potwierdzenia z wejściem do pełnej kartoteki i konkretnej sprawy, także przy bezpośrednich fallbackach komendowych

### Runtime hotfixy po audycie `errors.log`

- **`cogs/admin/sync.py`** — ownerowy `sync` nie zakłada już, że odpowiedź interaction-backed zawsze zwróci obiekt z `.edit()`; komenda potrafi odzyskać `original_response()` i poprawnie działa także z panelu Godmode
- **`cogs/temp_vc/events.py`** — join-to-create Daddy Voice`s przestał raportować fałszywy błąd, gdy użytkownik zdąży rozłączyć się z voice przed `move_to`; tworzenie kanału i powrót do istniejącej sesji są teraz odporne na ten race condition i kończą się cleanupem/warningiem zamiast głośnego tracebacku

## 2026-05-21 — Sprint 3 (cd.): pełny telemetry slice, retencja i widgety dashboardowe

### Telemetry policy — retencja, redakcja i maintenance

- **`config/loader.py`**, **`services/activity_service.py`**, **`core/bot.py`** i **`cogs/tracking/activity_maintenance.py`** — telemetry dostało sekcję `activity` w konfiguracji, centralne filtrowanie capture, sanitizację `content` / `metadata`, redakcję wzorców wrażliwych danych oraz okresową retencję czyszczącą stare wpisy z Mongo i JSONL; zapis aktywności przestał być bezwarunkowym `insert + append`

### Telemetry coverage — struktura serwera i profil członka

- **`cogs/events/guild_events.py`** — coverage został rozszerzony o zmiany serwera (`GUILD_UPDATE`), kanały (`CHANNEL_CREATE/UPDATE/DELETE`), wątki (`THREAD_CREATE/UPDATE/DELETE`), role (`ROLE_CREATE/UPDATE/DELETE`) i zaproszenia (`INVITE_CREATE/DELETE`), więc telemetry obejmuje już także ruch administracyjny i zmiany struktury serwera zamiast tylko zachowania użytkownika w wiadomościach i voice
- **`cogs/events/member_events.py`** — `on_member_update` nie kończy się już ślepym `return` dla wszystkiego poza timeoutem; logowane są też zmiany profilu i ról członka (`MEMBER_PROFILE_UPDATE`, `MEMBER_ROLE_UPDATE`), co daje ślad zmian personalnych i grupowych w obrębie guilda

### Dashboard read-side — widgety, role i watchlista

- **`api/app/activity_analytics.py`** i **`api/app/routes/activity.py`** — telemetry dostało gotowy read-side widgetowy: overview dla guilda i dla całej sieci admina, top użytkowników, top kanałów, top event types, top serwerów, kandydatów do watchlisty z preview współobecnych osób oraz grupowe summary ról; front dashboardu nie musi już liczyć tego ręcznie z surowego event streamu
- **`api/app/routes/activity.py`** — dodane zostały endpointy `/api/activity/guilds/{guild_id}/widgets/overview`, `/api/activity/widgets/network-overview` i `/api/activity/guilds/{guild_id}/roles/activity`, więc poza profilem osoby i social graphem istnieje już gotowa warstwa kart/rankingów pod dashboard operacyjny

## 2026-05-21 — Sprint 3 (cd.): telemetry coverage i analityka użytkownika / social graph

### Telemetry coverage — reakcje i głębsza klasyfikacja interakcji

- **`services/activity_service.py`** i **`cogs/events/message_events.py`** — telemetry aktywności został rozszerzony o surowe reakcje Discorda (`REACTION_ADD`, `REACTION_REMOVE`, `REACTION_CLEAR`, `REACTION_CLEAR_EMOJI`), więc strumień aktywności obejmuje już nie tylko wiadomości i komponenty, ale też realne użycie reakcji na serwerze bez zależności od cache wiadomości
- **`services/activity_service.py`** — log interakcji dostał dodatkową klasyfikację `interaction_scope` / `interaction_action`, wyciąganą z nazw komend i `custom_id`, dzięki czemu użycie przycisków oraz selectów Daddy Voice`s i innych paneli da się później agregować sensownie per feature, a nie tylko jako surowy `custom_id`
- **`cogs/tracking/global_voice.py`** — historyczne sesje `voice_sessions` zapisują teraz także `user_name` i `guild_name`, więc read-side analityczny nie musi już zgadywać nazw użytkownika i serwera wyłącznie z innych kolekcji

### Dashboard read-side — profil osoby, top serwery i top współobecni

- **`api/app/activity_analytics.py`** — dodana została wspólna warstwa analityczna dla aktywności użytkownika, licząca metrykę aktywności, breakdown kategorii/eventów, top kanały, top serwery oraz Top 5 współobecnych osób na podstawie pokrywających się sesji voice
- **`api/app/routes/activity.py`** — telemetry API dostało nowe endpointy profilu aktywności użytkownika zarówno per guild, jak i dla całej sieci admina (`/api/activity/guilds/{guild_id}/users/{user_id}/profile`, `/api/activity/users/{user_id}/network-profile`), więc dashboard może otworzyć dokładny profil osoby bez ręcznego składania eventów po stronie frontu
- **`api/app/routes/social.py`** i **`api/app/database.py`** — stary, prosty i nieautoryzowany `top-associates` został zastąpiony zabezpieczonym read-side social graphu dla jednej gildii i dla sieci admina, a Mongo dostało dodatkowe indeksy pod zapytania overlapów voice (`channel_id + start_time`, `guild_id + user_id + end_time`)

## 2026-05-21 — Sprint 3 (cd.): restart safety, lobby guard i v4 embeds w Daddy Voice`s i Dossier

### Daddy Voice`s — restart safety i automatyzacja cyklu życia
- **`cogs/temp_vc/events.py`** — rozbudowano pętlę uzgadniania stanu `reconcile_loop` o automatyczne usuwanie pustych kanałów głosowych (0 aktywnych członków) po restarcie bota i w cyklach cyklicznych. Wprowadzono odporność na błędy cache Discorda na starcie bota poprzez awaryjne pobieranie kanałów za pomocą `fetch_channel` z obsługą wyjątku `NotFound` (sesje są usuwane z MongoDB wyłącznie, gdy kanał fizycznie nie istnieje, co zapobiega gubieniu sesji przez bota). Dzięki temu całkowicie wyeliminowano potrzebę skanowania kategorii w poszukiwaniu nieśledzonych kanałów (`_recover_untracked_channels`), co gwarantuje 100% bezpieczeństwo dla stałych i ad-hoc tworzonych kanałów przez administratorów (bot nigdy nie dotknie pokoju, którego sam nie zapisał w bazie). Dodatkowo w konfiguracji wprowadzono listę wyjątków `safe_channel_ids`.
- **`cogs/temp_vc/events.py`** — zaimplementowano procedurę awaryjnego natychmiastowego transferu własności w przypadku, gdy dotychczasowy właściciel opuścił serwer Discord. Prawa własności są automatycznie delegowane na użytkownika o najdłuższym nieprzerwanym stażu w pokoju (lub losowego członka), a stary właściciel traci uprawnienia.
- **`cogs/temp_vc/events.py`** i **`ui/views/terminal_view.py`** — dodano blokadę prywatności dla kanałów typu Lobby (głosowanie większościowe). Z panelu operacyjnego usunięto przycisk zmiany widoczności, a ewentualne bezpośrednie interakcje zmiany statusu są blokowane na poziomie kodu.

### UI / UX — integracja standardu Cyber-Terminal v4
- **`utils/embeds.py`** i **`ui/views/operator_dossier_view.py`** — zintegrowano nowe builder'y embedów dla lekkiej moderacji (wezwania prywatne i publiczne, kontakty, notatki) oraz unowocześniono widok potwierdzenia i anulowania `OperatorExecutionConfirmView` zgodnie z neonowym standardem wizualnym v4.

## 2026-05-21 — Sprint 3 (cd.): telemetry backbone aktywności i runtime pod dashboard

### Logi aktywności — powrót realnego śladu użytkownika

- **`services/activity_service.py`** i **`core/database.py`** — dodany został nowy, strukturalny strumień telemetryczny `activity_events`, który zapisuje nie tylko błędy i surowe logi procesu, ale też zdarzenia wiadomości, voice, interakcji i lifecycle członków; zapis jest best-effort do Mongo oraz równolegle do JSONL w `logs/system/activity.jsonl` i `logs/guilds/{guild_id}/activity.jsonl`
- **`cogs/events/message_events.py`** i **`core/bot.py`** — zwykłe wiadomości, edycje, usunięcia i interakcje Discorda przestały przepadać bez śladu; bot loguje teraz treść wiadomości, attachmenty, podstawowy kontekst kanału/użytkownika oraz command/component interaction payload jako osobny strumień telemetryczny pod dashboard
- **`cogs/tracking/global_voice.py`** i **`cogs/events/member_events.py`** — globalny voice zapisuje już nie tylko finalne sesje do starszej kolekcji `voice_sessions`, ale też join/switch/leave/session events do nowego telemetry backbone; join/leave/ban/unban członka serwera trafia dodatkowo do tego samego ujednoliconego śladu aktywności

### Runtime i API — łącznik dla dashboardu

- **`core/logger.py`** — istniejący logger operacyjny zachował tekstowe logi i `errors.log`, ale każde jego zdarzenie jest teraz dodatkowo mirrorowane jako strukturalny event `BOT_LOG`, więc dashboard może czytać to, co bot sam mieli, bez parsowania plain-textowego `application.log`
- **`api/app/database.py`**, **`api/app/routes/activity.py`** i **`api/main.py`** — API dostało nową warstwę telemetryczną: overview globalny dla admina, szczegółową listę eventów per guild oraz summary per guild z breakdownem kategorii, top kanałów/użytkowników i zsumowanym czasem voice; to jest pierwszy realny backendowy łącznik pod późniejszy dashboard logów/aktywności

## 2026-05-21 — Sprint 3 (cd.): restart-safe dossier cards i synchronizacja snapshotu

### Moderacja — karty dossier odporne na restart

- **`ui/views/operator_dossier_view.py`** — karta dossier wysyłana na `report_channel_ids` została przepięta na restart-safe przyciski `DynamicItem` oparte o `custom_id`, więc `Kartoteka`, `Ostrzeż`, `Notatka`, `Kontakt` i `Wezwanie` nie zależą już od żyjącej instancji widoku z poprzedniego procesu ani od krótkiego timeoutu `LayoutView`
- **`ui/views/operator_dossier_view.py`** — szybkie modale z karty raportowej zostały uproszczone do modelu `bot + target`; target jest rozwiązywany leniwie z cache/API przy kliknięciu, a permission check pozostaje w tym samym shared runtime co pełna kartoteka operatora
- **`core/bot.py`** — bootstrap bota rejestruje teraz dynamiczne przyciski dossier już w `setup_hook`, więc po starcie nie trzeba skanować historii kanałów raportowych ani ręcznie dosyłać nowych kart logów

### Dokumentacja operacyjna — snapshot po persistent dossier

- **`docs/STATUS.md`**, **`docs/COMMANDLESS_AUDIT.md`** i **`docs/PILOT_CHECKLIST.md`** — snapshot repo został dosynchronizowany do nowego baseline'u: `6` widoków persistent, `16` bezpośrednich call-site'ów `ctx.send(...)` / `ctx.reply(...)`, nowy etap statusowy dla restart-safe kart dossier oraz osobny test restartowy dla kart raportowych w pilocie Discord

### Owner fallback — prywatność Godmode

- **`cogs/admin/godmode.py`** — ownerowy cockpit `godmode` nie publikuje już całego widoku na kanale, jeśli DM ownera są wyłączone; hybrydowa ścieżka używa wspólnego helpera prywatnej odpowiedzi, a prefixowy fallback kończy się już tylko komunikatem o konieczności włączenia DM
- **`docs/STATUS.md`**, **`docs/COMMANDLESS_AUDIT.md`** i **`docs/PILOT_CHECKLIST.md`** — snapshot interaction debt został dociągnięty po tym cleanupie do `15` bezpośrednich call-site'ów `ctx.send(...)` / `ctx.reply(...)`, a checklista owner fallbacków dostała osobny test braku wycieku panelu Godmode

### Godmode warn runtime + lżejsze wezwanie publiczne

- **`ui/views/godmode_view.py`** — adapter `InteractionCommandContext` dostał brakujący `channel`, więc ownerowe akcje `remove_warn` i `mass_remove_warn` uruchamiane z panelu Godmode nie wywracają już wspólnego runtime warnów na `AttributeError`
- **`ui/views/operator_dossier_view.py`** — publiczne `wezwanie` na kanale moderacyjnym zostało odchudzone do krótkiego pingu z prośbą o sprawdzenie DM; pełny embed pozostaje w logach i confirmach operatorskich zamiast dublować się 1:1 na samym kanale
- **`docs/STATUS.md`** i **`docs/PILOT_CHECKLIST.md`** — status oraz manualne scenariusze pilota zostały dosynchronizowane do naprawy adaptera Godmode i nowego, lżejszego formatu wezwania na kanale

### Shared response cleanup — owner fallbacki i Fortune

- **`cogs/admin/server_mgmt.py`**, **`cogs/admin/sync.py`**, **`cogs/admin/cleanup.py`**, **`cogs/fun/fortune_cookie.py`** i **`cogs/moderation/mass_ban.py`** — owner/admin fallbacki oraz `fortune_cookie` przeszły na wspólne helpery odpowiedzi zamiast lokalnych `ctx.send(...)`; dzięki temu wywołania przez Godmode i przyszłe interaction-backed ścieżki nie opierają się już na osobnych, surowych modelach odpowiedzi
- **`docs/STATUS.md`** i **`docs/COMMANDLESS_AUDIT.md`** — snapshot interaction debt został dociśnięty do `2` surowych call-site'ów `ctx.send(...)` / `ctx.reply(...)`, pozostających już tylko w `core/interaction_responses.py` jako bazowy fallback prefixowy

### Pilot ops — runbook foundation

- **`docs/RUNBOOK.md`** — dodany został pierwszy konkretny runbook pilota: topologia usług, komendy start/stop, smoke po starcie, backup/restore Mongo, zasady dla Redis, podstawowe scenariusze awaryjne i kolejność rolloutu per guild
- **`README.md`**, **`docs/WALKTHROUGH.md`** i **`docs/STATUS.md`** — repo wskazuje już runbook jako element obowiązkowej dokumentacji operacyjnej, a status operacyjny nie traktuje runbooka jako pustego hasła bez wykonanej treści

## 2026-05-20 — Sprint 3 (cd.): stabilność setupu, premium polish Daddy Voice`s i interaction hardening fallbacków

### Stats backbone, dashboard hooks i porównania zewnętrzne

- **`services/stats_service.py`**, **`cogs/stats/daily_stats.py`** i **`cogs/stats/user_tracker.py`** — statystyki sieciowe zostały spięte jednym snapshotem runtime: ownerowa komenda, hourly tracker i dzienne rekordy korzystają już z tego samego liczenia użytkowników, aktywnych serwerów voice, zajętych pokoi, peaków i udziału VC, zamiast utrzymywać osobne, rozjechane definicje danych
- **`cogs/stats/user_tracker.py`** — tracker zaczął wreszcie uruchamiać także jednorazowy snapshot startupu oraz przy każdym przebiegu aktualizuje zarówno godzinowy snapshot sieci, jak i dzienny dokument z peakiem VC; dashboard nie musi już polegać wyłącznie na ownerowej komendzie `statystyki`, żeby mieć sensowną serię danych
- **`core/command_dispatcher.py`**, **`core/event_bus.py`** i **`core/bot.py`** — most Bot ↔ API dostał live `get_network_snapshot` oraz bogatszy heartbeat statusu zapisujący ostatni stan do Redis; dashboard może teraz pobrać bieżący stan bota bez każdorazowego round-trip commandowego, a przy potrzebie ma też osobny live snapshot sieci
- **`api/app/routes/stats.py`** i **`api/app/database.py`** — stats API dostało nowe endpointy `network/latest`, `network/range`, `network/live` oraz magazyn `comparison/external`, dzięki czemu backend dashboardu ma już pod ręką zarówno nasze snapshoty wewnętrzne, jak i ręcznie importowane punkty odniesienia z zewnętrznych paneli analitycznych
- **`cogs/admin/metrics.py`** — ownerowy fallback `metrics` korzysta już ze wspólnego helpera odpowiedzi zamiast starego `ctx.send(..., ephemeral=...)`, więc nie utrzymuje oddzielnej, bardziej kruchej ścieżki interaction-backed


### Owner cockpit i actionable moderation cards
- **`ui/views/operator_dossier_view.py`** — karta dossier wysyłana na `report_channel_ids` nie jest już reply do raportu, tylko osobną wiadomością pod nim; sam komponent przestał też być zbiorem opisowych skrótów i daje teraz realne szybkie akcje `Ostrzeż`, `Notatka`, `Kontakt` i `Wezwanie`, spięte z tym samym shared runtime moderacyjnym oraz tym samym permission matrix co pełna kartoteka
- **`ui/views/godmode_view.py`** i **`cogs/admin/godmode.py`** — dodany został owner-only panel `godmode`, który opakowuje stare komendy recovery/admin w jeden prywatny cockpit komponentowy; akcje bez parametrów wykonują się od razu, a `zapros`, `gr`, `sr`, `stats_range`, `leave_server` i `ye_ban` otwierają modale lub preview zamiast wymagać ręcznego wpisywania pełnych komend
- **`ui/views/godmode_view.py`** — panel Godmode dostał też szybki ownerowy pakiet warn-recovery (`remove_warn`, `mass_remove_warn`) oraz przycisk `metrics`, więc ręczne czyszczenie warnów i snapshot runtime nie wymagają już osobnych komend poza panelem
- **`core/bot.py`** — runtime ładuje teraz dodatkowy cog `Godmode`, więc owner surface jest dostępny jako kolejny hybrydowy opener, a nie osobna gałąź poza normalnym bootstrapem bota
- **`cogs/admin/ban_export.py`**, **`cogs/admin/role_admin.py`**, **`cogs/admin/server_mgmt.py`**, **`cogs/admin/invite.py`**, **`cogs/admin/sync.py`**, **`cogs/admin/metrics.py`**, **`cogs/stats/range_stats.py`**, **`cogs/moderation/mass_ban.py`** i **`cogs/moderation/warn.py`** — ownerowe komendy używane przez Godmode zostały zdjęte z normalnej powierzchni: prefixowe są `hidden`, a hybrydy nie rejestrują już app-commandów, więc są widoczne tylko przez panel Godmode i zostają technicznym backendem/fallbackiem
- **`cogs/temp_vc/events.py`**, **`docs/PILOT_CHECKLIST.md`**, **`docs/STATUS.md`** i **`rozwój/02-temp-channels-terminal.md`** — Daddy Voice`s nie startuje już domyślnie jako prywatny pokój; nowe i odzyskiwane kanały są inicjalizowane jako `public`, a prywatność pozostaje świadomym stanem zmienianym z panelu wraz z visitor flow `Poproś o dostęp`

### Runtime hardening — Board guardrails, limiter i orphan recovery
- **`ui/views/operator_dossier_view.py`** — wspólny runtime kartoteki i ciężkiej moderacji respektuje już centralną ochronę `Board Equivalent` oraz rolę `Board/Emperor` z helperów uprawnień, zamiast ograniczać się wyłącznie do lokalnych `protected_roles` i `modco_protected`
- **`core/rate_limiter.py`** — `acquire()` dostał wewnętrzną synchronizację `asyncio.Lock`, więc współbieżne sweepy sieciowe nie powinny już przepuszczać burstów przez wspólny limiter tylko dlatego, że kilka tasków czytało to samo `allowance` przed `await sleep(...)`
- **`cogs/temp_vc/events.py`** — recovery nieśledzonych Daddy Voice`s przy starcie nie kończy już adopcji na pierwszym odzyskanym pokoju dla danego triggera; startup potrafi przejść przez wszystkie pasujące orphan channels
- **`services/moderation_guard_service.py`** — rate-limit moderacyjny normalizuje teraz stare timestampy z Mongo do UTC-aware przed filtrowaniem okna czasowego, więc confirm workflow nie wywraca się już na mieszance naive/aware `datetime` w `moderation_policy.events`
- **`ui/views/operator_dossier_view.py`** — panel operatora dostał opener `Otwórz po ID`, który pozwala pobrać i otworzyć kartotekę także dla użytkownika spoza aktualnego serwera; ten sam plik przechwytuje teraz także nieoczekiwane wyjątki w `OperatorExecutionConfirmView`, więc błąd wykonania nie kończy się już wyłącznie `Ignoring exception in view` bez feedbacku dla operatora
- **`ui/views/operator_dossier_view.py`** i **`services/ban_service.py`** — workflow bana tworzy teraz sprawę i wysyła DM z referencją oraz planowanym zakresem jeszcze przed fizycznym fan-outem bana, a dopiero potem aktualizuje rekord o realnie zbanowane i nieudane serwery; to zamyka lukę, w której użytkownik po pełnym network banie nie dostawał już żadnej wiadomości prywatnej
- **`cogs/moderation/ban.py`** — komenda `modco` przestała być slashowym app commandem i została zawężona do samej grupy `modco` plus owner bypass; role niższe nie powinny już widzieć ani dostawać ścieżki `Ban MODCO` poza dedykowanym workflow dla MODCO
- **`ui/views/operator_dossier_view.py`** — lekkie akcje kartoteki (`notatka`, `wezwanie`, `kontakt`) są teraz spięte z nowymi builderami embedów, a `OperatorExecutionConfirmView` pokazuje już spójne ekrany stanu dla wykonania i anulowania zamiast surowych tekstów; context menu, modale i confirm workflow nie rozjeżdżają się już wizualnie tak mocno między sobą
- **`cogs/temp_vc/events.py`** i **`ui/views/terminal_view.py`** — Daddy Voice`s dostał centralny sync fizycznego stanu kanału z Mongo (`status`, `bitrate`, `user_limit`, `channel status`, owner marker), cykliczny reconcile loop oraz self-healing paneli; martwy panel jest odtwarzany, a okresowy sweep może wymienić starą wiadomość na nową w tym samym voice chat
- **`cogs/temp_vc/events.py`** — kanały typu lobby / voting są teraz wymuszane jako `public` już przy create/adopt/reconcile, więc restart albo stary wpis sesji nie przywróci im prywatności ani visitor panelu wbrew zasadzie produktu
- W praktyce: domknięty został pakiet trzech realnych ryzyk runtime wskazanych w audycie luk 2026-05-20 — ochrona zarządu w shared moderation runtime, współbieżny rate limiting oraz wielokrotny recovery orphanów Temp VC

### Setup — interaction hardening
- **`core/interaction_responses.py`** — wspólny helper odpowiedzi dla hybryd przestał polegać na gołym `ctx.send(...)` w kontekście interaction-backed; odpowiedzi idą teraz przez jedną ścieżkę `send_interaction_response(...)` z bezpiecznym followup po stanie `already acknowledged`
- **`cogs/tools/setup.py`** — `/setup` deferuje odpowiedź od razu przed odczytem ustawień i budową widoku, więc wolniejszy slashowy flow nie powinien już wpadać w `404 Unknown interaction` przy otwieraniu panelu konfiguracji

### Daddy Voice`s — premium panel polish, etap 2
- **`ui/builders/terminal_embed.py`** — główny panel Daddy Voice`s został przebudowany na mniej płaski układ `LayoutView`: mocniejszy hero/header, bogatszy blok statusu operacyjnego, czytelniejszą migawkę sesji, lepiej pogrupowane sekcje składu/dostępu oraz bardziej „produktowy” blok gry, zdarzeń i zarządzania
- W praktyce: Daddy Voice`s jest teraz mniej prymitywny wizualnie i bardziej mobile-first, ale nadal pozostaje jeszcze ostatni cross-module polish, żeby ten sam poziom gęstości i hierarchii miały także Setup, LFG, LFM i Info Panel

### Daddy Voice`s — compact panel, bitrate i lobby guard
- **`ui/views/terminal_view.py`** — menu ustawień kanału zostało odchudzone: usunięta została duplikująca się opcja zmiany prywatności z selecta, dodane zostało runtimeowe sterowanie bitrate, a kanały lobby / voting nie pokazują już ręcznej zmiany prywatności z panelu
- **`ui/builders/terminal_embed.py`** — główny panel został dociśnięty bliżej docelowego konceptu: bardziej kompaktowe bloki `MIGAWKA SESJI`, `STAN DadVOICE-a` i `SESJA GRY I LFG`, mniej płaskie listy oraz czytelniejszy status kanału jako osobny blok z akcją tylko tam, gdzie ma sens produktowy
- **`services/audit_service.py`** — zmiana bitrate dostała własny event `EVT_VC_BITRATE_CHANGE`, więc nowe sterowanie kanałem pozostaje spójne z audytem panelu Daddy Voice`s

### Moderacja — ban i modco na jednym runtime
- **`ui/views/operator_dossier_view.py`** — wspólny runtime bana dostał tryb `network | modco`, a panel operatora zyskał brakującą akcję `Ban MODCO`, więc ciężka moderacja nie rozjeżdża już osobno komendy i dossier dla dwóch zakresów bana
- **`cogs/moderation/ban.py`** — komendy `ban` i `modco` zostały zredukowane do openerów/fallbacków nad tym samym helperem, tak jak wcześniej `unban` i `przerwa`; `modco` bez powodu otwiera teraz to samo dossier operatora zamiast utrzymywać oddzielny, legacy przebieg

### Setup — runtime cockpit polish
- **`ui/views/setup_view.py`** — główny ekran Setup został przebudowany z płaskiego dumpa modułów w bardziej zwarty cockpit runtime: snapshot konfiguracji, czytelniejszy stan modułów i gęstsze bloki Daddy Voice`s / LFG / LFM / Info / logów moderacji

### Owner fallback — ye_ban po twardnieniu
- **`cogs/moderation/mass_ban.py`** — `ye_ban` przestał być gołym bulk-banem: komenda robi teraz preview owner-only, wymaga jawnego potwierdzenia, deduplikuje kandydatów między serwerami i raportuje realne sukcesy per użytkownik zamiast zakładać, że każdy ban wszedł wszędzie
- **`services/audit_service.py`** — dodany został event `EVT_MASS_BAN`, dzięki któremu owner fallback ma osobny ślad audytowy poza zwykłymi banami jednostkowymi

### LFG / LFM / Info — premium baseline i interaction hardening
- **`ui/builders/lfg_embed.py`** i **`ui/builders/lfm_embed.py`** — ogłoszenia LFG i LFM dostały gęstsze, bardziej produktowe embedy: status runtime, lepszą hierarchię czasu/autora/kontaktu oraz mniej płaski układ informacji
- **`ui/views/info_view.py`** — Info Panel został przebudowany na bardziej aktualny i mniej prymitywny przewodnik po ekosystemie OJCIEC, z onboardingiem „jak zacząć” i lepiej opisanymi modułami zamiast starej listy funkcji
- **`cogs/tools/info.py`** — opener `/info-panel` korzysta już ze wspólnych helperów `defer + response/followup`, więc nie utrzymuje osobnej, bardziej kruchej ścieżki interaction-backed

### Instrukcje AI — częściowa personalizacja operacyjna
- **`.copilot/copilot-instructions.md`** — wdrożone zostały bardziej praktyczne reguły personalizacyjne: granica użycia profilu psychologicznego, `delta-first` w update’ach, obowiązek zakotwiczania pytań o postęp w procentach i roboczogodzinach oraz zasada, że komendy typu „ciśnij” domyślnie uruchamiają najwyższy otwarty front z roadmapy

### Interaction hardening — LFM, owner fallbacki i statystyki
- **`cogs/lfm/events.py`** — opener `/lfm` przestał polegać na gołych `ctx.send(..., ephemeral=True)` przy walidacji; komunikaty o wyłączonym module i aktywnym istniejącym ogłoszeniu idą teraz przez wspólny helper odpowiedzi, więc hybryda nie rozjeżdża się między prefixem i slashem
- **`cogs/admin/ban_export.py`** — ownerowe `banlist` i `banupdate` deferują teraz przy interakcjach, odpowiadają przez wspólny helper followupów i nie utrzymują już błędnego wpisu numeru sprawy przy imporcie banów (`case_number` zamiast martwej literówki)
- **`cogs/stats/daily_stats.py`** i **`cogs/stats/range_stats.py`** — hybrydy statystyk przeszły na wspólne helpery odpowiedzi, a generowanie wykresu `stats_range` robi teraz bezpieczny `defer`, zamiast ryzykować timeout slashowego requestu

### Fortune cookie — runtime config global + per guild
- **`services/user_service.py`** i **`services/guild_settings_service.py`** — `fortune_cookie` dostało resolver runtime, który scala globalną konfigurację z dokumentu Mongo z override per guild w `guild_settings`; ograniczenia guild/channel, cooldown i role exempt nie są już skazane wyłącznie na twarde stałe w `config/settings.py`
- **`cogs/fun/fortune_cookie.py`** — komenda `wr` korzysta już z rozwiązanego runtime configu, więc można ją uruchamiać z globalnym profilem albo lokalnym override serwera bez dalszego hardcode'u; sprawdzanie exempt roles nie zakłada też już na sztywno, że autor zawsze jest obiektem `discord.Member`

### Setup i telemetryka — fortune_cookie jako pełny moduł runtime
- **`ui/views/setup_view.py`** — cockpit Setup dostał szósty moduł `Fortune Cookie` z własnym widokiem konfiguracji: kanały rytuału, cooldown, role exempt, reset do profilu globalnego oraz wbudowaną telemetrykę claimów i snapshot puli wróżb
- **`services/audit_service.py`**, **`services/user_service.py`** i **`cogs/fun/fortune_cookie.py`** — każde użycie `wr` zapisuje teraz event `EVT_FORTUNE_CLAIM`, a runtime Fortune umie policzyć claims/7 dni, unikalnych użytkowników, ostatni claim i topkę puli bez osobnego subsystemu analitycznego
- **`services/user_service.py`**, **`services/guild_settings_service.py`** i **`ui/views/setup_view.py`** — Fortune dostało kolejny krok productizacji: serwer może teraz zawężać aktywną pulę po ID/tekście, dodawać lokalne wróżby tylko dla siebie i podpiąć własny fallback losowych obrazków bez ręcznego dłubania w centralnym dokumencie Mongo
- **`cogs/fun/fortune_cookie.py`** — sam claim przestał zakładać jedną globalną listę stringów; losowanie bierze teraz efektywną pulę runtime z warunkami per guild, lokalną selekcją i per-entry image fallbackiem, a embed odpowiedzi wygląda gęściej niż wcześniejszy pojedynczy opis

### Detail/mobile-first pass — Setup i Info
- **`ui/views/setup_view.py`** — główny embed `Setup Runtime` został dociśnięty do bardziej dojrzałego, mobile-first cockpitu: snapshot heartbeatów, Fortune jako osobny moduł i krótsze, gęstsze bloki stanu zamiast kolejnego płaskiego dumpa pól
- **`ui/views/info_view.py`** — Info Panel dostał nowy, bardziej aktualny onboarding: wyraźniejszą zasadę interaction-first, sekcję Fortune Cookie i bardziej spójny opis Setup/Info jako wejścia do całego ekosystemu OJCIEC

### Moderacja — finalny interaction hardening ownerowego `b`
- **`cogs/moderation/ban.py`** — success-path confirmów dla hybrydowej komendy `b` (nadanie i zdjęcie Perma) przestał polegać na gołym `ctx.send`; slashowy wariant odpowiada teraz przez wspólny helper interaction-safe zamiast wypadać poza ten sam model odpowiedzi co reszta ciężkiej moderacji

### Roadmapa i instrukcje wykonawcze — aktualizacja
- **`docs/STATUS.md`**, **`rozwój/05-fazy-wdrozenia-backlog.md`** i **`rozwój/10-masterplan-commandless-roadmap.md`** — najbliższy plan został zaktualizowany pod obecny stan: ostatnie cięcie ciężkiej moderacji (`ban` / `modco`), pełny ręczny E2E pilota Discord, końcowy premium polish cross-module, potem ops, a dashboard backend dopiero na końcu
- **`rozwój/06-konwencje-dla-agentow.md`** — dopisane zostały twardsze reguły dla AI: w hybrydach deferować przed Mongo/UI, używać wspólnych helperów odpowiedzi dla interakcji i zawsze synchronizować changelog/status/roadmapę po domkniętym etapie

## 2026-05-15 — Sprint 3: Daddy Voice`s parity, audyt i recovery backlog

### Daddy Voice`s i LFG — pakiet feedbacku użytkowników
- **`services/lfg_runtime_service.py`**, **`ui/views/lfg_view.py`**, **`ui/views/terminal_view.py`** i **`cogs/temp_vc/events.py`** — LFG dostał wspólny runtime dla aktualizacji ogłoszeń, przepisywania ownera i zamykania wraz z kanałem; aktywne LFG przechodzi teraz na nowego właściciela Daddy Voice`s z DM-notyfikacją, a usunięcie kanału lub cleanup Temp VC zamyka też powiązane ogłoszenie lokalne i sieciowe
- **`services/lfg_runtime_service.py`**, **`ui/views/lfg_view.py`** i **`ui/views/terminal_view.py`** — użytkownicy nie mogą już wrzucać linków do tytułu, opisu ani tagów LFG; tagi są też normalizowane i deduplikowane przed zapisem
- **`config/loader.py`**, **`services/guild_settings_service.py`**, **`services/lfg_service.py`**, **`ui/builders/lfg_embed.py`**, **`config.yaml.example`** i **`ui/views/setup_view.py`** — LFG dostał wsparcie dla `predefined_tags` oraz `predefined_games` z aliasami i `banner_url`; jeśli sesja pasuje do zdefiniowanej gry, embed pokazuje czytelną nazwę gry i jej banner, a katalog tagów/gier można już skonfigurować także z panelu Setup
- **`cogs/lfg/network.py`** i **`core/bot.py`** — globalny listener LFG przestał kończyć pracę na starcie, jeśli `event_bus` podniesie Redis chwilę później; cog czeka teraz na aktywne połączenie zamiast jednorazowo zgłaszać brak Redis i rezygnować z nasłuchu sieciowego
- **`cogs/temp_vc/events.py`**, **`services/temp_vc_service.py`**, **`config/loader.py`**, **`services/guild_settings_service.py`**, **`config.yaml.example`** i **`ui/views/setup_view.py`** — Daddy Voice`s wspiera teraz per-trigger `category_overrides_by_trigger` oraz `name_templates_by_trigger`, więc różne lobby mogą tworzyć pokoje w różnych kategoriach i z własnym formatem nazwy zamiast jednego globalnego schematu; te mapy są już edytowalne także w Setup
- **`cogs/temp_vc/events.py`** i **`ui/views/info_view.py`** — nowe Daddy Voice`s tworzone z lobby startują teraz od razu jako prywatne: bot zakłada deny na `@everyone`, zachowuje dostęp ownera i pilnuje wiadomości `Poproś o dostęp` także po rehydratacji
- **`cogs/temp_vc/events.py`** — zwykłe wejścia/wyjścia z aktywnego Daddy Voice`s uruchamiają teraz zduszony auto-refresh panelu, więc właściciel nie jest już skazany wyłącznie na ręczne `Odśwież`
- **`services/audit_service.py`** i **`ui/builders/terminal_embed.py`** — panel Daddy Voice`s pokazuje już ostatnie zdarzenia kanału na bazie istniejącego audit trail, więc logi kanałowe są widoczne bez schodzenia do backendu

### Hotfixy końcowe dnia — DM edge case i modal setupu
- **`cogs/moderation/ban.py`** — ścieżka `ban` uruchamiana z DM nie zakłada już istnienia `channel.mention`; log embed używa bezpiecznego fallbacku `DM`, więc komenda nie wywraca się na `DMChannel`
- **`ui/views/setup_view.py`** — etykiety pól w nowych modalach LFG i Daddy Voice`s zostały skrócone do limitu Discorda (`<= 45`), co usuwa `50035 Invalid Form Body` przy otwieraniu zaawansowanej konfiguracji setupu

### Audyt dojrzałości i nowy plan działania
- **`docs/STATUS.md`**, **`rozwój/10-masterplan-commandless-roadmap.md`** i **`rozwój/05-fazy-wdrozenia-backlog.md`** — po audycie dojrzałości projekt dostał nowy plan oparty już nie na dokładaniu feature’ów, tylko na trzech falach: domknięciu pilota Discord, redukcji prymitywnych surface’ów oraz dopiero potem backendzie dashboardu; plan wprost zaznacza też, że repo nie jest jeszcze w pełni „odprymitywnione”, bo nadal ma sporo legacy embedów, command surface’ów, luki w setupie i otwarte E2E/ops

### Setup parity — takeover roles z Discord UI
- **`ui/views/setup_view.py`** — setup Daddy Voice`s dostał osobny podwidok `RoleSelect` do edycji `takeover_role_ids`, więc role takeover można już ustawiać z Discord UI zamiast tylko oglądać ich stan w embeddzie modułu
- **`docs/STATUS.md`**, **`docs/PILOT_CHECKLIST.md`**, **`rozwój/10-masterplan-commandless-roadmap.md`** i **`rozwój/05-fazy-wdrozenia-backlog.md`** — luka setupowa została zamknięta, a najbliższym krokiem planu stało się pełne ręczne E2E pilota Discord zamiast dalszego dłubania w samym panelu

### Operator workflow parity — kontakt
- **`ui/views/operator_dossier_view.py`** — panel operatora dostał modal i akcję `Kontakt`, więc lekki flow operatorski obejmuje już `Ostrzeż`, `Notatka`, `Kontakt` i `Wezwanie` bez wychodzenia do oddzielnych, legacy komendowych ścieżek
- **`cogs/tools/contact.py`** — komenda `kontakt` została zredukowana do tego samego modelu co `notatka` i `wezwanie`: bez treści otwiera panel kartoteki, a z treścią korzysta już ze wspólnego helpera wykonania zamiast własnego silnika DM/logów
- **`docs/COMMANDLESS_AUDIT.md`** i **`docs/STATUS.md`** — audyt commandless i snapshot postępu zostały dosynchronizowane do faktu, że `kontakt` nie jest już osobnym wyjątkiem w pionie operator workflow

### Moderation fallback parity — Unban i Przerwa
- **`cogs/moderation/unban.py`** — komenda `unban` nie utrzymuje już własnej sekwencji biznesowej; po resolve i checkach korzysta ze wspólnego `execute_Unban_action(...)`, więc fallback komendowy i panel operatora używają tego samego runtime
- **`cogs/moderation/timeout.py`** — `przerwa` oraz `zprzerwy` zostały przepięte odpowiednio na `execute_timeout_action(...)` i `execute_remove_timeout_action(...)`, co usuwa duplikację logiki timeoutu między komendami i kartoteką operatora
- **`docs/COMMANDLESS_AUDIT.md`** i **`docs/STATUS.md`** — status commandless został zaktualizowany: cięższa moderacja jest mniej prymitywna niż wcześniej, a głównym otwartym dublem pozostaje już przede wszystkim `ban` / `modco` oraz owner-only flow `ye_ban`

### Moderation confirm + delivery parity — report_channel_ids i operator confirmations
- **`ui/views/operator_dossier_view.py`** — ciężkie akcje dossier (`ban`, `Ban MODCO`, `Perm`, `Unban`, `przerwa`, `zdejmij przerwę`) dostały etap jawnego potwierdzenia `Tak / Nie` przed wykonaniem zamiast natychmiast odpalać side-effect po submitcie modalu
- **`cogs/moderation/ban.py`**, **`cogs/moderation/unban.py`** i **`cogs/moderation/timeout.py`** — bezpośrednie komendy z podanym powodem nie omijają już confirm step; przed wykonaniem pokazują prywatny preview skutku akcji tak jak dossier
- **`ui/views/operator_dossier_view.py`**, **`cogs/moderation/warn.py`** i **`cogs/tools/dossier.py`** — post-akcyjny fan-out został ujednolicony: wynik trafia już nie tylko do audit/Mongo, ale też konsekwentnie do `report_channel_ids`, prywatnego potwierdzenia operatora oraz do embeda zwrotnego ze statusem dostarczenia
- **`utils/embeds.py`** — dodane zostały wspólne embedy `action_review_embed(...)` i `action_log_embed(...)`, więc confirmy i brakujące logi warn/note/wezwanie nie składają się już z kolejnych ad hoc, prymitywnych wariantów

### Moderacja i uprawnienia — zarządowy wyjątek operacyjny
- **`config/settings.py`** i **`utils/permissions.py`** — dodany został centralny wyjątek `BOARD_EQUIVALENT_USER_IDS`, więc ID `1127523682164690966` jest traktowane jak zarząd w helperach uprawnień i ochrony, zamiast wymagać ręcznego dopisywania po cogach
- **`cogs/moderation/ban.py`**, **`cogs/moderation/unban.py`**, **`cogs/moderation/timeout.py`**, **`cogs/moderation/warn.py`**, **`cogs/tools/contact.py`**, **`cogs/tools/dossier.py`** i **`cogs/stats/status_check.py`** — checki komend zostały przepięte na wspólny helper, więc wyjątek działa spójnie dla banów, timeoutów, kartoteki i komend operatorskich
- **`core/bot.py`** — naprawiony został uszkodzony blok `on_ready` / presence po błędnym wklejeniu surowego ID do kodu, co przywróciło poprawny boot i walidację pliku

### Daddy Voice`s — crash-safe recovery, etap 1
- **`cogs/temp_vc/events.py`** — join-to-create odzyskuje teraz istniejący kanał Daddy Voice`s po nazwie i triggerze właściciela, jeśli rekord sesji zniknął, zamiast od razu tworzyć duplikat z nowym `voice_channel_id`
- **`cogs/temp_vc/events.py`** — rehydracja przy starcie potrafi adoptować nieśledzone aktywne kanały Daddy Voice`s, gdy właściciel nadal siedzi w pokoju i kanał zachował domyślny wzorzec nazwy
- W praktyce: pierwszy recovery pass obejmuje już scenariusz „kanał przeżył restart / awarię, ale sesja nie wróciła”, choć pełna continuity właściciela i trackerów voice nadal pozostaje osobnym etapem

### Daddy Voice`s — continuity foundation, etap 2
- **`ui/views/terminal_view.py`** i **`cogs/temp_vc/events.py`** — właściciel Daddy Voice`s dostał trwały marker w overwrite kanału (`manage_channels=True`), dzięki czemu recovery nie musi już polegać wyłącznie na domyślnej nazwie kanału i działa także po ręcznym rename
- **`services/temp_vc_service.py`** — sesja Temp VC przechowuje teraz `current_member_ids`, `member_presence_order` i `owner_last_seen_at`, więc baza zaczyna pamiętać realny skład kanału i ostatnią obecność właściciela poza pamięcią procesu
- **`cogs/temp_vc/events.py`** — snapshot obecności jest odświeżany przy wejściu/wyjściu z kanału, rehydratacji i recovery, co tworzy fundament pod następny etap: automatyczny transfer właściciela albo cleanup po dłuższej nieobecności

### Daddy Voice`s — owner absence policy + voice continuity, etap 3
- **`services/temp_vc_service.py`**, **`services/guild_settings_service.py`**, **`config/loader.py`**, **`config.yaml.example`** i **`ui/views/setup_view.py`** — Temp VC dostał nowe pola runtime/setup: `owner_absence_grace_seconds` oraz `owner_absence_action`, dzięki czemu per guild da się ustawić próg i zachowanie `transfer | cleanup | off`
- **`cogs/temp_vc/events.py`** — gdy właściciel opuści aktywny Daddy Voice`s na dłużej niż ustawiony próg, bot uruchamia deterministyczną politykę: przekazanie pokoju najdłużej obecnemu członkowi albo cleanup kanału; task jest też wznawiany po rehydratacji po restarcie
- **`core/database.py`**, **`services/voice_session_service.py`**, **`cogs/tracking/global_voice.py`** i **`cogs/tracking/voice_tracker.py`** — aktywne sesje voice przestały być tylko RAM-owe; `global_voice` i `voice_tracker` zapisują teraz aktywny stan do `active_voice_sessions` i potrafią go zrehydrować po restarcie procesu
- **`cogs/tracking/voice_tracker.py`** — poprawione zostało też przełączanie między kanałami moderacyjnymi, żeby sesja była domykana i otwierana poprawnie zamiast gubić przebieg przy switchu

### Daddy Voice`s — runtime toggle typów triggerów, etap 4
- **`services/temp_vc_service.py`**, **`services/guild_settings_service.py`**, **`config/loader.py`**, **`config.yaml.example`** i **`ui/views/setup_view.py`** — Temp VC dostał per-guild przełączniki `normal_triggers_enabled` oraz `voting_triggers_enabled`, więc typ normalny i typ głosowania można wyłączyć z setup/runtime bez kasowania listy kanałów trigger i bez edycji YAML
- **`cogs/temp_vc/events.py`** — join-to-create, recovery i filtrowanie triggerów opierają się teraz o jeden helper aktywnego typu triggera, dzięki czemu wyłączony typ naprawdę przestaje tworzyć nowe Daddy Voice`s zamiast pozostawać martwą konfiguracją tylko w UI
- **`ui/views/terminal_view.py`** — vote kick/ban przestał być rozstrzygany wyłącznie po runtime całego guilda; panel najpierw czyta `member_vote_kick_ban_enabled` z sesji kanału, więc tryb głosowania nie „przecieka” już na normalne pokoje tego samego serwera

### Daddy Voice`s — hotfix interakcji i UTC w owner absence
- **`ui/views/terminal_view.py`** — przełączanie widoczności pokoju potwierdza interakcję wcześniej i ma fallback do edycji samej wiadomości panelu, więc wolniejsza ścieżka `toggle public/private` nie powinna już wpadać w `404 Unknown interaction`
- **`services/temp_vc_service.py`** — odczyt sesji Temp VC normalizuje teraz pola datetime do UTC-aware, więc scheduler owner-absence nie wywraca się już na mieszance `offset-naive` i `offset-aware` timestampów po odczycie z Mongo

### Voice tracking — hotfix UTC po rehydratacji
- **`services/voice_session_service.py`** — aktywne sesje `global_voice` i `voice_tracker` normalizują teraz `start_time` oraz `updated_at` do UTC-aware przy odczycie z Mongo, więc trackery nie wpadają już w `TypeError` po restarcie lub późniejszym domykaniu zrehydrowanych sesji

### Daddy Voice`s — audio abuse guard, etap 5
- **`ui/views/terminal_view.py`** — owner marker dla recovery przestał używać `manage_channels`; marker został przeniesiony na mniej inwazyjny overwrite, żeby właściciel Daddy Voice`s nie dostawał już natywnej ścieżki do ręcznego mute/overwrite abuse tylko dlatego, że bot musi pamiętać ownera po restarcie
- **`cogs/events/member_events.py`** — ręczny `server mute` / `server deafen` na aktywnym Daddy Voice`s jest teraz cofany przez guard moderacyjny tak samo jak ręczny ban/unban/timeout; actor dostaje strike polityki z metadanymi kanału i informacją, czy był ownerem tego Temp VC

### Daddy Voice`s — kolejny krok do referencyjnych screenów
- **`ui/builders/terminal_embed.py`** — panel dostał osobne bloki `STATUS KANAŁU`, `ZBANOWANI`, `PROŚBY O DOSTĘP`, `🎮 GRAMY W` i `🔐 KOD DO GRY`, a pod sekcjami zbanowanych i próśb pojawiły się dedykowane sloty na akcje zamiast dokładania kolejnych luźnych dropdownów
- **`ui/views/terminal_view.py`** — dodane commandless flow: `Pozwól wejść ponownie`, zatwierdzanie/odrzucanie próśb o dostęp i modal `Gra i kod`; panelowe audio mute/restore zostały celowo usunięte z UI, bo właściciel kanału robi to już natywnie ręcznie w Discordzie
- **`services/temp_vc_service.py`** i **`services/audit_service.py`** — stan sesji Daddy Voice`s przechowuje teraz `game_name` i `game_code`, a ich edycja trafia do osobnego eventu `EVT_VC_GAME_INFO`

### Audyt stanu i braków po sprincie
- **`docs/STATUS.md`** — snapshot projektu został przeliczony pod aktualny zakres: parity względem screenshotów, crash-safe recovery dla Temp VC i trwałość aktywnego voice poza RAM
- **`docs/PILOT_CHECKLIST.md`** — dopisane ręczne scenariusze E2E dla nowych sekcji Daddy Voice`s (`STATUS KANAŁU`, `GRAMY W`, `KOD DO GRY`, akceptacja/odrzucanie próśb, `Pozwól wejść ponownie`) oraz dla restart safety na tych samych ID kanałów
- **`rozwój/02-temp-channels-terminal.md`**, **`rozwój/05-fazy-wdrozenia-backlog.md`** i **`rozwój/10-masterplan-commandless-roadmap.md`** — plan został rozszerzony o brakujące elementy: adopcję istniejących Temp VC po awarii, trwały stan aktywnego voice, politykę nieobecności właściciela kanału i enable/disable triggerów bez restartu

## 2026-05-14 — Sprint 2 (cd.): Spójność działań moderacyjnych i perm

## 2026-05-14 — Sprint 2 (cd.): Bezpieczny workflow b_ban

### Potwierdzenie i eskalacja po `b`
- **`cogs/moderation/ban.py`** — komenda `b` wymaga teraz jawnego potwierdzenia przyciskiem przed wykonaniem właściwego `b_ban`
- **`cogs/moderation/ban.py`** — po wykonaniu `b_ban` bot wysyła dodatkowe DM-potwierdzenie do ownera bota oraz członków roli Kanclerza ds. Bezpieczeństwa i Administracji

### Blokada Unbana do czasu zdjęcia perma
- **`cogs/moderation/unban.py`** — aktywny perm blokuje każdy `unban`; zdjęcie perma zostało przeniesione do owner-only toggle w komendzie `b`
- **`ui/views/operator_dossier_view.py`** — panel operatora respektuje teraz ten sam flow, ale owner dostał już także commandless akcję `Nadaj Perm` / `Zdejmij Perm` bez wychodzenia do komendy `b`
- **`cogs/events/member_events.py`** — ręczny unban z poziomu Discorda jest automatycznie odwracany, jeśli w bazie nadal siedzi aktywny perm (`b_ban` w warstwie technicznej)

### Perm ban i owner-Unban
- **`cogs/moderation/ban.py`** — komenda `b` loguje teraz osobny event audytu `EVT_b_ban`, więc perm nie ginie w jednym worku ze zwykłym banem
- **`cogs/moderation/ban.py`** — ta sama komenda `b` działa teraz jako toggle perma: owner może nią zarówno założyć, jak i zdjąć perm bez utrzymywania osobnej komendy liftującej
- **`ui/views/operator_dossier_view.py`** — workflow `unban` w panelu operatora respektuje teraz sztywną separację: najpierw zdejmij perma akcją dossier albo komendą `b`, dopiero potem wykonaj `unban`
- **`services/audit_service.py`** — dodane typy zdarzeń `EVT_b_ban` i `EVT_perm_LIFT`

### Kolejna redukcja komend mod
- **`cogs/moderation/ban.py`** i **`cogs/moderation/unban.py`** — brak powodu nie wykonuje już legacy akcji z domyślnym placeholderem; obie komendy otwierają teraz dossier operatora jako commandless opener do wspólnego workflow
- **`cogs/moderation/timeout.py`** — `przerwa` i `zprzerwy` zostały przepięte na ten sam model: przy niepełnych danych wejściowych otwierają dossier operatora zamiast odpalać od razu osobny flow timeoutu

### Liczniki działań i odczyt historii
- **`services/ban_service.py`** — `get_previous_ban(...)`, `count_actions(...)` i leaderboard moderatorów liczą już pełne spektrum akcji banowych: `ban`, `modco_ban`, `b_ban`, `autoban`, `dashboard_ban`, `mass_ban`
- **`api/app/routes/reputation.py`**, **`api/app/routes/staff.py`** i **`api/app/routes/audit.py`** — reputacja, profil 360 i ocena staffu przestały opierać się wyłącznie na starych kodach `BAN/MCB/UNB/TOM` i uwzględniają nowe aliasy akcji

### Rename Daddy Voice`s
- **`ui/views/setup_view.py`**, **`ui/views/info_view.py`** i **`cogs/temp_vc/events.py`** — główne user-facing nazewnictwo modułu Temp VC / Terminal zostało przesunięte na `Daddy Voice`s`, żeby uprościć język produktu i zbić techniczne nazwy w codziennym UI

### Temp VC — dwa typy triggerów
- **`services/guild_settings_service.py`**, **`services/temp_vc_service.py`**, **`ui/views/setup_view.py`** i **`cogs/temp_vc/events.py`** — Temp VC obsługuje teraz dwa niezależne typy triggerów: normalne oraz głosowania; typ triggera zapisuje się do sesji i rozdziela zachowanie kick/ban już na etapie tworzenia kanału

## 2026-05-14 — Sprint 2 (cd.): O użytkowniku, etap 3

### Nowe wejście commandless i macierz uprawnień
- **`cogs/stats/status_check.py`** — dodane user context menu `O użytkowniku`, które otwiera ten sam prywatny panel co `ch_status`
- **`ui/views/operator_dossier_view.py`** — panel dostał macierz uprawnień rozbitą na lekkie akcje, ciężkie akcje, edycję warnów i owner-only archiwizację masową; uprawnienia są filtrowane przed wyrenderowaniem kontrolek
- **`ui/views/operator_dossier_view.py`** — dodany profil zabezpieczeń celu (`protected_roles`, `modco_protected`), widoczny bezpośrednio w panelu jako fundament pod przyszłe, bardziej wielowymiarowe modele bezpieczeństwa

### Panel operatora — cięższa moderacja i warn management
- **`ui/views/operator_dossier_view.py`** — dodane grupowane akcje panelu dla `ban`, `Unban`, `przerwa`, `zdejmij przerwę`
- **`ui/views/operator_dossier_view.py`** — dodane zarządzanie warnami z panelu: `edytuj warn`, `archiwizuj warn`, `archiwizuj wszystkie`
- **`services/audit_service.py`** — dodane eventy `EVT_WARN_EDIT`, `EVT_WARN_ARCHIVE`, `EVT_WARN_ARCHIVE_MASS` pod nowy runtime warn management
- **`utils/permissions.py`** — dodany helper `has_any_group_role(...)`, żeby nowe poziomy dostępu nie były dalej rozproszone po cogach

### Fallbacki i spójność workflow
- **`cogs/moderation/warn.py`** — `edit_warn`, `remove_warn` i `mass_remove_warn` korzystają już ze wspólnych helperów panelu; brak danych wejściowych kieruje operatora do wspólnego widoku zamiast do osobnych flow
- Pion `ch_status + context menu + warnings + warn + notatka + wezwanie` ma teraz jedno wspólne wejście i jedną wspólną powierzchnię akcji operatorskich

## 2026-05-14 — Sprint 2 (cd.): O użytkowniku, etap 2

### Legacy komendy przepięte na wspólny workflow
- **`ui/views/operator_dossier_view.py`** — logika `warn`, `notatka` i `wezwanie` została wyciągnięta do wspólnych helperów (`execute_warn_action`, `execute_note_action`, `execute_summon_action`) oraz helpera otwierającego panel `send_operator_dossier_panel(...)`
- **`cogs/moderation/warn.py`** — `warn` działa teraz jako cienki fallback nad wspólnym helperem, a wywołanie bez powodu otwiera panel kartoteki; `warnings` przestało renderować osobny legacy embed i otwiera ten sam panel prywatnie
- **`cogs/tools/dossier.py`** — `notatka` i `wezwanie` korzystają z tych samych helperów co panel; wywołanie bez treści przechodzi do panelu zamiast tworzyć oddzielny flow
- **`cogs/stats/status_check.py`** — `ch_status` korzysta już z helpera `send_operator_dossier_panel(...)`, więc wszystkie wejścia do kartoteki składają się teraz na ten sam mechanizm otwierania widoku

### UX i prywatność fallbacków
- Legacy fallbacki w `warn.py` zostały przesunięte na prywatne odpowiedzi zamiast prostych publicznych potwierdzeń na kanale
- Pion `warn + dossier + ch_status` ma już jeden wspólny runtime dla lekkiej moderacji, co ogranicza dalsze rozjeżdżanie się UI i logiki biznesowej

## 2026-05-14 — Sprint 2 (cd.): Cleanup legacy komend i audyt commandless

## 2026-05-14 — Sprint 2 (cd.): Pierwszy workflow kartoteki operatora

### Panel operatora zamiast surowego `ch_status`
- **`services/case_service.py`** — dodane read-side helpery `get_user_cases(...)` i `count_user_cases(...)`, żeby kartoteka mogła czytać dane z `mod_cases`, a nie tylko zapisywać nowe sprawy
- **`ui/views/operator_dossier_view.py`** — nowy prywatny panel operatora z podglądem spraw, warnów i banów oraz przyciskami `Ostrzeż`, `Notatka`, `Wezwanie`
- **`cogs/stats/status_check.py`** — `ch_status` przestał być prostym embedem statusowym i stał się hybrydowym openerem panelu kartoteki; dla moderatora może otworzyć kartę wskazanego użytkownika, a dla zwykłego użytkownika działa jako prywatny self-check

### Audit i commandless migration — etap 1
- **`services/audit_service.py`** — dodane typy zdarzeń `EVT_NOTE` i `EVT_SUMMON` pod nowe akcje z panelu operatora
- Pierwszy etap migracji `warn + dossier + ch_status` został zamknięty bez ruszania modelu danych i bez wycinania starych komend fallbackowych

### Dokumentacja procesu po etapie
- **`docs/STATUS.md`** i **`docs/WALKTHROUGH.md`** — dopisana stała zasada: po każdym etapie trzeba zaktualizować nie tylko changelog/todo, ale też bieżącą ocenę procentu realizacji i pozostałych roboczogodzin

### Usunięte powierzchnie legacy
- **`core/bot.py`** — z bootstrapa usunięte `cogs.tools.menu` oraz `cogs.fun.seasonal`; bot po cleanupie ładuje `35` cogów zamiast `37`
- **`cogs/tools/menu.py`** — usunięty cały stary, pamięciowy system `setupmenu/menu/disablemenu/enablemenu/resettime`
- **`cogs/fun/seasonal.py`** — usunięty jednorazowy seasonal slash `/dzienmezczyzn`
- **`config/settings.py`** — usunięte martwe ustawienia `OW_CHANNEL`, `OW_ADMIN_MOT`, `MENU_COOLDOWN` powiązane wyłącznie z legacy menu

### Dokumenty sterujące dalszą przebudową
- **`docs/COMMANDLESS_AUDIT.md`** — dodany audyt wszystkich aktualnych surface'ów komendowych po cleanupie (`31`), z podziałem na: usunąć, zostawić jako opener, zostawić jako owner/admin fallback i przebudować do workflow
- **`docs/PILOT_CHECKLIST.md`** — dodana checklista E2E/pilot dla Setup, Temp VC, vote kick/ban, takeover, LFG, LFM, restart safety i owner fallback

### Nowy nacisk roadmapy
- **`docs/STATUS.md`** — dodane wykonane zadania dla cleanupu legacy komend, audytu commandless i checklisty pilota; dopisany nowy dług priorytetowy: `warn + dossier + ch_status` oraz rozbudowa konfiguracji `fortune_cookie`
- **`docs/WALKTHROUGH.md`** — workflow repo rozszerzony o obowiązkową aktualizację `docs/COMMANDLESS_AUDIT.md` przy zmianach komend i `docs/PILOT_CHECKLIST.md` przy zmianach user-facing flow

## 2026-05-14 — Sprint 2 (cd.): Utrwalenie agendy interaction-first

### Dokumentacja robocza — stała agenda repo
- **`docs/WALKTHROUGH.md`** — dopisana nadrzędna zasada pracy: `interaction-first`, commandless dla użytkownika końcowego, komendy głównie jako hybrydowe openery workflow lub fallback admin/owner
- **`docs/WALKTHROUGH.md`** — doprecyzowany standard UI: `Embed + View` dla paneli dynamicznych, `LayoutView` tylko tam, gdzie daje realny zysk UX, progressive disclosure zamiast ściany komponentów

### Status projektu — korekta realizmu i estymacji
- **`docs/STATUS.md`** — dodany snapshot projektu na `2026-05-14`: checklista `87/104 = 83.7%`, ale realna realizacja docelowego produktu oceniona na około `65%`
- **`docs/STATUS.md`** — dodane widełki roboczogodzin: `55–85h` do dopięcia warstwy Discordowej do pilota oraz `132–206h` do szerokiego targetu OJCIEC 4.0
- **`docs/STATUS.md`** — poprawiona nieaktualna informacja o `RadioGroup` w modalu limitu; obecny stan odpowiada rzeczywistej kompatybilności `discord.py 2.7.1`

## 2026-05-14 — Sprint 2 (cd.): Majority vote w Temp VC setup

### Temp VC / panel kanału — nowe ustawienie per-serwer
- **`services/guild_settings_service.py`** i **`services/temp_vc_service.py`** — dodane pole `member_vote_kick_ban_enabled` do konfiguracji i runtime override dla `temp_vc`
- **`ui/views/setup_view.py`** — setup Temp VC dostał przełącznik `🗳️ Kick/Ban`, który pozwala wymusić większościowe głosowanie członków kanału zamiast decyzji samego właściciela

### Kick / ban z panelu kanału — większość zamiast pojedynczej decyzji
- **`ui/views/terminal_view.py`** — `TerminalKickSelect` i `TerminalBanSelect` obsługują teraz tryb głosowania; dla włączonej opcji bot publikuje głosowanie w kanale i wykonuje akcję dopiero po uzyskaniu większości `> 50%` uprawnionych członków kanału
- **`ui/views/terminal_view.py`** — bez włączonego głosowania zachowane jest szybkie wykonanie akcji, ale kick/ban korzystają już ze wspólnych helperów i zawsze odświeżają panel po sukcesie

### Panel UI — porządki po feedbacku
- **`ui/views/terminal_view.py`** — usunięty z widoku mylący, niewykorzystywany slot `Wzmacniacze Speranzy`
- **`ui/views/setup_view.py`** i **`ui/views/info_view.py`** — najbardziej widoczne teksty użytkownika przesunięte z nazwy `Terminal` na neutralny `panel kanału` / `panel zarządzania`

## 2026-05-14 — Sprint 2 (cd.): Commandless Temp VC i takeover ról

### Panel kanału — odzyskanie brakujących akcji z referencji
- **`ui/views/terminal_view.py`** — naprawiony `TerminalLimitModal`: limit wrócił na wspierany `TextInput`, co usuwa `400 Invalid Form Body` przy otwieraniu ustawień kanału
- **`ui/views/terminal_view.py`** — dropdown ustawień kanału dostał dodatkowe akcje commandless: dynamiczne `Zablokuj/Odblokuj`, `Dodaj opis LFG / Edytuj LFG` oraz `Zostań właścicielem`
- **`ui/views/terminal_view.py`** i **`ui/views/lfg_view.py`** — dodany szkic LFG per kanał (`title`, `description`, `tags`), który można edytować z panelu i którym prefillowany jest start sesji LFG

### Temp VC setup — pełniejsza rekonfiguracja serwera
- **`ui/views/setup_view.py`** — setup Temp VC obsługuje teraz wiele kanałów trigger jednocześnie, role takeover oraz lokalny reset modułu do defaults przed ponowną konfiguracją
- **`services/guild_settings_service.py`** i **`services/temp_vc_service.py`** — konfiguracja `temp_vc` rozszerzona o `trigger_channel_ids` i `takeover_role_ids`, z zachowaniem kompatybilności wstecznej z pojedynczym `trigger_channel_id`

### Przejęcie własności i typy kanałów
- **`ui/views/terminal_view.py`** — owner bota lub użytkownik z wybraną rolą takeover może przejąć kanał bez zgody właściciela, ale tylko gdy jego skonfigurowana rola stoi wyżej w hierarchii niż rola obecnego właściciela
- **`cogs/temp_vc/events.py`** — nazwa tworzonego kanału jest teraz budowana z nazwy kanału trigger, co pozwala obsłużyć różne typy pokoi bez osobnych komend i bez sztywnego prefiksu `Terminal`
- **`ui/views/terminal_view.py`** — użytkownikowe `ban` w panelu zostało doprecyzowane jako blokada wejścia wyłącznie do danego kanału, bez bana na cały serwer

## 2026-05-14 — Sprint 2 (cd.): Fixy regresji po refaktorze UI

### Fix startu modułów moderacyjnych
- **`utils/embeds.py`** — usunięty zdublowany, uszkodzony ogon starej wersji pliku po `action_confirm_embed()`; to naprawia `IndentationError` blokujący `automod`, `moderation`, `contact` i `dossier`

### Setup panel — kompatybilność z `discord.py 2.7.1`
- **`ui/views/setup_view.py`** — zamiana nieistniejącego dekoratora `discord.ui.channel_select` na wspierane `discord.ui.select(cls=discord.ui.ChannelSelect, ...)`

### Terminal LayoutView — poprawka payloadu Discorda
- **`ui/builders/terminal_embed.py`** — usunięte niedozwolone zagnieżdżone `Container`-y i surowe `Select`-y z wnętrza panelu; sekcje treści renderowane jako `TextDisplay`, a dropdowny opakowane w `ActionRow`, co usuwa błąd `400 Invalid Form Body`

### Komendy tools — usunięte kolizje nazw i aliasów
- **`cogs/tools/dossier.py`** — usunięty alias `note` z komendy `notatka`, żeby nie kolidował z aliasem w `warn`
- **`cogs/tools/contact.py`** — usunięty alias `wezwanie` z komendy `kontakt`, żeby nie kolidował z właściwą komendą `wezwanie` w dossier

## 2026-05-14 — Sprint 2 (cd.): Terminal LayoutView parity

### Terminal — przebudowa z embeda na panel komponentów
- **`ui/views/terminal_view.py`** — `TerminalView` przepisany z `discord.ui.View` na `discord.ui.LayoutView`; panel budowany jako `Container`, a nie embed z fieldami
- **`ui/builders/terminal_embed.py`** — dodany `build_terminal_panel_container()` z pełnowierszowymi blokami: nagłówek, status, metryki z akcesorium `Odśwież panel`, sekcje UPRAWNIENI / ZBANOWANI / CZŁONKOWIE / PROŚBY O DOSTĘP
- **`ui/views/terminal_view.py`** — status block bez ikon `🟢/🔴`; skrócone etykiety dropdownów (`Nazwa`, `Limit`, `Status`, `Zacznij/Stop LFG`, `Zezwól`, `Zbanuj`, `Wyrzuć`); placeholder `⚡ Wzmacniacze Speranzy` jako zarezerwowany trzeci slot

### Refresh / send flow — przejście na `view=`
- **`cogs/temp_vc/events.py`** — pierwszy panel Terminala wysyłany jako wiadomość z `LayoutView`, bez `embed=`
- **`ui/views/terminal_view.py`** — modale rename/limit, helper `_refresh_panel()` i refresh po prośbie o dostęp edytują teraz sam widok (`content=None, embed=None, view=...`)
- **`ui/views/lfg_view.py`** i **`cogs/lfg/tasks.py`** — odświeżenia Terminala po starcie / wygaszeniu LFG też przeszły na nowy panel

## 2026-05-14 — Sprint 2 (cd.): Fix visual + audit integracja w cogs

### Bug fix
- **`core/bot.py`** — usunięto `await super().on_interaction(interaction)` (AttributeError; w discord.py 2.x brak tej metody na klasie bazowej)

### Terminal embed — poprawki wizualne
- **`ui/builders/terminal_embed.py`** — usunięty `title=` (embed bez title bar, opis = nagłówek); `**WŁAŚCICIEL KANAŁU**` w CAPS; status block zmieniony z ` ``` ` na ` ```ansi ` z kolorami `\033[2;32m` (zielony) / `\033[2;31m` (czerwony); metryki LIMIT + BITRATE na jednej linii zamiast dwóch pól inline; sekcje dostępu (UPRAWNIENI/ZBANOWANI/CZŁONKOWIE/PROŚBY) jako ANSI label w field value zamiast field name

### Mod embedy — przebudowa (standard Terminal)
- **`utils/embeds.py`** — wszystkie embedy pozbawione `title=`; akcja przesunięta do `description` jako `emoji  **AKCJA** — użytkownik`; bloki ` ```ansi ` z powodem/serwerem/kanałem; footer z Sprawa# i imieniem moderatora

### Audit trail — pełna integracja
- **`cogs/moderation/ban.py`** — `audit_service.log(EVT_BAN)` po kroku 7 (zapis do MongoDB)
- **`cogs/moderation/Unban.py`** — `audit_service.log(EVT_Unban)` po kroku 5
- **`cogs/moderation/warn.py`** — `audit_service.log(EVT_WARN)` po `create_warning()`
- **`cogs/moderation/timeout.py`** — `audit_service.log(EVT_TIMEOUT)` po kroku 6 (case_service)
- **`cogs/moderation/mass_ban.py`** — `audit_service.log(EVT_BAN)` po każdym skutecznym banie zbiorczym

## 2026-05-14 — Sprint 2 (cd.): ANSI mod embeds + fix on_interaction crash

### Bug fix

- **`core/bot.py`** — usunięte `await super().on_interaction(interaction)`
- Dodane brakujące funkcje które były importowane ale nie istniały: `warn_log_embed`, `timeout_embed`, `action_confirm_embed`, `contact_confirm_embed`

## 2026-05-14 — Sprint 1: Backend hardening + Sprint 2: Terminal visual rebuild

### Nowe pliki

- **`services/audit_service.py`** — ujednolicony audit trail dla bota; stałe typów zdarzeń (`EVT_BAN`, `EVT_VC_KICK`, `EVT_LFG_START`, itd.); synchroniczne `log()` i `log_many()` do kolekcji `audit_events`; błędy logowane do stdlib logging, nigdy nie przerywają głównego flow
- **`core/database.py`** — dodana `audit_events_collection()` (kolekcja `audit_events` w bazie Overtime)

### Anti-spam / cooldown LFG

- **`cogs/lfg/network.py`** — dodany `_dm_last_sent: dict[str, float]`; DM do subskrybenta pomijany jeśli poprzedni był wysłany < 300s temu (`_DM_COOLDOWN_SECONDS`); automatyczny cleanup starych wpisów
- **`ui/views/lfg_view.py`** — guard przed duplikatem aktywnej sesji LFG na tym samym VC (`get_active_session_for_vc` przed `create_session`)

### Terminal — przebudowa wizualna (Sprint 2, pkt 11)

- **`ui/builders/terminal_embed.py`** — całkowita przebudowa embeda Terminala: tytuł `⚡ TERMINAL`, opis z właścicielem, blok statusu (STATUS/LFG z ikonami 🟢/🔴 w code block), metryki LIMIT/BITRATE/AKTYWNY OD, sekcje dostępu (UPRAWNIENI/ZBANOWANI/CZŁONKOWIE/PROŚBY O DOSTĘP) z mention listą i `+N więcej`, footer `VC id • owner id`
- **`ui/views/terminal_view.py`** — przebudowa TerminalView z 5-wierszowego panelu (przyciski + 4× UserSelect) na accordion 3-wierszowy: `[🔄 Odśwież panel]` + `StringSelect "⚙️ Zmień ustawienia kanału"` (Nazwa / Limit / Status / LFG) + `StringSelect "🛡️ Zmień uprawnienia kanału"` (Przekaż / Zezwól / Zbanuj / Wyrzuć); akcje uprawnień otwierają ephemeryczne widoki z UserSelect
- **`ui/views/terminal_view.py`** — nowa helperka `_refresh_panel()`: pobiera `panel_message_id` z sesji, fetcuje wiadomość i edytuje; naprawia stary bug gdzie `interaction.edit_original_response` edytował efemerę zamiast panelu
- **`ui/views/terminal_view.py`** — nowe klasy `TerminalSettingsSelect` i `TerminalPermissionsSelect` (discord.ui.Select z `row=1/2`)

### Embedy LFG / LFM — ujednolicenie stylu (Sprint 2, pkt 12)

- **`ui/builders/lfg_embed.py`** — nowy układ: `🎮  {tytuł}`, thumbnail awatara, pola: Organizuje / Kanał / Wygasa / Tagi (jeśli są); embed zamknięcia: przekreślony tytuł + `*Sesja zakończona/wygasła.*` bez thumbnail
- **`ui/builders/lfm_embed.py`** — opis jako mention (`author.mention`) zamiast `display_name`; tytuł `🔍  {tytuł}`; expired embed: przekreślony `~~🔍  tytuł~~`



### Zmiany w planach

- **`rozwój/04-discord-py-2.7-ui-ux.md`** — doprecyzowany standard docelowego UI po porównaniu obecnego Terminala z wariantem referencyjnym; dodane zasady doboru komponentów i wspólny standard paneli
- **`rozwój/05-fazy-wdrozenia-backlog.md`** — backlog przestawiony na kolejność: backend hardening → UI parity → dashboard backend; dodana osobna faza visual polish
- **`rozwój/10-masterplan-commandless-roadmap.md`** — roadmapa przepisana pod obecny etap projektu: sprint backendowy, sprint UX parity i dopiero potem dashboard backend
- **`docs/STATUS.md`** — uporządkowane następne kroki pod sprint hardening + visual parity, bez starych sekcji faz, które były już historyczne

### Wpływ na roadmapę

- Dashboard pozostaje ważny, ale nie wyprzedza już stabilizacji backendu i dopracowania Discord UI.
- Plan od teraz rozróżnia wyraźnie "feature działa" od "feature jest gotowy do pilota i wygląda jak docelowy produkt".
- Wszystkie główne embedy i panele mają być dalej rozwijane według jednego standardu UX/visual, a nie per-moduł.


## 2026-05-14 — Setup panel per-serwer + Faza 5.3 (LFG bell / subskrypcje)

### Nowe pliki

- **`services/guild_settings_service.py`** — per-guild config z modułami `temp_vc`, `lfg`, `lfm`, `moderation`, `info_panel`; zapis częściowy `update_module`; odczyt runtime override’ów
- **`services/lfg_subscribe_service.py`** — subskrypcje tagów LFG per user; `toggle_subscription`, `get_subscribers_for_tags`, `get_user_tags`
- **`ui/views/setup_view.py`** — pełny Discord UI setup panel z widokiem głównym i widokami modułów; channel-selecty, przełączniki, modale zaawansowane i zapis do Mongo
- **`cogs/tools/setup.py`** — komenda `/setup` (hybrid, `manage_guild`) otwierająca panel konfiguracji serwera

### Zmodyfikowane pliki

- **`core/database.py`** — dodane kolekcje `guild_settings` i `lfg_subscriptions`
- **`core/bot.py`** — dodany `cogs.tools.setup` do `COG_EXTENSIONS`
- **`services/temp_vc_service.py`** — runtime merge: globalny `config.yaml` + override’y `guild_settings.temp_vc`; bez utraty kompatybilności dla serwerów bez wpisu w Mongo
- **`cogs/temp_vc/events.py`** — join-to-create i cleanup czytają per-guild konfigurację Temp VC
- **`ui/views/lfg_view.py`** — LFG start używa per-guild configu; lokalne ogłoszenie dostaje `LfgBellView` jeśli sesja ma tagi
- **`ui/views/terminal_view.py`** — blokuje start LFG, jeśli moduł jest wyłączony na serwerze, ale pozwala zatrzymać już aktywną sesję
- **`ui/views/lfm_view.py`** — LFM create używa per-guild configu z fallbackiem do globalnych ustawień
- **`cogs/lfm/events.py`** — `/lfm` respektuje per-guild enable flag z setup panelu
- **`services/lfg_service.py`** — dodane `list_active_sessions()` pod rehydrację widoków dzwonka
- **`cogs/lfg/network.py`** — `LfgBellView(timeout=None)` z `custom_id`; rehydracja bell views po restarcie; DM-y do subskrybentów pasujących tagów; zdalne hub-kanały czytane z setup panelu (`guild_settings.lfg.hub_channel_id`) z fallbackiem do starego `config.yaml`

### Wpływ

- Panel `/setup` jest teraz realnym punktem konfiguracji serwera, a nie tylko formularzem zapisującym dane.
- Kanały i TTL ustawione z Discord UI zaczynają wpływać na Temp VC, LFG i LFM bez potrzeby ręcznej edycji `config.yaml`.
- LFG dostało mechanikę „obserwuj tagi” z powiadomieniami DM i zachowaniem po restarcie bota.

---

## 2026-05-14 — Faza 7: UI premium (brand colors, RadioGroup presets, LayoutView info panel)

### Nowe pliki

- **`ui/views/info_view.py`** — persistent `InfoPanelView(LayoutView, timeout=None)` z `Container`, `Separator`, `TextDisplay`; sekcje: Temp VC, LFG, LFM; accent `og_blurple`
- **`cogs/tools/info.py`** — `InfoCog`; hybridowa komenda `/info-panel` (wymaga `manage_guild`); sprawdza feature flag `features.info_panel_enabled`; defer + `channel.send(view=InfoPanelView())`

### Zmodyfikowane pliki

- **`ui/builders/terminal_embed.py`** (7.3) — kolory: `brand_red` (kanał prywatny) + `brand_green` (publiczny) zamiast `orange`/`green`
- **`ui/builders/lfg_embed.py`** (7.3) — kolor aktywny: `og_blurple` (zamiast `blurple`); zamknięty: `greyple` (zamiast `dark_gray`)
- **`ui/builders/lfm_embed.py`** (7.3) — kolor aktywny: `brand_green` (zamiast `green`)
- **`ui/views/terminal_view.py`** (7.2) — `TerminalLimitModal` przebudowany: `RadioGroup` (presety 2/4/6/8/10/bez limitu) zamiast `TextInput`; domyślna opcja ustawiana dynamicznie z aktualnego limitu kanału; dodano import `RadioGroupOption`
- **`core/bot.py`** — `"cogs.tools.info"` w `COG_EXTENSIONS`
- **`config/loader.py`** — `features.setdefault("info_panel_enabled", False)`

---


## 2026-05-14 — Faza 6: LFM (ogłoszenia 72h, edycja/usunięcie, auto-expire)

### Nowe pliki

- **`services/lfm_service.py`** — CRUD `lfm_posts`: `build_post_document`, `create_post`, `get_active_post_by_author`, `list_active_posts`, `set_announcement`, `update_post_content`, `delete_post`, `mark_expired`, `list_expired_active_posts`
- **`ui/builders/lfm_embed.py`** — `build_lfm_embed` (aktywne: zielony, autor, wygasa-R, kontakt) + `build_lfm_expired_embed` (szary, przekreślony tytuł, status Wygasłe/Usunięte)
- **`ui/views/lfm_view.py`** — `LfmPostModal` (3 pola), `LfmEditModal` (pre-filled defaults dynamiczne przez `add_item`), `LfmPostView` (persistent view; [Ed’ytuj] + [Usuń]; `interaction_check` tylko autor)
- **`cogs/lfm/__init__.py`** — pakiet
- **`cogs/lfm/events.py`** — `LfmEventsCog`: hybrid command `/lfm`; slash → modal; text → `_LfmEntryView` z przyciskiem (timeout 120s); rehydracja `LfmPostView` po restarcie
- **`cogs/lfm/tasks.py`** — `LfmTasksCog`: `@tasks.loop(minutes=30)` TTL cleanup; `_expire_one` → `mark_expired` + edit embed + `view=None`

### Zmodyfikowane pliki

- **`core/database.py`** — `lfm_posts_collection()` → `get_overtime_db()["lfm_posts"]`
- **`core/bot.py`** — `cogs.lfm.events` i `cogs.lfm.tasks` w `COG_EXTENSIONS`
- **`config.yaml.example`** — sekcja `lfm` z `announcement_channel_id`, `post_ttl_hours`, `max_posts_per_user`
- **`config/loader.py`** — domyślne wartości sekcji `lfm`

---

## 2026-05-14 — Faza 5: LFG sieciowe Overtime (Redis hub-channels)

### Nowe pliki

- **`cogs/lfg/network.py`** — `LfgNetworkCog`: psubscribe `lfg:*`; on_started → post embed w hub-kanałach zdalnych gildii + zapis `update_network_announcement`; on_closed → edycja embedów zamknięcia/wygaśnięcia na zdalnych serwerach

### Zmodyfikowane pliki

- **`services/lfg_service.py`** — stałe `LFG_CHANNEL_STARTED = "lfg:session_started"`, `LFG_CHANNEL_CLOSED = "lfg:session_closed"`; pola `game_tags`, `network_visible`, `announcements` w dokumencie sesji; nowa funkcja `update_network_announcement(session_id, guild_id, channel_id, message_id)`
- **`ui/views/lfg_view.py`** — `lfg_tags` TextInput (opcjonalny, max 100 znaków) w `LfgStartModal`; parsowanie tagów (split+strip+lower); obliczanie `network_visible` z `features.lfg_network_enabled` + `lfg.network_visible_default`; publish `lfg:session_started` po utworzeniu sesji
- **`ui/views/terminal_view.py`** — publish `lfg:session_closed` (expired=False) w ścieżce ręcznego zatrzymania LFG
- **`cogs/lfg/tasks.py`** — publish `lfg:session_closed` (expired=True) po auto-wygaśnięciu TTL
- **`core/bot.py`** — dodany `"cogs.lfg.network"` do `COG_EXTENSIONS`
- **`config.yaml.example`** — `lfg.hub_channels` (dict guild_id→channel_id), `lfg.network_visible_default`
- **`config/loader.py`** — `lfg.setdefault("hub_channels", {})` (pozostałe klucze sieciowe były już obecne)

---

## 2026-05-14 — Faza 4: LFG lokalne (start/stop, ogłoszenie, TTL)

### Nowe pliki

- **`services/lfg_service.py`** — CRUD `lfg_sessions`; ensure_indexes; `build_session_document`, `create_session`, `get_active_session_for_vc`, `set_announcement`, `close_session`, `list_expired_active_sessions`, `mark_expired`
- **`ui/builders/lfg_embed.py`** — `build_lfg_embed` (aktywna sesja) + `build_lfg_closed_embed` (zamknięta/wygasła, szary kolor, przekreślony tytuł)
- **`ui/views/lfg_view.py`** — `LfgStartModal` (discord.ui.Modal): tytuł (3–100 znaków) + opis (0–500 znaków, paragraph). `on_submit`: tworzy sesję LFG, publikuje embed w `lfg.announcement_channel_id`, aktualizuje Terminal embed. Lazy import `TerminalView` (unika cyklu modułów)
- **`cogs/lfg/__init__.py`** — pakiet
- **`cogs/lfg/tasks.py`** — `LfgTasksCog` z `@tasks.loop(minutes=5)`: wyszukuje przeterminowane sesje, oznacza jako `expired`, czyści `lfg_session_id` z sesji VC, edytuje ogłoszenie na „Wygasła", odświeża panel Terminala

### Zmodyfikowane pliki

- **`core/database.py`** — `lfg_sessions_collection()` → `get_overtime_db()["lfg_sessions"]`
- **`core/bot.py`** — dodany `"cogs.lfg.tasks"` do `COG_EXTENSIONS`
- **`config.yaml.example`** — sekcja `lfg` z `announcement_channel_id` i `session_ttl_minutes`
- **`config/loader.py`** — domyślne wartości dla `announcement_channel_id` (null) i `session_ttl_minutes` (120)
- **`ui/views/terminal_view.py`** — przycisk LFG odblokowany (`disabled=False`); `_lfg_stub_callback` → `_lfg_callback` (toggle: jeśli LFG aktywne → zamknij sesję + edytuj ogłoszenie; jeśli brak → otwórz `LfgStartModal`)

---

## 2026-05-14 — Terminal Dnia 4: AllowSelect, BanSelect, VisitorView (Fazy 3.2a/b, 3.4)

### Faza 3.2a — Zezwól na dostęp (Row 3)

- dodany `TerminalAllowSelect` (UserSelect, Row 3) w `ui/views/terminal_view.py`
- callback: `grant_access(guild_id, vc_id, user_id)` + `channel.set_permissions(user, connect=True, speak=True)`
- po nadaniu dostępu embed odświeżany automatycznie

### Faza 3.2b — Ban użytkownika (Row 4)

- dodany `TerminalBanSelect` (UserSelect, Row 4) w `ui/views/terminal_view.py`
- callback: `add_banned_user(...)` + `channel.set_permissions(user, connect=False, speak=False)` + `move_to(None)` jeśli user w kanale

### Serwis — nowe helpery

- `grant_access(guild_id, vc_id, user_id)` — atomowe: `$addToSet allowed` + `$pull banned` + `$pull pending`
- `set_visitor_panel_message(guild_id, vc_id, msg_id|None)` — zapisuje/czyści ID panelu gości
- `build_session_document` — dodane pole `visitor_panel_message_id: None`

### Faza 3.4 — Prośba o dostęp (TerminalVisitorView)

- dodana klasa `TerminalVisitorView(discord.ui.View)` — oddzielna wiadomość w VC chat (nie Terminal)
  - `custom_id=f"tempvc:request_access:{vc_id}"` — stabilny, umożliwia rehydrację po restarcie
  - callback: sprawdza czy zbanowany / już uprawniony / właściciel; `add_access_request(...)`; DM do właściciela (best-effort); odświeża Terminal embed (licznik prośb)
- `toggle_visibility_callback` w `TerminalView` — przy przejściu → prywatny: wysyła VisitorView do kanału VC; przy przejściu → publiczny: usuwa wiadomość VisitorView
- `_rehydrate_terminal_views` w `cogs/temp_vc/events.py` — rehydruje TerminalVisitorView gdy session ma `visitor_panel_message_id`

---

## 2026-05-14 — Terminal Dnia 3: Redis lock, kick, listy dostępu

### Faza 2.2 — Redis lock

- dodany lock `tempvc:create:{guild_id}:{user_id}` (SET NX EX 10s) w `_handle_trigger_join`
- ciało logiki przeniesione do `_handle_trigger_join_locked`; lock zwalniany w `finally`
- fallback: gdy Redis niedostępny bot kontynuuje bez locka i loguje best-effort

### Serwis — listy dostępu, ban i prośby

- `add_allowed_user` / `remove_allowed_user` — `$addToSet` / `$pull` na `allowed_user_ids`; add automatycznie usuwa z `banned_user_ids`
- `add_banned_user` / `remove_banned_user` — analogicznie; ban usuwa z `allowed_user_ids` i `pending_access_requests`
- `add_access_request` — `$addToSet` na `pending_access_requests` (idempotentne)
- `accept_access_request` — przesuwa z `pending` do `allowed_user_ids`
- `reject_access_request` — usuwa z `pending_access_requests`

### Faza 3.3 — Kick z VC

- dodany `TerminalKickSelect` (Row 2) w `ui/views/terminal_view.py`
- UserSelect wskazuje dowolnego członka serwera; callback sprawdza, czy jest w kanale
- właściciel nie może wyrzucić siebie; boty są chronione
- `await member.move_to(None)` — wymaga `MOVE_MEMBERS` po stronie bota

### Embed

- pole **Dostęp** w embeddzie Terminala pokazuje liczniki: `Uprawnieni N • Zbanowani N • ⚠️ Prośby N`
- pole pojawia się tylko gdy przynajmniej jedna lista jest niepusta

---

## 2026-05-14 — Terminal Dnia 2: akcje i reconcile

### Terminal — nowe akcje

- dodano `TerminalRenameModal` — modal zmiany nazwy kanału głosowego z poziomu panelu
- dodano przycisk **Zmień nazwę** (Row 0, pozycja 4) w `TerminalView`
- dodano przycisk **LFG** (Row 0, pozycja 5) jako disabled stub do czasu wdrożenia Fazy 4; callback odsyła efemerę "wkrótce"
- panel Terminala ma teraz 5 akcji w Row 0: Odśwież, Publiczny/Prywatny, Zmień limit, Zmień nazwę, LFG
- dodano `TerminalLimitModal` i `TerminalRenameModal` — oba po zatwierdzeniu odświeżają panel przez `fetch_message().edit()`
- `TerminalTransferSelect` i `TerminalView` — po transferze `owner_id` aktualizowany jest w pamięci widoku i selektu

### Reconcile sesji przy starcie

- ulepszony `_rehydrate_terminal_views` w `cogs/temp_vc/events.py`:
  - sesje bez kanału → usuwane z Mongo (raportowane w logach jako "sieroty")
  - sesje z kanałem ale bez panelu → `_send_terminal_panel` wywoływany ponownie
  - sesje z kanałem i panelem → rehydracja widoku jak dotychczas
- raportowanie liczby usuniętych sierot w logu przy każdym starcie

### Konfiguracja

- dodano `config.yaml.example` z pełną sekcją `features`, `temp_vc` (trigger, kategoria, limit, bitrate, grace, feature flags) i `lfg` oraz mapą `allowed_roles`

---

## 2026-05-14 — Foundation Dnia 1

### Architektura

- dodano `core/interaction_responses.py` jako wspólną warstwę odpowiedzi dla `commands.Context` i `discord.Interaction`
- dodano `core/interaction_registry.py` jako foundation pod rehydrację persistent views po restarcie
- w `core/bot.py` podłączono bootstrap interakcji do `setup_hook`
- usunięto martwy wpis `cogs.events.error_handler` z listy ładowanych rozszerzeń

### Stabilność hybryd

- globalny error handler w `core/bot.py` używa teraz bezpiecznej ścieżki odpowiedzi zamiast bezpośredniego `ctx.send(..., ephemeral=True)`
- aktywne cogi z hybrydami zostały przepięte na wspólne helpery odpowiedzi
- dodano prywatną ścieżkę odpowiedzi dla komend wrażliwych, która dla prefixu spada do DM zamiast ujawniać dane na kanale
- poprawiono błąd w `cogs/tools/menu.py`, gdzie logika roli zakładała istnienie `ctx.interaction`

### Dokumentacja

- dodano root `README.md`
- dodano `docs/STATUS.md` jako checklistę wykonanych prac i punkt powrotu
- dodano `docs/WALKTHROUGH.md`
- uporządkowano ścieżkę wejścia do dokumentacji rozwojowej przez `rozwój/10-masterplan-commandless-roadmap.md`

### Temp VC foundation

- znormalizowano `config.yaml` przez domyślne sekcje `features`, `temp_vc` i `lfg` w `config/loader.py`
- dodano kolekcję `temp_voice_channels` w `core/database.py`
- dodano `services/temp_vc_service.py` jako pierwszy moduł domenowy pod sesje join-to-create
- dodano `cogs/temp_vc/events.py` z minimalnym workflow join-to-create, przeniesieniem użytkownika i cleanupem pustych kanałów
- podpięto Temp VC do bootstrapa bota w `core/bot.py`
- dodano `ui/builders/terminal_embed.py` i `ui/views/terminal_view.py` jako minimalny panel Terminala
- dodano zapis `panel_message_id` oraz rehydrację persistent views dla Terminala po restarcie

## Zasada aktualizacji

Po każdej większej zmianie należy dopisać tu wpis zawierający:

- datę,
- obszar zmian,
- wpływ na architekturę lub wdrożenie,
- ryzyka lub migracje, jeśli występują.