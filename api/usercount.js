// Plik API dla Vercel Serverless Functions
const { MongoClient } = require('mongodb');
require('dotenv').config();

// Pobieranie zmiennych środowiskowych
const MONGO_URI = process.env.MONGODB_URI;
const DATABASE = process.env.DATABASE_NAME || 'overtime';
const COLLECTION = process.env.COLLECTION_NAME || 'razem';

// Optymalizacja połączeń dla środowiska serverless
let cachedClient = null;
let cachedDb = null;

// Cache dla ostatniego wyniku w przypadku problemów z połączeniem
let lastResult = null;

async function connectToDatabase() {
  if (cachedClient && cachedDb) {
    return { client: cachedClient, db: cachedDb };
  }

  const client = new MongoClient(MONGO_URI);
  await client.connect();
  
  const db = client.db(DATABASE);
  
  cachedClient = client;
  cachedDb = db;
  
  return { client, db };
}

async function getUserCount() {
  try {
    // Dodanie większej ilości logów dla debugowania na Vercel
    console.log(`MongoDB URI: ${MONGO_URI ? 'Skonfigurowany' : 'BRAK!'}`);
    console.log(`Database: ${DATABASE}, Collection: ${COLLECTION}`);
    
    const { db } = await connectToDatabase();
    console.log('Połączono z bazą danych MongoDB Atlas');
    
    const collection = db.collection(COLLECTION);
    
    // Pobierz dokument z liczbą użytkowników
    const result = await collection.findOne({});
    
    if (!result) {
      throw new Error('Nie znaleziono dokumentu z danymi');
    }
    
    // Zapisz wynik do cache
    lastResult = {
      totalCount: result.total_users || result.totalUsers || 0,
      voiceUsers: result.voice_users || result.voiceUsers || 0,
      dataSource: 'MongoDB Atlas',
      servers: result.servers || []
    };
    
    return lastResult;
  } catch (error) {
    console.error('Błąd podczas łączenia z MongoDB:', error);
    
    // Jeśli mamy cache, zwróć go z oznaczeniem
    if (lastResult) {
      return { ...lastResult, fromCache: true };
    }
    
    // W przypadku braku cache, zwróć wartość domyślną
    return { 
      totalCount: 0, 
      voiceUsers: 0,
      error: error.message,
      fromBackup: true
    };
  }
}

module.exports = async (req, res) => {
  // Obsługa CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, x-api-key');
  
  // Logowanie
  console.log(`[Vercel API] Otrzymano żądanie: ${req.method} ${req.url}`);
  console.log('[Vercel API] Headers:', req.headers);
  
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }
  
  try {
    // Sprawdź klucz API (opcjonalne)
    const apiKey = req.headers['x-api-key'];
    if (process.env.API_KEY && apiKey !== process.env.API_KEY) {
      console.log('[Vercel API] Nieprawidłowy klucz API:', apiKey);
      return res.status(401).json({ error: 'Nieprawidłowy klucz API' });
    }

    const data = await getUserCount();
    console.log('[Vercel API] Pobrano dane:', data);
    
    // Dodaj timestamp ostatniej aktualizacji
    const result = {
      ...data,
      lastUpdated: new Date().toISOString()
    };
    
    res.status(200).json(result);
  } catch (error) {
    console.error('[Vercel API] Błąd podczas pobierania danych:', error);
    res.status(500).json({ 
      error: 'Wystąpił błąd podczas pobierania danych',
      message: error.message 
    });
  }
};
