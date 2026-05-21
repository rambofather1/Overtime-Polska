# Pilot Checklist — Discord UI / Commandless

Ta checklista służy do ręcznych testów E2E przed pilotem i po większych zmianach interaction-first.

## 1. Setup per guild

- Włączyć `Temp VC`, `LFG`, `LFM`, `Info Panel` przez `/setup`
- Włączyć i skonfigurować `Fortune Cookie` przez `/setup`: kanały rytuału, cooldown, role exempt
- Zapisać konfigurację i potwierdzić, że runtime reaguje bez restartu
- Sprawdzić reset modułu Temp VC i ponowną konfigurację
- Sprawdzić zapis wielu triggerów Temp VC
- Sprawdzić zapis ról takeover Temp VC
- Sprawdzić zapis `owner_absence_grace_seconds` i `owner_absence_action` w setupie Temp VC
- Sprawdzić zapis `normal_triggers_enabled` i `voting_triggers_enabled` bez utraty zapisanych list triggerów
- Sprawdzić zapis `category_overrides_by_trigger` i potwierdzić tworzenie pokoi w różnych kategoriach bez restartu
- Sprawdzić zapis `name_templates_by_trigger` i potwierdzić custom lobby dla różnych triggerów
- Sprawdzić zapis `predefined_tags` i `predefined_games` w setupie LFG oraz potwierdzić, że runtime waliduje je bez restartu

## 2. Temp VC — tworzenie i panel

- Wejść na pierwszy trigger i potwierdzić utworzenie kanału o nazwie wynikającej z triggera
- Wejść na drugi trigger i potwierdzić inny typ/nazwę kanału
- Potwierdzić, że nowy Daddy Voice`s startuje domyślnie jako publiczny, a panel pozwala później świadomie przełączyć go na prywatny wraz z `Poproś o dostęp`
- Wyłączyć typ normalny w setupie i potwierdzić, że jego kanały trigger przestają tworzyć Daddy Voice`s bez kasowania zapisanej listy
- Włączyć typ normalny ponownie i potwierdzić, że zapisane kanały trigger znów działają bez ręcznej rekonfiguracji
- Sprawdzić odświeżenie panelu po rename, limit, lock/unlock
- Sprawdzić runtimeową zmianę bitrate z panelu i potwierdzić odświeżenie wartości w `MIGAWKA SESJI`
- Sprawdzić blok `STATUS KANAŁU` oraz modal edycji statusu
- Ustawić `GRAMY W` i `KOD DO GRY` i potwierdzić odświeżenie panelu
- Sprawdzić sekcję `OSTATNIE ZDARZENIA` i potwierdzić, że pokazuje najnowszy audit kanału
- Sprawdzić szkic LFG z panelu kanału
- Sprawdzić, że w panelu nie ma już legacy slotu po boosterach

## 3. Temp VC — uprawnienia i takeover

- Nadać dostęp użytkownikowi z panelu
- Wysłać prośbę o dostęp z widoku gościa i zatwierdzić ją z panelu właściciela
- Wysłać drugą prośbę i odrzucić ją z panelu właściciela
- Zablokować wejście użytkownikowi z panelu i potwierdzić, że to nie jest ban serwerowy
- Użyć `Pozwól wejść ponownie` i potwierdzić, że użytkownik może wrócić na kanał
- Wyrzucić użytkownika z kanału bez blokady wejścia
- Przejąć kanał jako owner bota
- Przejąć kanał jako rola wyższa od właściciela
- Potwierdzić, że rola równa lub niższa nie może przejąć kanału
- Spróbować ręcznie użyć `server mute` albo `server deafen` na użytkowniku w aktywnym Daddy Voice`s i potwierdzić auto-cofnięcie oraz strike policy

## 4. Temp VC — głosowanie kick/ban

- Włączyć tryb większościowy `Kick/Ban` w setupie
- Wyłączyć typ głosowania w setupie i potwierdzić, że kanały z listy voting nie tworzą nowych pokoi do czasu ponownego włączenia typu
- Potwierdzić, że normalny pokój na tym samym guildzie dalej wykonuje szybki kick/ban bez głosowania, gdy tylko kanał voting ma tryb majority
- Potwierdzić, że kanał lobby / voting nie pokazuje ręcznej zmiany prywatności w panelu Daddy Voice`s
- Rozpocząć vote kick i potwierdzić próg `> 50%`
- Rozpocząć vote blokady wejścia i potwierdzić ten sam próg
- Sprawdzić timeout głosowania bez rozstrzygnięcia
- Wyłączyć tryb większościowy i potwierdzić szybkie wykonanie przez właściciela

## 5. LFG lokalne

- Uruchomić LFG z panelu kanału
- Uruchomić LFG z tagiem należącym do `predefined_games` i potwierdzić czytelną nazwę gry oraz banner w embeddzie
- Sprawdzić ogłoszenie na kanale LFG
- Potwierdzić, że embed LFG pokazuje nowy blok statusu sesji, czas i czytelny snapshot gry/tagów
- Edytować szkic/opis LFG i potwierdzić aktualizację ogłoszenia
- Zakończyć LFG z panelu
- Poczekać na TTL i potwierdzić auto-zamknięcie w osobnym teście

## 6. LFG sieciowe

- Włączyć widoczność sieciową na 2 serwerach
- Utworzyć sesję z tagami
- Potwierdzić replikę ogłoszenia na drugim serwerze
- Potwierdzić przycisk dzwonka i DM do subskrybenta
- Zamknąć sesję i potwierdzić zamknięcie replik
- Zrestartować bota przy aktywnym Redis i potwierdzić, że listener LFG po starcie przechodzi z trybu oczekiwania do realnego nasłuchu sieciowego

## 7. LFM

- Otworzyć `/lfm` i przejść cały modal
- Potwierdzić publikację ogłoszenia
- Potwierdzić, że embed LFM pokazuje status runtime, czas wygaśnięcia i osobny blok kontaktu
- Wywołać `/lfm` przy wyłączonym module oraz przy już aktywnym ogłoszeniu i potwierdzić prywatny komunikat zamiast martwej interakcji
- Edytować ogłoszenie z widoku
- Usunąć ogłoszenie z widoku
- Sprawdzić auto-expire w osobnym teście TTL

## 8. Info Panel i premium UI

- Włączyć `Info Panel` w setupie
- Wysłać `/info-panel`
- Potwierdzić, że panel pokazuje już sekcję Fortune Cookie i zwięźlej opisuje interaction-first / recovery path
- Potwierdzić, że panel pokazuje onboarding `Jak zacząć` oraz aktualny opis Daddy Voice`s / LFG / LFM / Setup zamiast starej listy funkcji
- Sprawdzić czytelność na telefonie i desktopie
- Sprawdzić spójność języka wizualnego z Terminal, LFG, LFM i Setup

## 8a. O użytkowniku

- Otworzyć `/ch_status` jako zwykły użytkownik i potwierdzić prywatny self-check
- Otworzyć `/ch_status <id>` jako moderator i potwierdzić prywatny panel wskazanego użytkownika
- Otworzyć user context menu `O użytkowniku` i potwierdzić, że prowadzi do tego samego panelu
- Wywołać `warnings @user` i potwierdzić, że otwiera ten sam panel kartoteki, a nie osobny legacy embed
- Wywołać `warn @user` bez powodu i potwierdzić, że otwiera panel z akcją `Ostrzeż` dostępną z przycisku
- Wywołać `notatka @user` oraz `wezwanie @user` bez treści i potwierdzić, że oba przechodzą do tego samego panelu
- Nadać `warn` z przycisku i potwierdzić nowy wpis w panelu po odświeżeniu
- Dodać `notatkę` z przycisku i potwierdzić nową sprawę w kartotece
- Wysłać `wezwanie` z przycisku i potwierdzić wiadomość publiczną + DM best-effort
- Nadać `ban`, `Ban MODCO`, `Unban`, `przerwę` i `zdjęcie przerwy` z panelu oraz potwierdzić logi i prywatne odpowiedzi
- Dla `ban`, `Ban MODCO`, `Perm`, `Unban`, `przerwa` i `zdjęcie przerwy` potwierdzić nowy etap `Potwierdź / Anuluj` przed wykonaniem właściwej akcji
- Po wykonaniu ciężkiej akcji potwierdzić, że embed zwrotny pokazuje sekcję `Dystrybucja` z wynikiem wysyłki do `report_channel_ids`, DM do operatora i DM do celu, jeśli dotyczy
- Potwierdzić, że `Ban MODCO` respektuje `skip_modco_servers` i nie uderza w pełny zakres sieciowego bana
- Wywołać `ban @user`, `modco @user`, `unban @user`, `przerwa @user` i `zprzerwy @user` bez pełnych argumentów i potwierdzić, że każdy flow otwiera to samo dossier operatora zamiast odpalać osobny legacy przebieg
- Wywołać `ban @user <powód>`, `modco @user <powód>`, `unban @user <powód>`, `przerwa @user <czas> <powód>` i `zprzerwy @user <powód>` z pełnymi argumentami i potwierdzić, że komenda nadal wymaga prywatnego preview `Tak / Nie`, a nie wykonuje akcji od razu
- Nadać `Perm` z dossier jako owner i potwierdzić log, DM do celu oraz dodatkową eskalację do ownera / Kanclerza
- Spróbować `Unban` przy aktywnym Permie i potwierdzić twardą blokadę oraz komunikat o konieczności `Zdejmij Perm`
- Zdjąć `Perm` z dossier jako owner i potwierdzić osobny log `EVT_perm_LIFT` bez automatycznego unbana
- Wykonać `Unban` dopiero po zdjęciu Perma i potwierdzić wyjątek + log + DM best-effort
- Spróbować ręcznego Discord unban przy aktywnym Permie i potwierdzić automatyczny re-ban przez guard w `member_events`
- Wywołać hybrydowe `b @user <powód>` oraz zdjęcie Perma i potwierdzić, że końcowy confirm w slashowym wariancie przychodzi interaction-safe, a nie jako surowa wiadomość na kanał
- Sprawdzić `edytuj warn`, `archiwizuj warn` i `archiwizuj wszystkie` z poziomu panelu
- Potwierdzić, że `edytuj warn`, `archiwizuj warn`, `archiwizuj wszystkie`, `notatka` i `wezwanie` zostawiają teraz również czytelny wpis na `report_channel_ids`, a nie tylko sam audit w Mongo
- Potwierdzić, że panel pokazuje zabezpieczenia celu (`chroniony`, `modco-safe`) tam, gdzie role ochronne istnieją
- Wywołać `warn @user <powód>`, `notatka @user <treść>` i `wezwanie @user <powód>` jako fallback i potwierdzić prywatne potwierdzenie bez dodatkowego legacy embeda statusowego
- Kliknąć kartę dossier wysłaną na `report_channel_ids`, zrestartować bota i potwierdzić, że `Kartoteka`, `Ostrzeż`, `Notatka`, `Kontakt` i `Wezwanie` nadal działają bez martwych przycisków
- Potwierdzić, że publiczne `wezwanie` na kanale moderacyjnym jest już tylko krótkim pingiem z prośbą o sprawdzenie DM, a pełny szczegół zostaje w logach/report channelach zamiast dublować się 1:1 na kanale

## 8b. Ciasteczko z wróżbą

- Otworzyć `/setup` dla Fortune Cookie i potwierdzić, że cockpit pokazuje claims/7 dni, topkę puli i ostatni claim
- Ustawić globalną konfigurację `fortune_cookie` w Mongo (`allowed_guild_ids`, `allowed_channel_ids`, `cooldown_seconds`, `exempt_role_ids`) i potwierdzić, że `wr` respektuje ją bez zmian w `config/settings.py`
- Dodać override modułu `fortune_cookie` w `guild_settings` dla wybranego serwera i potwierdzić, że lokalny kanał oraz cooldown nadpisują profil globalny
- W modalu `Pula i obrazki` zawęzić lokalną pulę po ID/tekście i potwierdzić, że serwer losuje już tylko z tej selekcji
- Dodać 1–2 `Lokalne wróżby tylko dla tego serwera` i potwierdzić, że nie pojawiają się na innym guildzie
- Dodać kilka lokalnych URL-i obrazków i potwierdzić, że `wr` znów losuje obrazek nawet wtedy, gdy centralny dokument Fortune nie ma globalnej listy `images`
- Użyć przycisku `Profil globalny` i potwierdzić, że lokalny override znika, a widok wraca do efektywnego runtime globalnego
- Potwierdzić, że role z `exempt_role_ids` omijają cooldown po override per guild, a zwykły użytkownik nadal dostaje poprawny komunikat o pozostałym czasie
- Wykonać claim `wr` i potwierdzić, że telemetryka w setupie aktualizuje claims/7 dni oraz ostatni claim po odświeżeniu panelu

## 9. Restart safety

- Uruchomić aktywny Temp VC z panelem
- Uruchomić aktywne LFG i co najmniej jeden bell view
- Ręcznie zmienić nazwę aktywnego Daddy Voice`s przed restartem
- Zrestartować bota
- Potwierdzić rehydrację paneli i brak martwych interakcji
- Potwierdzić, że istniejący Daddy Voice`s wrócił na tym samym `voice_channel_id`, a bot nie utworzył duplikatu kanału
- Potwierdzić, że rename Daddy Voice`s nie zablokował recovery i bot nadal odzyskał ten sam kanał
- Potwierdzić odtworzenie panelu, jeśli kanał istnieje po restarcie, ale wiadomość panelu musiała zostać odtworzona
- Potwierdzić brak sierot w Mongo po reconcile
- Potwierdzić, że zwykła aktywna sesja voice po restarcie nadal zapisuje się ciągłym przebiegiem po wyjściu użytkownika z kanału
- Potwierdzić, że aktywna sesja kanału moderacyjnego po restarcie nadal zapisuje poprawny wpis `MOD_VOICE` po opuszczeniu kanału
- Potwierdzić, że karta dossier z `report_channel_ids` zachowuje działające przyciski po restarcie bez ręcznego resendowania wiadomości logu
- Potwierdzić auto-transfer do najdłużej obecnego członka albo cleanup po przekroczeniu progu nieobecności właściciela
- Potwierdzić, że stary aktywny Daddy Voice`s z poprzednim owner markerem nadal odzyskuje ownera po restarcie i migracji markera

## 10. Owner/admin fallback

- `sync` działa po zmianach slash commandów
- `metrics` działa po restarcie i nie wysypuje się w DM/guild
- `ban` uruchomiony z DM nie wywraca logowania kanału i zapisuje poprawny fallback `DM`
- `ye_ban` pokazuje preview owner-only, wymaga potwierdzenia i nie dubluje tych samych kandydatów między skanowanymi serwerami
- Slashowe `banlist`, `banupdate`, `statystyki` i `stats_range` przechodzą przez defer/followup bez timeoutu interaction i zwracają wynik bez legacy `ctx.send(..., ephemeral=True)`
- `list_servers`, `leave_server`, `banlist`, `banupdate`, `gr`, `sr`, `zapros`, `sc` działają jako owner fallback
- `godmode` przy wyłączonych DM nie publikuje panelu na kanale i zwraca tylko komunikat o konieczności włączenia prywatnych wiadomości
- `remove_warn` i `mass_remove_warn` odpalane z panelu Godmode nie wywracają się na brakującym `ctx.channel` i kończą się tym samym shared runtime co zwykły fallback warnów

## 11. Kryterium pilota

Pilot można uznać za gotowy dopiero gdy:

- wszystkie sekcje 1–9 przechodzą bez restartowych regresji
- owner fallback z sekcji 10 działa jako recovery path
- nie ma już krytycznych flow zależnych od pamięci procesu
- changelog, status i commandless audit są zaktualizowane po ostatniej serii zmian
