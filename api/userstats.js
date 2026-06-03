// Plik API dla Vercel Serverless Functions - Statystyki Użytkownika
const { MongoClient } = require('mongodb');
require('dotenv').config();

const MONGO_URI = process.env.MONGODB_URI;
const DATABASE = process.env.DATABASE_NAME || 'overtime';

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
    // Sprawdź klucz API
    const apiKey = req.headers['x-api-key'];
    if (process.env.API_KEY && apiKey !== process.env.API_KEY) {
      return res.status(401).json({ error: 'Nieprawidłowy klucz API' });
    }

    const { userId } = req.query;
    if (!userId) {
      return res.status(400).json({ error: 'Brak parametru userId' });
    }

    const { db } = await connectToDatabase();
    const collection = db.collection('activity_events');

    // 1. Zliczanie wiadomości wysłanych przez użytkownika
    const messageCount = await collection.countDocuments({
      user_id: userId,
      category: 'message',
      event_type: 'MESSAGE_CREATE'
    });

    // 2. Najbardziej aktywny kanał (zarówno tekstowy jak i głosowy)
    const activeChannelResult = await collection.aggregate([
      { $match: { user_id: userId, channel_name: { $ne: null } } },
      { $group: { _id: "$channel_name", count: { $sum: 1 } } },
      { $sort: { count: -1 } },
      { $limit: 1 }
    ]).toArray();

    const activeChannel = activeChannelResult.length > 0 ? activeChannelResult[0]._id : "Brak danych";

    res.status(200).json({
      userId,
      messageCount,
      activeChannel
    });
  } catch (error) {
    console.error('[Vercel API] Błąd podczas pobierania statystyk użytkownika:', error);
    res.status(500).json({ 
      error: 'Wystąpił błąd podczas pobierania statystyk z bazy danych',
      message: error.message 
    });
  }
};
