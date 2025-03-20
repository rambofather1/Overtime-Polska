require('dotenv').config({ path: '../.env' });
const express = require('express');
const cors = require('cors');
const { getUserCount } = require('./db');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors({
  origin: '*', // Zezwalaj na dostęp z różnych źródeł dla celów testowych
  credentials: true
}));
app.use(express.json());

// Logowanie żądań
app.use((req, res, next) => {
  console.log(`[${new Date().toISOString()}] ${req.method} ${req.url}`);
  next();
});

// Obsługa statycznych plików z głównego katalogu (dla testów lokalnych)
app.use(express.static(path.join(__dirname, '..')));

// Endpoint do pobierania liczby użytkowników - obsługuje obie ścieżki
app.get(['/api/usercount', '/usercount'], async (req, res) => {
  try {
    // Logowanie informacji o żądaniu
    console.log('Otrzymano żądanie danych użytkowników');
    console.log('Headers:', req.headers);
    
    // Sprawdź klucz API (opcjonalne)
    const apiKey = req.headers['x-api-key'];
    if (process.env.API_KEY && apiKey !== process.env.API_KEY) {
      console.log('Nieprawidłowy klucz API:', apiKey);
      return res.status(401).json({ error: 'Nieprawidłowy klucz API' });
    }

    const data = await getUserCount();
    console.log('Pobrano dane:', data);
    
    // Dodaj timestamp ostatniej aktualizacji
    const result = {
      ...data,
      lastUpdated: new Date().toISOString()
    };
    
    res.json(result);
  } catch (error) {
    console.error('Błąd podczas pobierania danych:', error);
    res.status(500).json({ 
      error: 'Wystąpił błąd podczas pobierania danych',
      message: error.message 
    });
  }
});

// Obsługa nieznanych ścieżek
app.use((req, res) => {
  res.status(404).json({ error: 'Nie znaleziono zasobu' });
});

// Uruchomienie serwera
app.listen(PORT, () => {
  console.log(`Serwer API działa na porcie ${PORT}`);
  console.log(`Adres API: http://localhost:${PORT}/api/usercount`);
});
