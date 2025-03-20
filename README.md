# Overtime Polska - Licznik użytkowników

## Instalacja i pierwsze uruchomienie

1. Zainstaluj zależności:
```bash
cd api
npm install
```

2. Zainstaluj PM2 (menedżer procesów):
```bash
./install-pm2.sh
```

3. Uruchom serwer API jako usługę w tle:
```bash
./start-api.sh
```

## Zarządzanie serwerem API

- **Status serwera:**
  ```bash
  pm2 status
  ```

- **Podgląd logów:**
  ```bash
  pm2 logs overtime-api
  ```

- **Zatrzymanie serwera:**
  ```bash
  pm2 stop overtime-api
  ```

- **Ponowne uruchomienie:**
  ```bash
  pm2 restart overtime-api
  ```

## Automatyczne uruchamianie po starcie systemu

Wykonaj komendę (wymagane uprawnienia administratora):
```bash
pm2 startup
```
Następnie wykonaj polecenie, które wyświetli PM2 (z wcześniejszej komendy)
```bash
pm2 save
```

## Konfiguracja

Połączenie z bazą danych i inne ustawienia znajdują się w pliku `.env`. Upewnij się, że plik ten zawiera prawidłowe dane dostępowe do MongoDB.

## Hosting na Vercel

### Przygotowanie
1. Załóż konto na [GitHub](https://github.com) i [Vercel](https://vercel.com)
2. Utwórz repozytorium na GitHub i wypchnij tam kod projektu
3. Zaloguj się do Vercel i wybierz "New Project"
4. Importuj projekt z GitHub
5. Podczas konfiguracji projektu, dodaj zmienne środowiskowe:
   - `MONGODB_URI` - adres połączenia do MongoDB Atlas
   - `DATABASE_NAME` - nazwa bazy danych (domyślnie "overtime")
   - `COLLECTION_NAME` - nazwa kolekcji (domyślnie "razem")
   - `API_KEY` - klucz API (np. "67553d23c8f8817ead5d0606")
6. Kliknij "Deploy" i poczekaj na zakończenie wdrożenia

### Co oferuje Vercel?
- Automatyczne wdrożenia po każdym push do repozytorium
- HTTPS i własna domena
- CDN dla szybkiego ładowania
- Funkcje serverless dla API
- Darmowy hosting dla projektów niekomercyjnych

## Struktura projektu

- `index.html` - Główna strona z licznikiem użytkowników
- `/api` - Folder zawierający funkcje serverless dla Vercel
  - `usercount.js` - API do pobierania liczby użytkowników
- `.env` - Zmienne środowiskowe (nie wersjonowane)
- `vercel.json` - Konfiguracja deploymentu na Vercel

## Testowanie lokalne

Możesz testować projekt lokalnie przed wdrożeniem:

1. Zainstaluj Vercel CLI:
```bash
npm i -g vercel
```

2. Uruchom projekt lokalnie:
```bash
vercel dev
```

Aplikacja będzie dostępna pod adresem http://localhost:3000
