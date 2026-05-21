# Audyt Commandless — 2026-05-21

Ten dokument jest roboczą mapą konwersji OJCIEC 4.0 z modelu command-heavy do modelu interaction-first.

## Snapshot

- Stan po cleanupie: `33` surface'y komendowe w `cogs/`
- Nadal obecne: `38` surowych użyć `discord.Embed(...)`
- Nadal obecne: `4` użycia `discord.ui.LayoutView`
- Nadal obecne: `6` widoków persistent (`timeout=None`)
- Nadal obecne: `2` bezpośrednie call-site'y `ctx.send(...)` / `ctx.reply(...)` (oba w `core/interaction_responses.py` jako bazowy prefix fallback)
- Wniosek: fundament interaction-first istnieje, ale większość repo nadal opiera się na komendach, prostych embedach i częściowo starym modelu odpowiedzi; snapshot liczbowy jest już po pakiecie zmian z 2026-05-21.

## Usunięte od razu

### Legacy menu — wycięte

- `cogs/tools/menu.py`
- Komendy: `setupmenu`, `menu`, `disablemenu`, `enablemenu`, `resettime`
- Powód: pamięciowy stan procesu, brak trwałości, brak spójności z roadmapą interaction-first, brak wartości względem nowego setup/paneli

### Fun jednorazowy — wycięty

- `cogs/fun/seasonal.py`
- Komenda: `dzienmezczyzn`
- Powód: jednorazowy seasonal flow bez związku z docelową platformą interakcji

## Zostawić jako hybrid opener lub wejście administracyjne

### Docelowo zostają jako otwieracze workflow

- `cogs/tools/setup.py` → `/setup`
- `cogs/tools/info.py` → `/info-panel`
- `cogs/lfm/events.py` → `/lfm`
- `cogs/stats/status_check.py` → `/ch_status` jako prywatny opener panelu kartoteki/operatora
- `cogs/moderation/warn.py` → `warnings` jako prywatny opener tego samego panelu kartoteki

### Context menu / interakcje bez nazwy komendy

- `O użytkowniku` → user context menu otwierające ten sam panel co `ch_status`

Te komendy są zgodne z agendą, bo otwierają panel lub modal zamiast być całym UX-em.

## W trakcie przebudowy

### Lekkie akcje moderacyjne są już częściowo spięte

- `cogs/moderation/warn.py` → `warn` korzysta już ze wspólnego runtime i bez powodu otwiera panel zamiast iść osobnym flow
- `cogs/tools/dossier.py` → `notatka`, `wezwanie` korzystają już ze wspólnego runtime i bez treści otwierają panel kartoteki
- `cogs/tools/contact.py` → `kontakt` bez treści otwiera już panel kartoteki, a z treścią korzysta ze wspólnego runtime operatora zamiast własnego flow DM/logów
- `ui/views/operator_dossier_view.py` → panel obsługuje już także `kontakt`, `ban`, `Unban`, `przerwa`, `zdejmij przerwę`, `edytuj warn`, `archiwizuj warn`, `archiwizuj wszystkie`
- `ui/views/operator_dossier_view.py` → report-channel dossier cards są już restart-safe: przyciski raportowe działają po `DynamicItem`/`custom_id`, więc nie giną po restarcie procesu ani po wygaśnięciu krótkiego timeoutu widoku
- Wniosek: ten pion nie wymaga już osobnych embedów i osobnej logiki zapisu dla lekkiej moderacji; główny ciężar został już przesunięty na dalsze cięcie ciężkich komend jako fallbacków

### Część cięższych fallbacków jest już na wspólnym runtime

- `cogs/moderation/unban.py` → `unban` po resolve i checkach korzysta już z `execute_Unban_action(...)`
- `cogs/moderation/timeout.py` → `przerwa` i `zprzerwy` korzystają już z `execute_timeout_action(...)` oraz `execute_remove_timeout_action(...)`
- Wniosek: cięższa moderacja nie jest już równoznaczna z pełnym legacy; największy dług tej warstwy siedzi teraz głównie w `ban` / `modco` i owner-only `ye_ban`

## Zostawić jako owner/admin fallback

### Owner/runtime/ops

- `cogs/admin/server_mgmt.py` → `list_servers`, `leave_server`
- `cogs/admin/cleanup.py` → `sc`
- `cogs/admin/sync.py` → `sync`
- `cogs/admin/metrics.py` → `metrics`
- `cogs/admin/invite.py` → `zapros`
- `cogs/admin/ban_export.py` → `banlist`, `banupdate`
- `cogs/admin/role_admin.py` → `gr`, `sr`
- `cogs/stats/daily_stats.py` → `statystyki`
- `cogs/stats/range_stats.py` → `stats_range`

Te surface'y nie są priorytetem commandless dla zwykłego użytkownika. Mogą zostać jako owner/operator fallback albo docelowo trafić do dashboardu.

## Przebudować w pierwszej kolejności

### Moderacja i kartoteka

- `cogs/moderation/ban.py` → `ban`, `modco`
- `cogs/moderation/mass_ban.py` → `ye_ban`
- `cogs/moderation/warn.py` → `remove_warn`, `edit_warn`, `mass_remove_warn`

### Kierunek migracji

- `warn`, `notatka`, `kontakt`, `wezwanie` są już spięte ze wspólnym runtime i panelem dossier; następny krok to dalsze uproszczenie wejść komendowych i rozwój context menu wokół tego samego workflow
- `ch_status` jest już pierwszym openerem tego workflow i powinien zostać dalej rozwinięty zamiast wracać do prostego embeda
- `Unban`, `przerwa` i `zprzerwy` korzystają już z tego samego runtime co panel operatora; największym otwartym dublem w cięższej moderacji pozostaje `ban` / `modco`
- `ye_ban` zostawić jako owner-only fallback, ale owinąć w bezpieczniejszy confirm flow i audit-first UX

## Ciasteczka — osobny tor rozwoju, nie do usunięcia

### Zostaje

- `cogs/fun/fortune_cookie.py` → `wr`, `wrozba`, `cookie`

### Dlaczego zostaje

- To nie jest przypadkowy fun command, tylko rytuał społecznościowy z własnym storage w Mongo
- Już dziś korzysta z konfiguracji w bazie przez `services/user_service.py`

### Co już zostało zrobione

- gating działania nie zależy już wyłącznie od `config/settings.py`; `services/user_service.py` scala teraz globalny runtime config Fortune z override per guild zapisanym w `guild_settings`
- `wr` respektuje już konfigurację kanałów, cooldownu i exempt roles bez kolejnego hardcode'u w samym cogu
- `fortune_cookie` ma już własny moduł w `/setup`: kanały rytuału, cooldown, role exempt, reset do profilu globalnego i telemetrykę claimów bez osobnego panelu administracyjnego poza ekosystemem Setup
- claims zapisują event `EVT_FORTUNE_CLAIM`, więc moduł ma już podstawowy audit trail i operacyjny heartbeat per guild

### Co trzeba zrobić dalej

- dodać wygodniejszą edycję samej puli wróżb i obrazów z Discord UI albo dashboardu, zamiast polegać wyłącznie na dokumencie Fortune w Mongo
- rozważyć hybrydowy opener lub przycisk/panel rytuału zamiast surowej komendy tekstowej

## Najbliższa kolejność przebudowy

1. dokończyć pion `warn` + `dossier` wokół już istniejącego openera `ch_status`
2. `ban` / `modco` jako cięższy moderation workflow z progressive disclosure
3. `fortune_cookie` jako osobny, bogato konfigurowalny moduł społecznościowy
4. owner/admin fallback przenosić do dashboardu dopiero po stabilizacji warstwy Discord

## Definition of done dla commandless migration

- użytkownik końcowy nie musi znać nazwy komendy, żeby wykonać główny flow
- komenda, jeśli zostaje, otwiera panel albo modal zamiast wykonywać cały proces tekstowo
- sukcesy i błędy są domyślnie prywatne
- flow działa po restarcie, jeśli ma persistent view
- stan i konfiguracja są zapisane w Mongo lub w innym trwałym źródle, nie w RAM
