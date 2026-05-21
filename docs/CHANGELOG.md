# Changelog - Overtime Polska Portal & API

Główny rejestr zmian wprowadzanych w portalu internetowym oraz API dla sieci społecznościowej Overtime Polska.

## [Wersja 1.1.0] - 2026-05-21

### Dodano
- Nową, w pełni interaktywną podstronę dedykowaną dziennikowi zmian bota Ojciec: `changelog.html`.
- Nowy przycisk nawigacyjny "Changelog Bota" w menu głównym portalu `index.html`.
- Wyłączenie cache w przeglądarkach oraz po stronie silnika Vercel (nagłówki `Cache-Control` i `Surrogate-Control` w API serverless) dla zapewnienia stałego odświeżania na żywo.

### Zmieniono
- Podkręcono responsywność pobierania danych użytkowników live.
- Usunięto przestarzały, długoterminowy cache odpowiedzi w wątkach backendu PM2 (`api/db.js`) i Vercel API (`api/usercount.js`), który serwował nieaktualne statystyki użytkowników w przypadku spowolnienia bazy danych. API odpytuje teraz bezpośrednio MongoDB przy każdym zapytaniu, co gwarantuje 100% autentyczność stanu live licznika.
- Przebudowano sekcję największych serwerów na stronie głównej: zamiast prostych badge'y pojawił się czytelny, pionowo przewijany ranking topki (scroll box) z numeracją, paskami udziału i lepszą prezentacją nazw oraz liczby członków. Usunięto limit 8 serwerów – obecnie wyświetlana jest cała lista serwerów z bazy danych, dynamicznie dostosowując się do wysokości scroll boxa bez rozciągania layoutu.
- Naprawiono responsywność całej strony głównej (onepage): usunięto globalne blokowanie przewijania strony (`overflow: hidden`), zmieniono pozycjonowanie tła particles.js na `fixed`, wdrożono mobilny układ siatki kafli (sub-grid 2x2) dla przycisków społecznościowych oraz zachowano horyzontalny układ meta-informacji serwerów na małych rozdzielczościach, eliminując zniekształcenia widoku.

---
*Rejestr prowadzony zgodnie z wymaganiami technicznymi i standardami jakości zespołu Overtime Polska.*