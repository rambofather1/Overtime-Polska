# Walkthrough wdrażania OJCIEC 4.0

Ten dokument opisuje, jak rozwijać repo w modelu **OJCIEC 4.0** tak, żeby nowe moduły były łatwe do wdrożenia, testowania i łączenia z innymi.

## 0. Nadrzędna agenda repo

To jest stała zasada pracy nad OJCIEC 4.0, niezależnie od konkretnego tasku.

- Repo ma iść w stronę **interaction-first**: zwykły użytkownik ma klikać, wybierać i wypełniać workflow, a nie wpisywać kolejne komendy.
- Każdy nowy lub przebudowywany interfejs powinien maksymalnie wykorzystywać to, co realnie daje `discord.py 2.7.1`: `LayoutView`, `Container`, `Section`, modale, selecty, buttony, Radio/Checkbox tam, gdzie są stabilne i wspierane.
- Jeśli komenda zostaje, to głównie jako **hybrydowy opener** do bardziej rozbudowanego menu, panelu lub modala. Prefix command nie jest docelowym UX dla zwykłego użytkownika.
- Nie dokładamy „jeszcze jednej komendy”, jeśli ten sam flow można sensownie zamknąć przez panel, modal, context menu albo setup per guild.
- Po każdym etapie aktualizujemy `docs/CHANGELOG.md`, `docs/STATUS.md`, todo oraz odpowiedni dokument projektowy.
- Każdy etap kończy się dopisaniem do `docs/STATUS.md` dwóch liczb: aktualnej oceny realnego procentu całkowitego postępu oraz szacunku pozostałych roboczogodzin.

W praktyce oznacza to, że priorytetem nie jest już samo „feature działa”, tylko: **feature działa, wygląda premium, jest stabilny po restarcie i nie wymaga komend tam, gdzie nie musi**.

## 1. Najpierw decyzja domenowa

Zanim powstanie kod, ustal:

- jaki problem rozwiązuje moduł,
- jaki ma model danych,
- czy jest lokalny per guild, czy sieciowy,
- jakie ma zależności od innych modułów,
- czy wymaga audit trail, RBAC lub integracji z dashboardem.

Jeśli zmiana jest większa architektonicznie, zaktualizuj najpierw odpowiedni plik w folderze [rozwój/](../rozwój/README.md).

## 2. Kontrakt danych i konfiguracji

Nowy moduł nie powinien hardkodować wyjątków per serwer w logice. Najpierw ustal kontrakt:

- pola Mongo,
- indeksy,
- eventy Redis,
- konfigurację per guild,
- feature flags.

W OJCIEC 4.0 identyfikatory Discorda traktujemy jako **string** na granicy zapisu do bazy i API.

## 3. Warstwa serwisowa

Większość logiki biznesowej powinna wejść do `services/`.

Dobry moduł serwisowy:

- nie zna szczegółów konkretnego embeda,
- nie miesza się z routingiem slash/prefix,
- da się testować z mockiem Mongo i Redis,
- może być użyty przez kilka cogów albo później przez API.

Przykład docelowego wzorca:

- `services/temp_vc_service.py`
- `services/lfg_service.py`
- `services/audit_service.py`

## 4. Warstwa UI i interakcji

Jeżeli moduł używa hybryd, przycisków, modali albo persistent views, korzystaj z foundation:

- `core/interaction_responses.py`
- `core/interaction_registry.py`

Zasady:

- zwykłe odpowiedzi hybryd kieruj przez `send_context_response(...)`
- odpowiedzi wrażliwe kieruj przez `send_private_context_response(...)`
- persistent views rejestruj przez registry zamiast robić lokalny bootstrap w każdym cogu
- `custom_id` projektuj tak, żeby dało się je utrzymać przy wielu panelach i restartach
- dla ekranów dynamicznych preferuj **`Embed + View`**, a `LayoutView` bierz wtedy, gdy faktycznie poprawia hierarchię i czytelność zamiast komplikować payload
- jeśli flow robi się zbyt szeroki, przechodź na **progressive disclosure**: grupa akcji głównych + osobna grupa akcji zaawansowanych, zamiast ściany komponentów w jednym ekranie
- nowy UI ma być projektowany mobile-first; desktop jest ważny, ale nie może być jedynym punktem odniesienia

## 5. Cienki cog jako wejście

Cog ma być warstwą wejścia, nie całym silnikiem modułu.

Preferowana odpowiedzialność coga:

- odbiór eventu,
- wejście slash/hybrid/context menu,
- walidacja kontekstu Discord,
- delegacja do serwisu,
- render odpowiedzi.

Nie wkładaj do coga:

- dużej logiki biznesowej,
- modelu danych,
- kolejki i locków rozproszonych,
- złożonego łączenia wielu kolekcji.

## 6. Jak dodawać nowy moduł

Minimalna ścieżka wygląda tak:

1. aktualizacja dokumentu w `rozwój/`, jeśli moduł zmienia architekturę,
2. dodanie serwisu w `services/`,
3. dodanie coga w `cogs/<obszar>/`,
4. decyzja, czy UX ma być commandless, hybrydowym openerem workflow, czy tylko owner/admin fallbackiem,
5. podpięcie do `core/bot.py`,
6. jeśli potrzeba persistent views: rejestracja rehydratora,
7. test kompilacji,
8. aktualizacja `docs/CHANGELOG.md`, `README.md` i tego walkthrough.

Jeśli moduł dodaje lub usuwa komendy, zaktualizuj też [docs/COMMANDLESS_AUDIT.md](COMMANDLESS_AUDIT.md).
Jeśli zmiana dotyka user-facing workflow, zaktualizuj też [docs/PILOT_CHECKLIST.md](PILOT_CHECKLIST.md).

## 7. Integracja między modułami

Nowy moduł powinien być od razu projektowany pod integrację, nie jako samotny feature.

Przykłady:

- temp VC może uruchamiać LFG,
- LFG może emitować event do dashboardu,
- moderacja może wpisywać zdarzenia do kartoteki i audytu,
- dashboard może czytać ten sam model danych co bot.

Dlatego preferuj:

- wspólny model identyfikatorów,
- wspólny audit,
- spójne eventy Redis,
- wspólny styl konfiguracji per guild.

## 8. Rollout

Każdy moduł user-facing wdrażaj etapami:

1. serwer testowy,
2. jeden serwer pilotażowy,
3. mała część sieci,
4. reszta sieci.

Do tego potrzebne są feature flags per guild oraz prosty rollback bez zmiany kodu produkcyjnego.

## 9. Dokumenty, które zawsze aktualizujemy

Po każdym etapie aktualizujemy:

1. [docs/CHANGELOG.md](CHANGELOG.md)
2. [docs/STATUS.md](STATUS.md)
3. aktywną listę todo dla bieżącego sprintu lub zadania
4. [docs/COMMANDLESS_AUDIT.md](COMMANDLESS_AUDIT.md), jeśli zmienia się mapa komend lub workflow commandless
5. [docs/PILOT_CHECKLIST.md](PILOT_CHECKLIST.md), jeśli zmieniają się krytyczne flow user-facing
6. [docs/RUNBOOK.md](RUNBOOK.md), jeśli zmienia się procedura backup/restore, rollout albo obsługa incydentów
7. [README.md](../README.md), jeśli zmienia się punkt wejścia do repo albo sposób użycia
8. [docs/WALKTHROUGH.md](WALKTHROUGH.md), jeśli zmienia się sposób pracy z repo
9. odpowiedni plik w [rozwój/](../rozwój/README.md), jeśli zmiana dotyka architektury albo roadmapy

## 10. Minimum przed merge

- kod się kompiluje,
- wrażliwe odpowiedzi nie wyciekają na kanał,
- identyfikatory Discorda są spójne,
- moduł nie łamie restartu i bootstrapu,
- wpis do changeloga istnieje,
- `docs/STATUS.md` i todo odzwierciedlają nowy stan prac,
- walkthrough i README są aktualne, jeśli zmienił się sposób pracy z repo.
- `docs/STATUS.md` zawiera świeżą ocenę procentu postępu i pozostałych roboczogodzin po danym etapie.