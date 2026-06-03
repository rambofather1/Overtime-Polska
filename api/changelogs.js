// Plik API dla Vercel Serverless Functions - Pobieranie Changelogów i Dokumentacji
const { MongoClient } = require('mongodb');
require('dotenv').config();

const MONGO_URI = process.env.MONGODB_URI;
const DATABASE = process.env.DATABASE_NAME || 'overtime';
const COLLECTION = process.env.COLLECTION_NAME || 'razem';

let cachedClient = null;
let cachedDb = null;

async function connectToDatabase() {
  if (cachedClient && cachedDb) {
    try {
      await cachedDb.command({ ping: 1 });
      return { client: cachedClient, db: cachedDb };
    } catch (e) {
      console.log('Połączenie z cache wygasło, nawiązywanie nowego...');
      cachedClient = null;
      cachedDb = null;
    }
  }

  const client = new MongoClient(MONGO_URI);
  await client.connect();
  
  const db = client.db(DATABASE);
  
  cachedClient = client;
  cachedDb = db;
  
  return { client, db };
}

module.exports = async (req, res) => {
  // Wyłączenie cache
  res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0, s-maxage=0');
  res.setHeader('Pragma', 'no-cache');
  res.setHeader('Expires', '0');
  res.setHeader('Surrogate-Control', 'no-store');

  // Obsługa CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, x-api-key');
  
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }
  
  try {
    const { db } = await connectToDatabase();
    const collection = db.collection(COLLECTION);

    const result = await collection.findOne({ _id: "changelogs" });
    
    res.status(200).json(result || {});
  } catch (error) {
    console.error('[Vercel API] Błąd podczas pobierania changelogów:', error);
    res.status(500).json({ 
      error: 'Wystąpił błąd podczas pobierania changelogów z bazy danych',
      message: error.message 
    });
  }
};
