import Document, { Html, Head, Main, NextScript } from 'next/document';

class MyDocument extends Document {
  render() {
    return (
      <Html lang="pl">
        <Head>
          <meta charSet="UTF-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1.0" />
          <meta name="description" content="Overtime Polska - Czekasz? To jeszcze sobie poczekasz. Overtime. Nam się nigdzie nie spieszy..." />
          <meta name="keywords" content="Overtime, Polska, gaming, turnieje, technologia, społeczność, Discord, YouTube" />
          <meta name="author" content="Overtime Polska" />

          {/* Open Graph Meta Tags */}
          <meta property="og:title" content="Overtime Polska - Gaming, Turnieje, Technologia" />
          <meta property="og:description" content="Overtime Polska - Centrum dla graczy, turnieje, technologia. Dołącz do nas na Linktree, YouTube i Discord." />
          <meta property="og:image" content="https://i.imgur.com/ZGkYfMv.png" />
          <meta property="og:url" content="https://overtime.community" />
          <meta property="og:type" content="website" />

          {/* Twitter Card Meta Tags */}
          <meta name="twitter:card" content="summary_large_image" />
          <meta name="twitter:title" content="Overtime Polska - Gaming, Turnieje, Technologia" />
          <meta name="twitter:description" content="Overtime Polska - Centrum dla graczy, turnieje, technologia. Dołącz do nas na Linktree, YouTube i Discord." />
          <meta name="twitter:image" content="https://i.imgur.com/ZGkYfMv.png" />
          <meta name="twitter:url" content="https://overtime.community" />

          <link rel="icon" href="/favicon.ico" />
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
        </Head>
        <body>
          <Main />
          <NextScript />
        </body>
      </Html>
    );
  }
}

export default MyDocument;
