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

---
*Rejestr prowadzony zgodnie z wymaganiami technicznymi i standardami jakości zespołu Overtime Polska.*