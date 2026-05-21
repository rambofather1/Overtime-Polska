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

async function connectToDatabase() {
  if (cachedClient && cachedDb) {
    try {
      // Szybki test czy połączenie działa (ping)
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

async function getUserCount() {
  try {
    // Dodanie większej ilości logów dla debugowania na Vercel
    console.log(`MongoDB URI: ${MONGO_URI ? 'Skonfigurowany' : 'BRAK!'}`);
    console.log(`Database: ${DATABASE}, Collection: ${COLLECTION}`);
    
    const { db } = await connectToDatabase();
    console.log('Połączono z bazą danych MongoDB Atlas');
    
    const collection = db.collection(COLLECTION);
    
    // Pobieramy nadrzędny dokument telemetrii
    let result = await collection.findOne({ _id: "global_telemetry" });
    if (!result) {
      // Fallback w razie braku migrowanego dokumentu
      result = await collection.findOne({ _id: { $ne: "voice_analytics" } });
    }
    
    if (!result) {
      throw new Error('Nie znaleziono dokumentu z danymi w bazie');
    }
    
    // Pobranie TOP 5 najlepszych synergii partnerskich z kolekcji live
    let topSynergies = [];
    try {
      topSynergies = await collection.aggregate([
        { $match: { _id: "voice_analytics" } },
        { $unwind: "$synergy_couples" },
        { $sort: { "synergy_couples.together_minutes": -1 } },
        { $limit: 5 },
        { $project: {
            _id: 0,
            partner_a: "$synergy_couples.user_a",
            partner_b: "$synergy_couples.user_b",
            duration: "$synergy_couples.together_minutes"
        }}
      ]).toArray();
    } catch (e) {
      console.warn("Błąd aggregacji synergii (prawdopodobnie brak jeszcze danych):", e.message);
    }

    // Pobranie rekordów najdłuższych sesji (największych biesiadników serwera)
    let topGamers = [];
    try {
      topGamers = await collection.aggregate([
        { $match: { _id: "voice_analytics" } },
        { $unwind: "$all_time_longest_sessions" },
        { $sort: { "all_time_longest_sessions.duration_minutes": -1 } },
        { $limit: 5 },
        { $project: {
            _id: 0,
            username: "$all_time_longest_sessions.username",
            user_id: "$all_time_longest_sessions.user_id",
            minutes: "$all_time_longest_sessions.duration_minutes"
        }}
      ]).toArray();
    } catch (e) {
      console.warn("Błąd aggregacji rekordowych sesji:", e.message);
    }
    
    return {
      totalCount: result.total_users || result.totalUsers || 0,
      voiceUsers: result.voice_users || result.voiceUsers || 0,
      voice_users_detailed: result.voice_users_detailed || [],
      connectedServers: result.connected_servers || 0,
      latencyMs: result.latency_ms || 0,
      shardCount: result.shard_count || 1,
      dataSource: 'MongoDB Atlas',
      top_synergies: topSynergies,
      top_gamers: topGamers,
      servers: (result.servers || []).map(s => ({
        ...s,
        member_count: s.members || s.member_count || 0,
        active_voice: s.active_voice || s.activeVoice || 0,
        boosts: s.boosts || 0,
        boost_tier: s.boost_tier || s.premium_tier || 0,
        vanity_code: s.vanity_code || s.vanityCode || null,
        is_partnered: s.is_partnered || false,
        is_verified: s.is_verified || false,
        icon: s.icon || null
      }))
    };
  } catch (error) {
    console.error('Błąd podczas pobierania danych z MongoDB:', error);
    throw error;
  }
}

module.exports = async (req, res) => {
  // Wyłączenie cache w przeglądarkach i na serwerach proxy Vercel
  res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0, s-maxage=0');
  res.setHeader('Pragma', 'no-cache');
  res.setHeader('Expires', '0');
  res.setHeader('Surrogate-Control', 'no-store');

  // Obsługa CORS
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type, x-api-key');
  
  // Logowanie
  console.log(`[Vercel API] Otrzymano żądanie: ${req.method} ${req.url}`);
  
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
    console.log('[Vercel API] Pobrano dane live:', data);
    
    // Dodaj timestamp ostatniej aktualizacji
    const result = {
      ...data,
      lastUpdated: new Date().toISOString()
    };
    
    res.status(200).json(result);
  } catch (error) {
    console.error('[Vercel API] Błąd podczas pobierania danych:', error);
    res.status(500).json({ 
      error: 'Wystąpił błąd podczas pobierania danych z bazy danych',
      message: error.message 
    });
  }
};
