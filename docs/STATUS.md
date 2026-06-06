# Zbiorczy Status Prac: Overtime Multiverse

Ten dokument śledzi status wdrożenia, checklistę postępu oraz wskaźniki gotowości operacyjnej dla całej sieci botów i usług Overtime.

---

## 🛰️ Architektura Sieci Proxmox (LXC Fleet)

Bieżący układ maszyn w klastrze Proxmox dla całego systemu:
* **VM 117 (`rf-gw-matka`)** — [PRIORYTET] Gateway bota Matka (ekonomia i social UX) [Status: `running` (zalogowany Matka#3189)]
* **VM 118 (`rf-wrk-matka`)** — [PRIORYTET] Worker asynchroniczny bota Matka (Arq / ekonomia) [Status: `running`]
* **VM 101 (`rf-mongodb`)** — Centralna baza danych MongoDB [Status: `running`]
* **VM 102 (`rf-redis`)** — Centralny broker eventów i pamięć podręczna Redis [Status: `running`]
* **VM 103 (`rf-rabbitmq`)** — Broker kolejkowy AMQP RabbitMQ dla Ojca [Status: `running`]
* **VM 104 (`rf-nginx`)** — Główny serwer Reverse Proxy (zarządzanie ruchem i SSL) [Status: `running`]
* **VM 106 (`rf-gw-ojciec`)** — Gateway / Sensor bota Ojciec (GATEWAY_ONLY) [Status: `running`]
* **VM 107 (`rf-exec-discord`)** — Discord Outbound REST Executor (Ojciec) [Status: `running`]
* **VM 108 (`rf-wrk-admin`)** — Worker administracyjny Ojca [Status: `running`]
* **VM 109 (`rf-wrk-voice`)** — Worker głosowy (voice tracker) Ojca [Status: `running`]
* **VM 110 (`rf-wrk-match`)** — Worker dopasowywania graczy LFG [Status: `running`]
* **VM 111 (`rf-api-cockpit`)** — Zunifikowane FastAPI & Web Dashboard (Ojciec + Matka) [Status: `running`]
* **VM 112 (`rf-jobs-ops`)** — Ops/Backup/Cykliczny Scheduler (Matka + Ojciec) [Status: `running` (Sanity Check OK)]
* **VM 115 (`rf-gitea-bunker`)** — Prywatny Gitea do przechowywania wrażliwych plików `.env` [Status: `running`]

---

## 📈 Statusy Podsystemów

### 1. Bot MATKA (v1.0.0)
* **Status:** [PRIORYTET OPERACYJNY] Uruchomiony produkcyjnie na Proxmox Fleet (CT 117 i CT 118). Zintegrowany z API na CT 111.
* **Technologia:** Python 3.12/3.14, `discord.py 2.6.4`, MongoDB, Redis Streams, ARQ.
* **Kluczowe mechaniki:** Dwuwarstwowa ekonomia (Overcoins + lokalne waluty), dynamiczna giełda serwerów, hybrid shop, questy, reputacja (social credit) & voice gating.
* **Integracja API:** Zintegrowany endpoint profilu hakerskiego (`GET/PUT /api/matka/profile`), panelu sklepu (`GET /api/matka/shops/{guild_id}`) oraz zakupów (`POST /api/matka/shops/{guild_id}/buy`).
* **Postęp:** `100%` (Zintegrowany z bazą danych i zsynchronizowany, bot zalogowany pomyślnie, 194 testy przechodzą pomyślnie).

### 2. Bot OJCIEC (v5.7.02)
* **Status:** Stabilny / Wdrożony na Proxmox Fleet (6 kontenerów LXC + 3 kontenery baz danych).
* **Technologia:** Python 3.12, `discord.py 2.7.1`, RabbitMQ (`aio-pika`), MongoDB (`motor`), Redis (`aioredis`).
* **Kluczowe mechaniki:** Dossier 360, Temp VC, LFG, system sporów MMR (Glicko-2), REST Executor, centralne sterowanie flotą (`fleet_control.sh`).
* **Postęp:** `100%` (Zero runtime crashes w klastrze, 40 zautomatyzowanych testów regresyjnych przechodzi pomyślnie).

### 3. PORTAL WWW OT POLSKA (v1.5.5)
* **Status:** Wdrożony produkcyjnie / Serwowany bezpośrednio przez FastAPI z kontenera `api-cockpit` (CT 111) oraz zsynchronizowany z lokalnym portem deweloperskim `8000`.
* **Technologia:** HTML5, CSS3 (Vanilla / Custom styling), JavaScript.
* **Kluczowe mechaniki:** Landing page, Metropolis Live (Spatial Clustering, 3D Canvas Pan/Zoom), Tactical HUD, logowanie SSO (Discord OAuth2), integracja z profilami i sklepem live (FastAPI + Mongo).
* **Postęp:** `100%` (W pełni połączony z centralną infrastrukturą i bazą MongoDB).

---

## 📝 Checklista Integracyjna (Multiverse v1.0)

- `[x]` Faza 1: Przygotowanie monorepo i migracja projektów w jedno miejsce
- `[x]` Faza 2: Unifikacja i reorganizacja dokumentacji (Status, Changelog, Readme)
- `[x]` Faza 3: Fuzja serwerów FastAPI w jeden proces na VM 111
- `[x]` Faza 4: Integracja zdarzeniowa (Ojciec jako sensor telemetrii -> Redis Stream -> Matka)
- `[x]` Faza 5: Uruchomienie, testy i walidacja zunifikowanej floty
- `[x]` Faza 6: Integracja Portalu z Centralnym API (SSO i Ekonomia Live)
- `[x]` Faza 7: Serwowanie Portalu z Kontenera API (Uproszczenie Hostingu & SSO)
- `[x]` Faza 8: Konsolidacja dokumentacji i synchronizacja portów localhost (3000 -> 8000)
