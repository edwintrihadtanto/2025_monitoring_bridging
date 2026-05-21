const { app, BrowserWindow } = require('electron')

function createWindow() {
  const win = new BrowserWindow({
    width: 1400,
    height: 900,
    autoHideMenuBar: true
  })

  win.loadURL('http://localhost/2025_monitoring_bridging')
}

app.whenReady().then(createWindow)