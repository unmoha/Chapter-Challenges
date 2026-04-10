# Quick Start Guide: Server-Client Translation System

## 5-Minute Setup

### Computer A (Server) - 2 Minutes
1. **Install Docker Desktop** from docker.com
2. **Run setup script:**
   ```bash
   setup-server.bat
   ```
3. **Note your IP address** (shown in output)

### Computer B (Client) - 3 Minutes  
1. **Install Java 17+** from java.com
2. **Copy application files** to Computer B
3. **Run setup script:**
   ```bash
   setup-client.bat
   ```
4. **Enter server IP** when prompted
5. **Launch application:**
   ```bash
   run-client.bat
   ```

---

## Detailed Steps

### Step 1: Server Computer (A)
```bash
# 1. Install Docker Desktop
# Download from https://docker.com

# 2. Run server setup
setup-server.bat

# 3. Note your IP address (e.g., 192.168.1.100)
```

### Step 2: Client Computer (B)
```bash
# 1. Install Java 17+
# Download from https://java.com

# 2. Copy these files to Client B:
# - translator-desktop-0.1.0-jar-with-dependencies.jar
# - setup-client.bat
# - run-client.bat

# 3. Run client setup
setup-client.bat

# 4. Enter server IP when prompted
# (e.g., 192.168.1.100)

# 5. Start application
run-client.bat
```

---

## Verification

### Test Server
- Open browser: `http://localhost:5000`
- Should show LibreTranslate interface

### Test Client
- Application should start
- Enter text and click "Translate"
- Should get translation from server

---

## Troubleshooting

### Server Issues
- **Docker not found**: Install Docker Desktop
- **Port 5000 blocked**: Open Windows Firewall
- **Container not starting**: Run `docker logs libretranslate`

### Client Issues  
- **Java not found**: Install Java 17+
- **Can't connect**: Check server IP and network
- **Translation fails**: Verify server is running

---

## Files Created

### Server Side
- `setup-server.bat` - Automated server setup
- Docker container `libretranslate` on port 5000

### Client Side
- `setup-client.bat` - Automated client setup  
- `client-config.properties` - Server connection settings
- `run-client.bat` - Application launcher

---

## Next Steps

1. **Test basic translation** between computers
2. **Configure language preferences** in the app
3. **Enable advanced features** (voice, OCR, history)
4. **Add more clients** by repeating client setup

That's it! Your two-computer translation system is ready.
