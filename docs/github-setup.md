# Instrukcja konfiguracji GitHub

## 1.1. Utwórz konto na GitHub

Jeśli jeszcze nie masz konta, przejdź na [github.com](https://github.com) i zarejestruj się.

## 1.2. Utwórz nowe repozytorium

1. Kliknij przycisk "+" w prawym górnym rogu, a następnie "New repository"
2. Podaj nazwę repozytorium (np. `overtime-polska`)
3. Opcjonalnie dodaj opis
4. Wybierz opcję "Public" lub "Private" (zalecane Private dla kodu zawierającego klucze)
5. Nie zaznaczaj opcji inicjalizacji plików README, .gitignore ani licencji
6. Kliknij "Create repository"

## 1.3. Przygotuj lokalny katalog projektu

Zanim wypchniesz kod do GitHub, upewnij się, że plik `.gitignore` jest prawidłowy:

```bash
cd /home/rambofather/Dokumenty/OT_Poland_main_WWW
cat .gitignore
```

## 1.4. Inicjalizuj repozytorium i dodaj pliki

```bash
# Inicjalizacja repozytorium Git
git init

# Dodanie wszystkich plików (z uwzględnieniem .gitignore)
git add .

# Pierwsze zatwierdzenie zmian
git commit -m "Inicjalna wersja Overtime Polska"

# Ustawienie gałęzi głównej jako 'main'
git branch -M main
```

## 1.5. Powiąż lokalne repozytorium z GitHub

```bash
# Zastąp {TWOJA_NAZWA_UŻYTKOWNIKA} swoją nazwą użytkownika GitHub
git remote add origin https://github.com/{TWOJA_NAZWA_UŻYTKOWNIKA}/overtime-polska.git

# Wypchnij zmiany do GitHub
git push -u origin main
```

## 1.6. Sprawdź czy kod został poprawnie wypchnięty

1. Odśwież stronę repozytorium na GitHub
2. Powinieneś widzieć wszystkie pliki projektu (bez plików wykluczonych przez .gitignore)
