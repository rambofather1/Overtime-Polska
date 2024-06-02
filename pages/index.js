// pages/index.js
import Head from 'next/head';
import Link from 'next/link';
import styles from '../styles/Home.module.css';

export default function Home() {
  return (
    <div className={styles.container}>
      <Head>
        <title>Overtime Polska - Gaming, Turnieje, Technologia</title>
        <meta name="description" content="Overtime Polska - Centrum dla graczy, turnieje, technologia. Dołącz do nas na Linktree, YouTube i Discord." />
        <meta name="keywords" content="Overtime Polska, gaming, technologia, turnieje, społeczność, Discord, YouTube" />
        <meta name="author" content="Overtime Polska" />
        <meta property="og:title" content="Overtime Polska - Gaming, Turnieje, Technologia" />
        <meta property="og:description" content="Overtime Polska - Centrum dla graczy, turnieje, technologia. Dołącz do nas na Linktree, YouTube i Discord." />
        <meta property="og:image" content="https://i.imgur.com/ZGkYfMv.png" />
        <meta property="og:url" content="https://overtime.community" />
        <meta property="og:type" content="website" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Overtime Polska - Gaming, Turnieje, Technologia" />
        <meta name="twitter:description" content="Overtime Polska - Centrum dla graczy, turnieje, technologia. Dołącz do nas na Linktree, YouTube i Discord." />
        <meta name="twitter:image" content="https://i.imgur.com/ZGkYfMv.png" />
        <meta name="twitter:url" content="https://overtime.community" />
      </Head>

      <main className={styles.main}>
        <img src="/logo.png" alt="Logo Overtime Polska" className={styles.logo} />
        <h1 className={styles.title}>Wkrótce</h1>
        <div className={styles.socialIcons}>
          <a href="https://linktr.ee/overtimepolska" target="_blank" rel="noopener noreferrer">
            <i className="fas fa-link"></i>
          </a>
          <a href="https://www.youtube.com/@Overtimepl" target="_blank" rel="noopener noreferrer">
            <i className="fab fa-youtube"></i>
          </a>
          <a href="https://discord.gg/lolpolska" target="_blank" rel="noopener noreferrer">
            <i className="fab fa-discord"></i>
          </a>
        </div>
      </main>
    </div>
  );
}
