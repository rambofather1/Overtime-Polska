#!/bin/bash
set -e

echo "=== Overtime Polska - Skrypt wdrażający ==="
echo ""

# Sprawdź, czy git jest zainstalowany
if ! command -v git &> /dev/null; then
    echo "❌ Git nie jest zainstalowany. Zainstaluj git i spróbuj ponownie."
    exit 1
fi

# Sprawdź, czy jesteśmy w repozytorium git
if [ ! -d .git ]; then
    echo "❌ Nie jesteś w repozytorium git. Najpierw wykonaj 'git init'."
    exit 1
fi

# Pobierz aktualną gałąź
BRANCH=$(git rev-parse --abbrev-ref HEAD)
if [ "$BRANCH" != "main" ]; then
    echo "⚠️ Nie jesteś na gałęzi 'main'. Aktualnie na: $BRANCH"
    read -p "Czy chcesz kontynuować? (t/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Tt]$ ]]; then
        exit 1
    fi
fi

# Sprawdź status repozytorium
echo "🔍 Sprawdzanie zmian w repozytorium..."
git status -s

# Potwierdź wprowadzenie zmian
echo ""
read -p "Czy chcesz dodać wszystkie zmiany i wdrożyć? (t/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Tt]$ ]]; then
    exit 1
fi

# Dodaj wszystkie zmiany
echo "📦 Dodawanie zmian..."
git add .

# Commit zmian
echo "✏️ Wprowadzanie zmian do repozytorium..."
read -p "Podaj opis zmian: " COMMIT_MSG
if [ -z "$COMMIT_MSG" ]; then
    COMMIT_MSG="Aktualizacja $(date +"%Y-%m-%d %H:%M")"
fi
git commit -m "$COMMIT_MSG"

# Push do GitHub
echo "🚀 Wypychanie zmian do GitHub..."
git push origin $BRANCH

echo ""
echo "✅ Kod został pomyślnie wypchnięty do GitHub."
echo "🔄 Jeśli masz skonfigurowane automatyczne wdrażanie w Vercel, zmiany będą"
echo "   automatycznie wdrożone po weryfikacji przez Vercel."
echo ""
echo "📊 Monitoruj status wdrożenia na: https://vercel.com/dashboard"
echo ""
echo "🔗 Twoja strona będzie dostępna pod adresem, który widzisz w panelu Vercel."
echo "   Zwykle jest to: https://overtime-polska.vercel.app"
echo ""
echo "⚠️ Pamiętaj, że pierwsze wdrożenie może zająć kilka minut."
echo ""
echo "🎉 Gotowe!"
