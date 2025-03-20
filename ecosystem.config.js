module.exports = {
  apps: [{
    name: "overtime-api",
    script: "./api/index.js",
    cwd: "/home/rambofather/Dokumenty/OT_Poland_main_WWW/",
    watch: true,
    env: {
      NODE_ENV: "production",
    },
    // Automatyczny restart po awarii
    autorestart: true,
    // Liczba prób restartu
    max_restarts: 10,
    // Logi
    output: './logs/output.log',
    error: './logs/error.log',
    // Utworzenie katalogu logs jeśli nie istnieje
    log_date_format: "YYYY-MM-DD HH:mm:ss"
  }]
};
