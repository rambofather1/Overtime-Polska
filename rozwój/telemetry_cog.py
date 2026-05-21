# Zapewnienie live-telemetrii bota w discord.py 2.7.1
# Plik ten przechowuje strukturę przesyłu danych bota Ojciec 4.0 do bazy MongoDB Atlas (kolekcja 'razem').

import discord
from discord.ext import tasks, commands
import motor.motor_asyncio
import datetime
import logging

class TelemetryBackbone(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
        # Instancjonowanie asynchronicznego klienta MongoDB Atlas
        # Podmień poniższy URI na swój poprawny Connection String
        self.mongo_uri = "mongodb+srv://user:password@cluster.mongodb.net/NazwaBazy?retryWrites=true&w=majority"
        self.client = motor.motor_asyncio.AsyncIOMotorClient(self.mongo_uri)
        self.db = self.client["overtime_polska"] # Nazwa bazy danych
        self.collection = self.db["razem"]       # Nazwa kolekcji wspólnej
        
        # Start pętli telemetrycznej (odpytywanie co 15 sekund dla maksymalnej dynamiki)
        self.telemetry_pulse.start()

    def cog_unload(self):
        self.telemetry_pulse.cancel()

    @tasks.loop(seconds=15)
    async def telemetry_pulse(self):
        try:
            await self.bot.wait_until_ready()
            
            # 1. Obliczanie statystyk ogólnych bota
            total_users = 0
            voice_users = 0
            connected_servers = len(self.bot.guilds)
            shard_count = self.bot.shard_count or 1
            latency_ms = self.bot.latency * 1000 # Latency w milisekundach
            
            servers_data = []

            # 2. Iterowanie po serwerach dla zbierania danych szczegółowych
            for guild in self.bot.guilds:
                total_users += guild.member_count or 0
                
                # Liczenie osób na kanałach głosowych tej konkretnej gildii
                guild_voice = sum(len(vc.members) for vc in guild.voice_channels)
                voice_users += guild_voice
                
                # Zabezpieczenie danych o partnerstwie/weryfikacji
                is_partnered = "PARTNERED" in guild.features
                is_verified = "VERIFIED" in guild.features
                
                # Zbieranie informacji o boostach
                boosts = guild.premium_subscription_count or 0
                boost_tier = guild.premium_tier
                
                # Pobranie spersonalizowanego odnośnika zaproszenia (Vanity URL)
                vanity_code = guild.vanity_url_code if "VANITY_URL" in guild.features else None

                # Budowanie mapy pojedynczej gildii
                server_info = {
                    "id": str(guild.id),
                    "name": guild.name,
                    "members": guild.member_count or 0,
                    "active_voice": guild_voice,
                    "boosts": boosts,
                    "boost_tier": boost_tier,
                    "vanity_code": vanity_code,
                    "is_partnered": is_partnered,
                    "is_verified": is_verified
                }
                servers_data.append(server_info)

            # SORTOWANIE: Największe serwery idą na górę rankingu
            servers_data.sort(key=lambda s: s["members"], reverse=True)

            # 3. Zapisywanie potężnej i bogatej paczki danych do dokumentu w MongoDB Atlas (UPSERT)
            # Używamy jednego centralnego rekordu o unikalnym identyfikatorze, aby ułatwić odpytywanie API
            payload = {
                "total_users": total_users,
                "voice_users": voice_users,
                "connected_servers": connected_servers,
                "shard_count": shard_count,
                "latency_ms": latency_ms,
                "servers": servers_data,
                "lastUpdated": datetime.datetime.now(datetime.timezone.utc)
            }

            await self.collection.update_one(
                {"_id": "global_telemetry"},
                {"$set": payload},
                upsert=True
            )
            logging.info(f"[Telemetria] Pomyślnie zsynchronizowano dane live: {total_users} użytkowników na {connected_servers} serwerach.")

        except Exception as e:
            logging.error(f"[Telemetria Błąd] Nie udało się wysłać pakietu danych: {e}")

async def setup(bot):
    await bot.add_cog(TelemetryBackbone(bot))
