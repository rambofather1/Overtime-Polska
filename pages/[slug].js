// pages/[slug].js
import { useRouter } from 'next/router';
import Head from 'next/head';
import styles from '../styles/Home.module.css';

const Page = () => {
  const router = useRouter();
  const { slug } = router.query;

  return (
    <div className={styles.container}>
      <Head>
        <title>{slug} - Overtime Polska</title>
        <meta name="description" content={`${slug} - Overtime Polska`} />
        <meta name="keywords" content="Overtime Polska, gaming, technologia, turnieje, społeczność, Discord, YouTube" />
        <meta property="og:title" content={`${slug} - Overtime Polska`} />
        <meta property="og:description" content={`${slug} - Overtime Polska - Centrum dla graczy, turnieje, technologia.`} />
        <meta property="og:image" content="https://i.imgur.com/ZGkYfMv.png" />
        <meta property="og:url" content={`https://overtime.community/${slug}`} />
        <meta property="og:type" content="article" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content={`${slug} - Overtime Polska`} />
        <meta name="twitter:description" content={`${slug} - Overtime Polska - Centrum dla graczy, turnieje, technologia.`} />
        <meta name="twitter:image" content="https://i.imgur.com/ZGkYfMv.png" />
        <meta name="twitter:url" content={`https://overtime.community/${slug}`} />
      </Head>

      <main className={styles.main}>
        <h1 className={styles.title}>{slug}</h1>
      </main>
    </div>
  );
};

export default Page;
