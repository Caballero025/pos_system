const { app, BrowserWindow } = require('electron');
const { exec } = require('child_process');
const path = require('path');

let mainWindow;

app.whenReady().then(() => {

  const phpPath = path.join(process.resourcesPath, 'php', 'php.exe');
  const publicPath = path.join(process.resourcesPath, 'public');

  // 🔥 Arranca PHP embebido
  exec(`"${phpPath}" -S 127.0.0.1:8080 -t "${publicPath}"`);

  mainWindow = new BrowserWindow({
    width: 1280,
    height: 800,
    webPreferences: {
      contextIsolation: true
    }
  });

  // 🚨 ESTO es lo importante
  mainWindow.loadURL('http://127.0.0.1:8080');

});
