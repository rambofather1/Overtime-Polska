# Changelog - Overtime Polska Portal & API

Główny rejestr zmian wprowadzanych w portalu internetowym oraz API dla sieci społecznościowej Overtime Polska.

## [Wersja 1.5.2] - 2026-05-29

### Naprawiono i Udoskonalono

- **Przywrócenie Sprawności Map Multiverse (`calculateOrbit`)**: Naprawiono błąd braku definicji funkcji `calculateOrbit` w pliku [community.html](community.html). Brak ten wywoływał błąd `ReferenceError` w konsoli i całkowicie paraliżował silnik renderowania postaci oraz telemetrycznych biesiadników na mapach Metropolis i Tawerny. Postacie pojawiają się teraz poprawnie i stabilnie wokół swoich stref.
- **Ultra-Innowacyjna Wydajność Przeciągania (Buttery Smooth Drag & Drop)**: Zoptymalizowano reakcję interfejsu na ruch myszą i gesty dotykowe. Narzut silnika tranzycji CSS (`transition: transform 0.1s ease;` na elemencie `#mapCanvas`) został dynamicznie wyłączony na czas trwania operacji przeciągania (drag) i włączony z powrotem przy zwolnieniu kursora/palca. Dzięki temu usunięto denerwujące lagowanie i opóźnienia mapy, nadając jej niespotykaną płynność charakterystyczną dla profesjonalnych silników gier.

## [Wersja 1.5.1] - 2026-05-21

### Naprawiono i Zabezpieczono

- **Krytyczny Błąd Składniowy w [community.html](community.html)**: Usunięto martwy, osierocony fragment kodu (`charObj.addEventListener`) w globalnej sekcji skryptu, który wywoływał błąd parsera JavaScript w przeglądarkach u klientów i całkowicie paraliżował działanie mapy cyberpunkowej.
- **Zabezpieczenie Inicjalizacji Ekonmicznej (Defensive Programming)**:
  - Przeniesiono stałe konfiguracyjne czarnego rynku (`DECK_UPGRADES` oraz `SECURITY_UPGRADES`) powyżej inicjalizacji zmiennej `playerInventory`, zapobiegając błędom referencji (ReferenceError) podczas pierwszego ładowania stanu.
  - Wprowadzono rygorystyczne walidacje i sanityzację danych wczytywanych z `localStorage` (`parsedCredits`, `parsedDeck`, `parsedSec`) chroniące przed wartościami `NaN` lub uszkodzeniem struktur zapisu przez użytkownika.
  - Zastosowano mechanizm clampowania indeksów (funkcje `Math.max`/`Math.min`) w funkcji `updateEconomyHUD()`, eliminując potencjalne błędy typu `TypeError: Cannot read properties of undefined` przy odpytywaniu właściwości z ulepszeń poziomu cyberdecku i zabezpieczeń ICE.

## [Wersja 1.5.0] - 2026-05-21

### Dodano

- **Silnik Żyjącej Symulacji Ekosystemu ("Extreme Level 2.0 Living Simulation")**:
  - **Rejestr Aktorów i Wykluczenie Flickerowania DOM**: Całkowicie zrezygnowano z destrukcji i ponownej kreacji węzłów DOM przy dynamicznej telemetrycznej synchronizacji. Wprowadzono pamięciowy rejestr `actorsRegistry = {}`, w którym istniejące postacie są płynnie aktualizowane, a ich położenie podlega gładkiej interpolacji CSS.
  - **Wandering Patrol State Engine (Płynny Spacer)**: Zaaimplementowano autonomiczny algorytm spacerowania. Co 4.5 sekundy losowe 35% postaci wychodzi na mały dynamiczny spacer wokół swoich terytorialnych stref biesiadnych. Podczas ruchu postacie kołyszą się fizycznie (`.actor--walking` powiązany z animacją `pixelBob`) i zmieniają kierunek zwrotu twarzy (flip horyzontalny po wektorze chodu).
  - **Skanujące Bezzałogowe Drony Patrolowe**: Metropolis i Gospoda otrzymały autonomiczne drony patrolowe (`.cyber-drone`) ze stałą rotacją wektorów lotu. Drony przemieszczają się bezgłośnie nad mapami, emitując pulsujący gradient błękitno-neonowego lasera skanera podsieciowego i symulując military-grade nadzór Netwatch.
  - **Dynamiczne Rozproszone Pozycjonowanie Orbitowe**: Stworzono fizycznie wierny algorytm orbitowania orbitalnego zoptymalizowany pod kątem eliminacji nakładania się mieszkańców ("kanapki"). Każdy mieszkaniec Metropolis i biesiadnik Tawerny jest rozpraszany wektorowo za pomocą funkcji trygonometrycznych w polarnej siatce współrzędnych i unikalnego jitteru wokół centralnych punktów stref.
  - **Geopolityczna Dominacja Megakorporacji**: Zaimplementowano dynamiczny system dominacji megakorporacji (Arasaka, Militech, Biotech Pharma, Network Syndicate) kontrolujących dzielnice handlowe, rozrywkowe i muzyczne na mapie. Podział terytorium odbywa się w czasie rzeczywistym na podstawie współczynnika aktywności telemetrycznej, boostów oraz wielkości serwerów w bazie danych, dynamicznie zmieniając branding wizualny i neonowe barwy dzielnic.
  - **Głęboka Cyber-Ekonomia i Sklep Czarnego Rynku**: Wprowadzono systemową walutę (Kredyty) zapisywaną lokalnie na urządzeniu gracza. Stworzono zintegrowany z konsolą CLI sklep z ulepszeniami (Cyberdecki zwiększające penetrację ICE oraz zapory ICE zwiększające poziom obrony przed potencjalnymi hakami intruzów).
  - **Zbrojne Polowanie na Grube Ryby (HVT Hunt)**: Użytkownicy o najdłuższym czasie aktywnej sesji głosowej są automatycznie oznaczani flagą systemu krytycznego ("HIGH-VALUE TARGET"), umożliwiając przejęcie bazy danych o wartości kilkukrotnie większej od standardowej.
  - **System Globalnych Anomalii Architekta (Blackwall Breach / Netwatch Raids)**: Wprowadzono losowy system incydentów sieciowych zmieniający globalny styl wizualny (czerwone syreny alarmowe, glitche, dynamiczna modyfikacja koloru cząsteczek particles.js na krwisty czerwony lub jaskrawy błękit). Anomalie wymuszają interwencję deszyfrującą całej społeczności poprzez CLI terminalu komendą `/DECODE` / `/STABILIZE` obniżającą skażenie sieci, nagradzaną potężnymi funduszami.
  - **Sensoryczna Macierz Nastrojów (Mood Matrix) & Cyber-Statusy**: Przetłumaczono statusy Discord (self-mute, self-deaf, streaming) na cyberpunkowe odpowiedniki (Neural Mute Active, Neural Deaf, Matrix Stream Online, Overloaded Central) połączone z dynamicznymi filtrami na awatarach postaci. Zaimplementowano słownik fraz neonowych tłumaczący gry w profilach na rasowe, klimatyczne cyber-monologi i losowo generowane dymki z matrixa na mapie.
  - **Kontrola Kamery Drag & Zoom**: Pełnoekranowe wsparcie przeciągania mapy oraz skalowania (Drag & Zoom myszką i touch/swipe dla urządzeń mobilnych) z dokładnym badgem zoomu chroniącym przed anomaliami ekranu mobilnego.

## [Wersja 1.4.0] - 2026-05-21

### Dodano

- **Głęboka Cyberpunkowa Interaktywność Wyglądu ("Interaction-First" / RPG Game Mechanics)**:
  - Wdrożono dedykowany, zaawansowany modal cyberpunkowego profilu postaci ([community.html](community.html)) wywoływany kliknięciem na dowolnego obywatela Metropolis lub biesiadnika w Gospodzie.
  - Wyświetlanie precyzyjnych szczegółów profilu: powiększony cyberpunkowy awatar z dynamiczną poświatą neonową, prawdziwym identyfikatorem NET (UID Discorda), bieżącą lokalizacją głosową oraz autentycznym statusem Discord (aktywności lub tryb czuwania sensorycznego).
  - **Mechanizm Cyber-Wszczepów**: Generator deterministycznych ulepszeń technologicznych (np. *Korteks Synaptyczny*, *Optyka Kiroshi MK4*, *Moduł Hakowania Satori*) wraz z poziomem integracji (39% - 99%) wyliczany na podstawie statycznego skrótu identyfikatora użytkownika.
  - **Retro Mini-Gra Hackerska**: Wbudowana gra tekstowa CLI (Retro Terminal v4.0.9). Gracz podejmuje próbę infiltracji cyberware mieszkańca przez łamanie trójbarierowej zapory ICE. System generuje klucz obejściowy, który gracz musi poprawnie wprowadzić do konsoli. Sukces lub porażka wywołują autentyczną dynamiczną reakcję postaci w postaci unoszących się dymków dialogowych w czasie rzeczywistym nad awatarem na mapie!
  - **Interaktywny System Toastów / Stawiania Drinków**: Opcjonalna cyberpunkowa akcja pozwalająca postawić mieszkańcowi jeden z klasycznych cyber-trunków (np. *Johnny Hand*, *Wściekły Cyber-Pies*, *Sake Arasaka Special*). Postacie natychmiastowo dziękują, wznosząc wirtualny toast zintegrowany z interfejsem dymków na globalnej planszy.
  - Ujednolicony i usprawniony mechanizm nasłuchu zdarzeń we frontendzie z zabezpieczeniami zapobiegającymi propagacji bąbelkowej kliknięć.

## [Wersja 1.3.0] - 2026-05-21

### Dodano

- **Prawdziwa wieloobszarowa telemetria postaci na żywo**:
  - Podstrona Tawerny Live ([tavern.html](tavern.html)) oraz Miasta Metropolis Live ([metropolis.html](metropolis.html)) zostały w pełni zintegrowane z tablicą `voice_users_detailed` z bazy MongoDB Atlas.
  - Zamiast statycznych, fikcyjnych postaci i losowych awataryzacji, portal generuje teraz **realnie przebywających na kanałach Discorda użytkowników**.
  - Każda postać otrzymuje swój **prawdziwy avatar Discorda**, poprawną nazwę wyświetlaną (`display_name`) oraz dynamicznie generowany dymek dialogowy lub toast opisujący grę, w którą aktualnie gra na bazie tablicy `user.activities`.
  - Przypisywanie biesiadników do konkretnych stołów w Tawernie oraz dzielnic w Metropolis ("gaming" -> Cyber Arena, "chill" -> Neon Lounge, "general" -> Central Plaza) odbywa się teraz w pełni dynamicznie na podstawie sklasyfikowanego przez bota typu kanału głosowego (`channel_type`).
  - Dodano bezpieczne funkcje escapowania kodu HTML (`escapeHtml`) w skryptach frontendu, zapobiegając atakom typu XSS przy renderowaniu metadanych użytkowników Discorda.
- **Analityka i Synergia Głosowa (Retention & Connection Analytics)**:
  - Zaimplementowano w pełni nieblokujący asynchroniczny moduł akumulowania i zapisu statystyk sesji (`voice_analytics`) u bota.
  - Wdrożono agregację statystyk **TOP 5 najlepszych synergii partnerskich** (`synergy_couples`), która bada wspólny czas spędzany na kanałach (z unikalnym alfabetycznym grupowaniem par użytkowników), oraz **TOP 5 rekordowych czasów sesji** (`all_time_longest_sessions`).
  - Zaktualizowano punkty dostępowe API (`api/usercount.js` oraz `api/db.js`) o precyzyjne potężne potoki agregacji MongoDB Atlas w celu udostępnienia tych statystyk dla całego systemu.

## [Wersja 1.2.0] - 2026-05-21

### Dodano

- Dynamiczne wskaźniki techniczne na żywo bezpośrednio z MongoDB bota:
  - **Liczba połączonych serwerów** (`connected_servers`).
  - **Liczba aktywnych shardów** (`shard_count`).
  - **Opóźnienie sieciowe bota (Ping API)** (`latency_ms`).
- Nowe atrybuty serwerów w sekcji "Największe serwery Overtime" na stronie głównej:
  - **Złote/fioletowe diamentowe odznaki boostów** (`boosts`, `boost_tier`) dla serwerów posiadających ulepszenia.
  - **Ikony weryfikacji i partnerstwa** (`is_verified` oraz `is_partnered`) przy nazwach serwerów.
  - **Dynamiczne linki szybkiego dołączenia** do serwerów wykorzystujące własny vanity URL (`vanity_code`).
- Mapowanie zaawansowanych właściwości statystycznych serwerów w API Express (`api/db.js`) i Vercel Function (`api/usercount.js`) z kompletnym, bezpiecznym zestawem fallbacków chroniących przed brakiem danych w starszych wpisach bazodanowych.

## [Wersja 1.1.0] - 2026-05-21

### Dodano

- Nową, w pełni interaktywną podstronę dedykowaną dziennikowi zmian bota Ojciec: `changelog.html`. Podstrona została wzbogacona o kompletną historię zmian od wersji 1.01 aż do rewolucyjnej, extreme odsłony 4.0.0.
- Nowy przycisk nawigacyjny "Changelog Bota" w menu głównym portalu `index.html`.
- Wyłączenie cache w przeglądarkach oraz po stronie silnika Vercel (nagłówki `Cache-Control` i `Surrogate-Control` w API serverless) dla zapewnienia stałego odświeżania na żywo.

### Zmieniono

- Podkręcono responsywność pobierania danych użytkowników live.
- Usunięto przestarzały, długoterminowy cache odpowiedzi w wątkach backendu PM2 (`api/db.js`) i Vercel API (`api/usercount.js`), który serwował nieaktualne statystyki użytkowników w przypadku spowolnienia bazy danych. API odpytuje teraz bezpośrednio MongoDB przy każdym zapytaniu, co gwarantuje 100% autentyczność stanu live licznika.
- Przebudowano sekcję największych serwerów na stronie głównej: zamiast prostych badge'y pojawił się czytelny, pionowo przewijany ranking topki (scroll box) z numeracją, paskami udziału i lepszą prezentacją nazw oraz liczby członków. Usunięto limit 8 serwerów – obecnie wyświetlana jest cała lista serwerów z bazy danych, dynamicznie dostosowując się do wysokości scroll boxa bez rozciągania layoutu.
- Naprawiono responsywność całej strony głównej (onepage): usunięto globalne blokowanie przewijania strony (`overflow: hidden`), zmieniono pozycjonowanie tła particles.js na `fixed`, wdrożono mobilny układ siatki kafli (sub-grid 2x2) dla przycisków społecznościowych oraz zachowano horyzontalny układ meta-informacji serwerów na małych rozdzielczościach, eliminując zniekształcenia widoku.
- Rozbudowano podstronę `changelog.html` o sekcje "Extreme 4.0.0" ze szczegółowym wyróżnieniem wizualnym (gradienty, efekty hover, ikony) dedykowaną potężnym zmianom w architekturze bota, nowym modułom telemetrycznym (Moderation Cockpit & Telemetry Backbone), auto-healingowi systemu kanałów głosowych (Daddy Voice's) oraz wzmocnionym zabezpieczeniom administratorskim (Board Guardrails).
- Wprowadzono system interaktywnych, rozwijanych szczegółów technicznych (przy użyciu nowoczesnych elementów `<details>` i `<summary>` oraz Font Awesome) dla każdej aktualizacji. Opisano tam realne niskopoziomowe zmiany w kodzie (np. asynchroniczny batching MongoDB, klasę uprawnień `VoicePermissionGuard`, parser interwałów timedelta, rotacyjny moduł logowania błędów `error_logger.py` oraz asynchroniczne pętle `tasks.Loop`).

---

*Rejestr prowadzony zgodnie z wymaganiami technicznymi i standardami jakości zespołu Overtime Polska.*
