# Instrukcja konfiguracji Vercel

## 2.1. Utwórz konto na Vercel

1. Przejdź na [vercel.com](https://vercel.com)
2. Zarejestruj się - zalecane jest logowanie przez GitHub, aby łatwo integrować projekty

## 2.2. Importuj projekt do Vercel

1. Po zalogowaniu kliknij przycisk "Add New..." a następnie "Project"
2. W sekcji "Import Git Repository" znajdź i wybierz swoje repozytorium `overtime-polska`
3. Kliknij "Import"

## 2.3. Skonfiguruj projekt

Na ekranie konfiguracji projektu:

1. **Podstawowe ustawienia**:
   - Framework Preset: wybierz "Other" (ponieważ używamy czystego HTML/JS)
   - Build Command: pozostaw puste
   - Output Directory: pozostaw puste
   - Install Command: `npm install --prefix api`

2. **Zmienne środowiskowe**:
   Kliknij "Environment Variables" i dodaj następujące zmienne:
   
   | Nazwa | Wartość |
   |-------|---------|
   | MONGODB_URI | mongodb+srv://rambofather:Password123@cluster0.e3crjk5.mongodb.net/ |
   | DATABASE_NAME | overtime |
   | COLLECTION_NAME | razem |
   | API_KEY | 67553d23c8f8817ead5d0606 |

   > ⚠️ **Ważne**: Nie pokazuj nikomu tych danych! W repozytorium wyświetlane są zastępcze wartości.

3. Kliknij "Deploy"

## 2.4. Sprawdź status wdrożenia

1. Obserwuj postęp wdrożenia - Vercel pokaże logi procesu budowania
2. Po zakończeniu zobaczysz komunikat "Congratulations!"
3. Kliknij przycisk "Continue to Dashboard"

## 2.5. Testowanie wdrożonej aplikacji

1. W dashboard projektu znajdziesz link do wdrożonej strony (np. overtime-polska.vercel.app)
2. Otwórz link w przeglądarce i sprawdź:
   - Czy strona się ładuje
   - Czy dane są pobierane z MongoDB (licznik użytkowników)
   - Czy wszystkie elementy wizualne wyglądają prawidłowo

## 2.6. Dodawanie domeny niestandardowej (opcjonalne)

1. W dashboardzie projektu przejdź do zakładki "Settings" > "Domains"
2. Dodaj swoją domenę i postępuj zgodnie z instrukcjami konfiguracji DNS

## 2.7. Monitorowanie i rozwiązywanie problemów

1. Przejdź do sekcji "Monitoring" w dashboardzie projektu
2. Sprawdź zakładkę "Logs" aby zobaczyć logi z wykonania funkcji serverless
3. Funkcja API powinna być widoczna w zakładce "Functions"

W przypadku problemów:
- Sprawdź zmienne środowiskowe
- Zweryfikuj czy MongoDB Atlas pozwala na połączenia z dowolnego IP (0.0.0.0/0)
- Sprawdź logi w sekcji "Deployments"
