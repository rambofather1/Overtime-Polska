# Zapewnienie live-telemetrii bota w discord.py 2.7.1
# Plik ten przechowuje strukturę przesyłu danych bota Ojciec 4.0 do bazy MongoDB Atlas (kolekcja 'razem').

import discord
from discord.ext import tasks, commands
import pymongo
import datetime
import logging
import asyncio

class TelemetryBackbone(commands.Cog):
    def __init__(self, bot):
        self.bot = bot
        # Instancjonowanie klienta pymongo i integracja nieblokująca poprzez run_in_executor
        # Podmień poniższy URI na swój poprawny Connection String
        self.mongo_uri = "mongodb+srv://user:password@cluster.mongodb.net/NazwaBazy?retryWrites=true&w=majority"
        self.client = pymongo.MongoClient(self.mongo_uri)
        self.db = self.client["overtime_polska"] # Nazwa bazy danych
        self.collection = self.db["razem"]       # Nazwa kolekcji wspólnej
        
        # Słownik do śledzenia aktywnych sesji głosowych na żywo {user_id: joined_time}
        self.active_sessions = {}
        
        # Start pętli telemetrycznej (odpytywanie co 15 sekund dla maksymalnej dynamiki)
        self.telemetry_pulse.start()

    def cog_unload(self):
        self.telemetry_pulse.cancel()

    def _sync_save(self, payload):
        """Synchroniczny zapis do bazy wywoływany w osobnym executorze, aby nie blokować pętli zdarzeń bota"""
        self.collection.update_one(
            {"_id": "global_telemetry"},
            {"$set": payload},
            upsert=True
        )

    def _sync_accumulate_voice(self, user_id, username, duration_minutes, partners=None):
        """Synchroniczna akumulacja minut głosowych użytkownika oraz synergii i sesji rekordowych"""
        # Spójny zapis bezpośrednio do kolekcji "razem", aby ułatwić agregację w API Node.js/Vercel
        analytics_collection = self.collection

        # 1. Zwykła inkrementacja czasu całkowitego
        analytics_collection.update_one(
            {"_id": "voice_analytics"},
            {
                "$inc": {f"duration.{user_id}": duration_minutes},
                "$set": {f"usernames.{user_id}": username}
            },
            upsert=True
        )

        # 2. Rekord najdłuższych sesji (all_time_longest_sessions)
        analytics_collection.update_one(
            {"_id": "voice_analytics"},
            {
                "$push": {
                    "all_time_longest_sessions": {
                        "$each": [{
                            "user_id": user_id,
                            "username": username,
                            "duration_minutes": duration_minutes,
                            "timestamp": datetime.datetime.now(datetime.timezone.utc).isoformat()
                        }],
                        "$sort": {"duration_minutes": -1},
                        "$slice": 100
                    }
                }
            },
            upsert=True
        )

        # 3. Kumulacja synergii partnerskich (synergy_couples)
        if partners:
            for p in partners:
                p_name = p["user_b_name"]
                t_mins = p["together_minutes"]
                
                # Zabezpieczenie przed samozaliczeniem i alfabetyczny porządek par
                user_a, user_b = sorted([username, p_name])
                
                # Próba inkrementacji istniejącej pary
                db_res = analytics_collection.update_one(
                    {"_id": "voice_analytics", "synergy_couples.user_a": user_a, "synergy_couples.user_b": user_b},
                    {"$inc": {"synergy_couples.$.together_minutes": t_mins}}
                )
                
                # Brak pary -> dodanie nowego elementu
                if db_res.matched_count == 0:
                    analytics_collection.update_one(
                        {"_id": "voice_analytics"},
                        {
                            "$push": {
                                "synergy_couples": {
                                    "user_a": user_a,
                                    "user_b": user_b,
                                    "together_minutes": t_mins
                                }
                            }
                        },
                        upsert=True
                    )

    @commands.Cog.listener()
    async def on_voice_state_update(self, member, before, after):
        """Mierzenie czasu sesji głosowej oraz wykrywanie partnerstw/synergii na żywo"""
        user_id = str(member.id)
        now = datetime.datetime.now(datetime.timezone.utc)
        
        # Wykrycie realnego opuszczenia kanału lub przełączenia na inny
        if before.channel is not None and (after.channel is None or before.channel.id != after.channel.id):
            joined_time = self.active_sessions.pop(user_id, None)
            if joined_time:
                duration = now - joined_time
                duration_minutes = round(duration.total_seconds() / 60.0, 1)
                
                if duration_minutes > 0.1:
                    # Wykrywanie partnerów, którzy dzielili kanał w tym samym czasie
                    partners = []
                    for other_member in before.channel.members:
                        if other_member.bot or other_member.id == member.id:
                            continue
                        other_id = str(other_member.id)
                        other_joined = self.active_sessions.get(other_id)
                        if other_joined:
                            # Wspólny czas na tym samym kanale
                            overlap_start = max(joined_time, other_joined)
                            overlap_duration = now - overlap_start
                            overlap_mins = round(overlap_duration.total_seconds() / 60.0, 1)
                            if overlap_mins > 0.1:
                                partners.append({
                                    "user_b_id": other_id,
                                    "user_b_name": other_member.display_name,
                                    "together_minutes": overlap_mins
                                })

                    loop = asyncio.get_event_loop()
                    await loop.run_in_executor(
                        None, 
                        self._sync_accumulate_voice, 
                        user_id, 
                        member.display_name, 
                        duration_minutes,
                        partners
                    )
            
        # Wykrycie dołączenia na kanał lub przełączenia na inny
        if after.channel is not None and (before.channel is None or before.channel.id != after.channel.id):
            self.active_sessions[user_id] = now

    def _classify_channel(self, channel_name):
        """Inteligentna klasyfikacja przeznaczenia kanału głosu na podstawie nazwy i emoji"""
        name_lower = channel_name.lower()
        gaming_keywords = ["game", "gra", "cs", "lol", "play", "🎮", "gaming", "valorant", "fifa", "rust", "mc", "minecraft"]
        chill_keywords = ["music", "muzyka", "radio", "chill", "🎵", "🎧", "pogaduchy", "lounge", "relaks", "pary"]
        
        if any(kw in name_lower for kw in gaming_keywords):
            return "gaming"
        if any(kw in name_lower for kw in chill_keywords):
            return "chill"
        return "general"

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
            detailed_voice_users = []

            # 2. Iterowanie po serwerach dla zbierania danych szczegółowych
            for guild in self.bot.guilds:
                total_users += guild.member_count or 0
                
                # Zliczanie osób w kanałach głosowych
                guild_voice_count = 0
                for vc in guild.voice_channels:
                    channel_members_count = len(vc.members)
                    guild_voice_count += channel_members_count
                    
                    # Logowanie szczegółowe każdego użytkownika na kanale
                    channel_type = self._classify_channel(vc.name)
                    for member in vc.members:
                        if member.bot:
                            continue
                        
                        # Pobieranie gier i innej aktywności
                        activities_list = []
                        for act in member.activities:
                            if act.type != discord.ActivityType.custom:
                                activities_list.append(act.name)
                        
                        # Data dołączenia z cache sesji głosowej (domyślnie teraz)
                        usr_joined = self.active_sessions.get(str(member.id))
                        joined_str = usr_joined.isoformat() if usr_joined else datetime.datetime.now(datetime.timezone.utc).isoformat()

                        avatar_url = str(member.display_avatar.url) if member.display_avatar else None

                        detailed_voice_users.append({
                            "user_id": str(member.id),
                            "username": member.name,
                            "display_name": member.display_name,
                            "avatar_url": avatar_url,
                            "guild_id": str(guild.id),
                            "guild_name": guild.name,
                            "channel_id": str(vc.id),
                            "channel_name": vc.name,
                            "channel_type": channel_type,
                            "joined_at": joined_str,
                            "activities": activities_list
                        })

                # Globalna suma głosowych użytkowników
                voice_users += guild_voice_count
                
                # Zabezpieczenie danych o partnerstwie/weryfikacji
                is_partnered = "PARTNERED" in guild.features
                is_verified = "VERIFIED" in guild.features
                
                # Zbieranie informacji o boostach
                boosts = guild.premium_subscription_count or 0
                boost_tier = guild.premium_tier
                
                # Pobranie spersonalizowanego odnośnika zaproszenia (Vanity URL)
                vanity_code = guild.vanity_url_code if "VANITY_URL" in guild.features else None
                
                # Sformatowanie ikony gildii
                icon_url = str(guild.icon.url) if guild.icon else None

                # Budowanie mapy pojedynczej gildii
                server_info = {
                    "id": str(guild.id),
                    "name": guild.name,
                    "members": guild.member_count or 0,
                    "active_voice": guild_voice_count,
                    "boosts": boosts,
                    "boost_tier": boost_tier,
                    "vanity_code": vanity_code,
                    "is_partnered": is_partnered,
                    "is_verified": is_verified,
                    "icon": icon_url
                }
                servers_data.append(server_info)

            # SORTOWANIE: Największe serwery idą na górę rankingu
            servers_data.sort(key=lambda s: s["members"], reverse=True)

            # 3. Zapisywanie potężnej i bogatej paczki danych do dokumentu w MongoDB Atlas (UPSERT)
            payload = {
                "total_users": total_users,
                "voice_users": voice_users,
                "connected_servers": connected_servers,
                "shard_count": shard_count,
                "latency_ms": latency_ms,
                "servers": servers_data,
                "voice_users_detailed": detailed_voice_users,
                "lastUpdated": datetime.datetime.now(datetime.timezone.utc)
            }

            # Wywołanie synchronicznej metody zapisu w executorze asyncio (nieblokujące dla bota)
            loop = asyncio.get_event_loop()
            await loop.run_in_executor(None, self._sync_save, payload)
            
            logging.info(f"[Telemetria] Pomyślnie zsynchronizowano dane live: {total_users} użytkowników na {connected_servers} serwerach. Aktywnych głosowo ze szczegółami: {len(detailed_voice_users)}.")

        except Exception as e:
            logging.error(f"[Telemetria Błąd] Nie udało się wysłać pakietu danych: {e}")

async def setup(bot):
    await bot.add_cog(TelemetryBackbone(bot))
