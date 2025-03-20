#!/bin/bash

# Utwórz katalog na logi jeśli nie istnieje
mkdir -p logs

# Uruchom aplikację z PM2
pm2 start ecosystem.config.js

# Sprawdź status
pm2 status

echo ""
echo "Serwer API działa w tle. Aby sprawdzić logi, użyj komendy:"
echo "pm2 logs overtime-api"
echo ""
echo "Aby zatrzymać serwer, użyj komendy:"
echo "pm2 stop overtime-api"
