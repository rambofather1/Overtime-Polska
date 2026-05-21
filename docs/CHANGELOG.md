# Changelog - Overtime Polska Portal & API

Główny rejestr zmian wprowadzanych w portalu internetowym oraz API dla sieci społecznościowej Overtime Polska.

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
