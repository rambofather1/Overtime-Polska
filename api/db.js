const { MongoClient } = require('mongodb');

// Pobieranie zmiennych środowiskowych z pliku .env
const MONGO_URI = process.env.MONGODB_URI;
const DATABASE = process.env.DATABASE_NAME || 'overtime';
const COLLECTION = process.env.COLLECTION_NAME || 'razem';

// Cache dla ostatniego wyniku w przypadku problemów z połączeniem
let lastResult = null;

/**
 * Pobiera liczbę użytkowników z bazy danych MongoDB
 */
async function getUserCount() {
  let client = null;
  
  try {
    // Połączenie z bazą danych
    client = new MongoClient(MONGO_URI);
    await client.connect();
    console.log('Połączono z bazą danych MongoDB Atlas');
    
    const db = client.db(DATABASE);
    const collection = db.collection(COLLECTION);
    
    // Pobierz dokument z liczbą użytkowników
    // Zakładamy, że dokument zawiera pole total_users lub totalUsers
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
  } finally {
    // Zamknij połączenie
    if (client) {
      await client.close();
      console.log('Zamknięto połączenie z bazą danych');
    }
  }
}

module.exports = { getUserCount };
