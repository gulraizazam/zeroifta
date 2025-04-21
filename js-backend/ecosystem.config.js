module.exports = {
    apps: [
      {
        name: "ZeroIFTA JS Backend - SK",
        script: "./server.js",
        env: {
          PORT: 3000,
          NODE_ENV: "production",
          DB_HOST: "localhost",
          DB_DATABASE: "zeroifta", 
          DB_USERNAME: "root",
          DB_PASSWORD: "",
          GOOGLE_MAPS_API_KEY: "AIzaSyA0HjmGzP9rrqNBbpH7B0zwN9Gx9MC4w8w",
          FTP_HOST: "ftp.efsllc.com",
          FTP_USERNAME: "zeroifta",
          FTP_PASSWORD: "DokUrtye",
          FTP_PORT: 21,
          FTP_ROOT: "/",
          FTP_FILE: "EFSLLCpricing"
        }
      }
    ]
  };