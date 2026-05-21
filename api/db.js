const { MongoClient } = require('mongodb');

// Pobieranie zmiennych środowiskowych z pliku .env
const MONGO_URI = process.env.MONGODB_URI;
const DATABASE = process.env.DATABASE_NAME || 'overtime';
const COLLECTION = process.env.COLLECTION_NAME || 'razem';

/**
 * Pobiera liczbę użytkowników z bazy danych MongoDB (zawsze LIVE)
 */
async function getUserCount() {
  let client = null;
  
  try {
    // Połączenie z bazą danych
    client = new MongoClient(MONGO_URI);
    await client.connect();
    console.log('Połączono z bazą danych MongoDB Atlas (LIVE)');
    
    const db = client.db(DATABASE);
    const collection = db.collection(COLLECTION);
    
    // Pobierz dokument z liczbą użytkowników
    const result = await collection.findOne({});
    
    if (!result) {
      throw new Error('Nie znaleziono dokumentu z danymi w bazie');
    }
    
    return {
      totalCount: result.total_users || result.totalUsers || 0,
      voiceUsers: result.voice_users || result.voiceUsers || 0,
      connectedServers: result.connected_servers || 0,
      latencyMs: result.latency_ms || 0,
      shardCount: result.shard_count || 1,
      dataSource: 'MongoDB Atlas (Główna)',
      servers: (result.servers || []).map(s => ({
        ...s,
        member_count: s.members || s.member_count || 0,
        active_voice: s.active_voice || 0,
        boosts: s.boosts || 0,
        boost_tier: s.boost_tier || 0,
        vanity_code: s.vanity_code || null,
        is_partnered: s.is_partnered || false,
        is_verified: s.is_verified || false
      }))
    };
  } catch (error) {
    console.error('Błąd podczas pobierania danych z MongoDB Atlas:', error);
    throw error;
  } finally {
    // Zamknij połączenie
    if (client) {
      await client.close();
      console.log('Zamknięto połączenie z bazą danych');
    }
  }
}

module.exports = { getUserCount };
