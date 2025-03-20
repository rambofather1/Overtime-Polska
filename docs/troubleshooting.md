# Rozwiązywanie problemów z wdrożeniem

## Problemy z połączeniem do MongoDB

### Problem: API nie może połączyć się z bazą danych

**Rozwiązanie**:

1. Zaloguj się do [MongoDB Atlas](https://cloud.mongodb.com/)
2. Wybierz swój klaster
3. Przejdź do "Network Access" w menu po lewej stronie
4. Kliknij "ADD IP ADDRESS"
5. Wybierz "Allow Access from Anywhere" (dodaje 0.0.0.0/0)
6. Kliknij "Confirm"

### Problem: Błędy uwierzytelniania do MongoDB

**Rozwiązanie**:

1. Upewnij się, że podałeś prawidłowe dane w zmiennej MONGODB_URI w Vercel
2. Sprawdź, czy użytkownik ma uprawnienia do odczytu bazy danych
3. Możesz utworzyć nowego użytkownika w MongoDB Atlas:
   - Przejdź do "Database Access" w menu po lewej
   - Kliknij "ADD NEW DATABASE USER"
   - Ustaw Authentication Method na "Password"
   - Wprowadź nazwę użytkownika i hasło
   - W Database User Privileges wybierz "Read and write to any database"
   - Kliknij "Add User"

## Problemy z funkcją serverless na Vercel

### Problem: Funkcja zwraca błąd 500

**Rozwiązanie**:

1. Sprawdź logi funkcji w dashboardzie Vercel:
   - Przejdź do projektu > Deployments > Latest
   - Kliknij "Functions" i znajdź swoją funkcję
   - Kliknij na nią, aby zobaczyć szczegółowe logi

2. Dodaj więcej logów w funkcji serverless:
   - Dodaj `console.log()` w kluczowych miejscach kodu
   - Wdróż ponownie aplikację
   - Sprawdź logi po wywołaniu funkcji

### Problem: Funkcja działa lokalnie, ale nie na Vercel

**Rozwiązanie**:

1. Uruchom lokalne środowisko Vercel do testowania:
   ```bash
   npm install -g vercel
   cd /home/rambofather/Dokumenty/OT_Poland_main_WWW
   vercel dev
   ```

2. Sprawdź, czy istnieją różnice w zachowaniu między lokalnym a zdalnym środowiskiem

## Problemy z wyświetlaniem danych na stronie

### Problem: Strona ładuje się, ale dane nie są wyświetlane

**Rozwiązanie**:

1. Otwórz konsolę przeglądarki (F12) i sprawdź błędy
2. Sprawdź, czy żądanie do API jest wykonywane prawidłowo
3. Zweryfikuj odpowiedź API w zakładce Network w narzędziach deweloperskich

### Problem: Błędy CORS

**Rozwiązanie**:

Twoja konfiguracja już zawiera obsługę CORS, ale jeśli nadal występują problemy:

1. Upewnij się, że nagłówki CORS są prawidłowo ustawione w funkcji API:
   ```javascript
   res.setHeader('Access-Control-Allow-Origin', '*');
   res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
   res.setHeader('Access-Control-Allow-Headers', 'Content-Type, x-api-key');
   ```

2. W przypadku problemów z preflight requests, upewnij się że obsługa OPTIONS jest prawidłowa
