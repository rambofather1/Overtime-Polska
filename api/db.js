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
    let result = await collection.findOne({ _id: "global_telemetry" });
    if (!result) {
      result = await collection.findOne({ _id: { $ne: "voice_analytics" } });
    }
    
    if (!result) {
      throw new Error('Nie znaleziono dokumentu z danymi w bazie');
    }

    // Pobranie TOP 5 najlepszych synergii partnerskich
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
      console.warn("Błąd aggregacji synergii w local API:", e.message);
    }

    // Pobranie rekordów najdłuższych sesji
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
      console.warn("Błąd aggregacji rekordowych sesji w local API:", e.message);
    }
    
    return {
      totalCount: result.total_users || result.totalUsers || 0,
      voiceUsers: result.voice_users || result.voiceUsers || 0,
      voice_users_detailed: result.voice_users_detailed || [],
      connectedServers: result.connected_servers || 0,
      latencyMs: result.latency_ms || 0,
      shardCount: result.shard_count || 1,
      dataSource: 'MongoDB Atlas (Główna)',
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

async function getUserStats(userId) {
  let client = null;
  try {
    client = new MongoClient(MONGO_URI);
    await client.connect();
    
    const db = client.db(DATABASE);
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

    return {
      userId,
      messageCount,
      activeChannel
    };
  } catch (error) {
    console.error('Błąd podczas pobierania statystyk z MongoDB:', error);
    throw error;
  } finally {
    if (client) {
      await client.close();
    }
  }
}

async function getChangelogs() {
  let client = null;
  try {
    client = new MongoClient(MONGO_URI);
    await client.connect();
    
    const db = client.db(DATABASE);
    const collection = db.collection(COLLECTION);
    
    const result = await collection.findOne({ _id: "changelogs" });
    return result;
  } catch (error) {
    console.error('Błąd podczas pobierania changelogów z MongoDB:', error);
    throw error;
  } finally {
    if (client) {
      await client.close();
    }
  }
}

module.exports = { getUserCount, getUserStats, getChangelogs };
