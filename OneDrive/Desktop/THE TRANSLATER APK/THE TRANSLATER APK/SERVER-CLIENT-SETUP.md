# Server-Client Setup Guide: Two Computer Translation System

## Overview
- **Computer A (Server)**: Hosts LibreTranslate translation service
- **Computer B (Client)**: Runs the translation application

---

## Computer A: SERVER Setup (Translation Service Host)

### Step 1: Install Docker
```bash
# Download Docker Desktop from https://docker.com
# Install and start Docker Desktop
```

### Step 2: Start LibreTranslate Server
```bash
# Open Command Prompt or PowerShell
docker run -d --name libretranslate -p 5000:5000 libretranslate/libretranslate

# Verify server is running
curl http://localhost:5000/spec.json
```

### Step 3: Configure Firewall
```bash
# Allow port 5000 through Windows Firewall
# Go to Windows Defender Firewall > Advanced Settings
# Add inbound rule for TCP port 5000
```

### Step 4: Find Server IP Address
```bash
ipconfig
# Look for "IPv4 Address" (e.g., 192.168.1.100)
```

### Step 5: Test Server Accessibility
```bash
# Test from server computer
curl http://localhost:5000/translate

# Test from client computer (replace with server IP)
curl http://192.168.1.100:5000/translate
```

### Step 6: Create Server Configuration File
Create `server-config.properties`:
```properties
# LibreTranslate Server Configuration
server.host=0.0.0.0
server.port=5000
server.ssl=false

# Translation Settings
translation.char.limit=10000
translation.req.limit=100
translation.batch.limit=10

# Language Support
languages=en,es,fr,de,ar,zh,ru,pt,it,ja,ko
```

---

## Computer B: CLIENT Setup (Translation Application)

### Step 1: Install Java 17+
```bash
# Download from https://java.com
# Verify installation:
java -version
```

### Step 2: Build Translation Application
```bash
# On development machine or copy pre-built files
cd "c:\Users\hp\OneDrive\Desktop\THE TRANSLATER APK"
mvn clean package
```

### Step 3: Create Client Configuration
Create `client-config.properties`:
```properties
# Translation Server Connection
translation.engine=libretranslate
translation.url=http://192.168.1.100:5000/
translation.timeout=30000

# Application Settings
ui.theme=light
ui.language=en
feature.voice=true
feature.ocr=true
feature.history=true

# Network Settings
network.retry.attempts=3
network.retry.delay=1000
```

### Step 4: Create Client Launcher
Create `run-client.bat`:
```batch
@echo off
echo Starting Translator Desktop Client...

REM Set server IP (update this to match your server)
set SERVER_IP=192.168.1.100

REM Set Java path (adjust if needed)
set JAVA_HOME=C:\Program Files\Java\jdk-17

REM Run application with server configuration
"%JAVA_HOME%\bin\java" -jar translator-desktop-0.1.0-jar-with-dependencies.jar

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo Failed to connect to server at %SERVER_IP%
    echo Please check:
    echo 1. Server computer is running LibreTranslate
    echo 2. Network connectivity between computers
    echo 3. Firewall settings on server
    echo.
)

pause
```

---

## Step-by-Step Deployment Process

### Phase 1: Server Preparation (Computer A)

1. **Install Docker Desktop**
   - Download from docker.com
   - Install with default settings
   - Restart computer

2. **Start Translation Service**
   ```bash
   docker run -d --name libretranslate -p 5000:5000 libretranslate/libretranslate
   ```

3. **Verify Server**
   - Open browser: `http://localhost:5000`
   - Should show LibreTranslate interface

4. **Get Server IP**
   ```bash
   ipconfig
   # Note the IPv4 Address (e.g., 192.168.1.100)
   ```

5. **Test Network Access**
   - From another device, try: `http://[SERVER_IP]:5000`
   - Should show LibreTranslate interface

### Phase 2: Client Preparation (Computer B)

1. **Install Java 17+**
   - Download from java.com
   - Install with default settings

2. **Copy Application Files**
   ```
   Create folder: C:\TranslatorDesktop\
   Copy files:
   - translator-desktop-0.1.0-jar-with-dependencies.jar
   - client-config.properties
   - run-client.bat
   ```

3. **Update Server IP**
   - Edit `run-client.bat`
   - Change `SERVER_IP=192.168.1.100` to actual server IP

4. **Test Connection**
   ```bash
   # Test server connectivity
   ping 192.168.1.100
   
   # Test translation service
   curl http://192.168.1.100:5000/spec.json
   ```

### Phase 3: Final Testing

1. **Start Client Application**
   - Double-click `run-client.bat`
   - Application should start

2. **Test Translation**
   - Enter text in input field
   - Select languages
   - Click "Translate"
   - Should get translation from server

3. **Verify Features**
   - Text translation works
   - Language switching works
   - Copy to clipboard works
   - History saving works

---

## Network Configuration Details

### Server Side (Computer A)

#### Port Configuration
```bash
# Port 5000 must be open for incoming connections
# Windows Firewall: Allow TCP port 5000
# Router: Port forward 5000 to server IP (if accessing from internet)
```

#### Docker Container Management
```bash
# Start server
docker start libretranslate

# Stop server
docker stop libretranslate

# View logs
docker logs libretranslate

# Restart server
docker restart libretranslate
```

### Client Side (Computer B)

#### Network Testing
```bash
# Test server connectivity
ping 192.168.1.100

# Test translation service
telnet 192.168.1.100 5000

# Test HTTP access
curl http://192.168.1.5000/translate
```

---

## Troubleshooting Guide

### Server Issues

#### "Docker not found"
```bash
# Install Docker Desktop
# Restart computer
# Verify: docker --version
```

#### "Port 5000 already in use"
```bash
# Find process using port 5000
netstat -ano | findstr :5000

# Kill process
taskkill /PID [PID] /F

# Or use different port
docker run -d --name libretranslate -p 5001:5000 libretranslate/libretranslate
```

#### "Client cannot connect"
```bash
# Check firewall
# Verify Docker container is running
# Test with curl from client machine
```

### Client Issues

#### "Java not found"
```bash
# Install Java 17+
# Set JAVA_HOME environment variable
# Verify: java -version
```

#### "Connection refused"
```bash
# Verify server IP is correct
# Test server accessibility
# Check network connectivity
```

#### "Translation fails"
```bash
# Check server logs: docker logs libretranslate
# Verify server is responding
# Test with curl command
```

---

## Advanced Configuration

### Multiple Clients

To support multiple client computers:

1. **Server Side**
   ```bash
   # Start server with higher capacity
   docker run -d --name libretranslate -p 5000:5000 \
     -e LIBRETRANSLATE_REQ_LIMIT=1000 \
     -e LIBRETRANSLATE_BATCH_LIMIT=50 \
     libretranslate/libretranslate
   ```

2. **Client Side**
   - Copy same application files to each client
   - Update server IP in each client's configuration
   - Test each client individually

### SSL/HTTPS Setup

For production environments:

1. **Generate SSL Certificate**
   ```bash
   # Use Let's Encrypt or self-signed certificate
   ```

2. **Configure Server with SSL**
   ```bash
   docker run -d --name libretranslate -p 443:5000 \
     -e LIBRETRANSLATE_SSL=true \
     -v /path/to/cert:/app/cert \
     libretranslate/libretranslate
   ```

3. **Update Client Configuration**
   ```properties
   translation.url=https://192.168.1.100/
   ```

### Performance Optimization

#### Server Side
```bash
# Allocate more resources
docker run -d --name libretranslate -p 5000:5000 \
  --memory=2g \
  --cpus=2 \
  libretranslate/libretranslate
```

#### Client Side
```properties
# Increase timeout for slow networks
translation.timeout=60000

# Enable caching
translation.cache.enabled=true
translation.cache.size=1000
```

---

## Security Considerations

### Server Security
1. **Network Security**
   - Use firewall to restrict access
   - Consider VPN for remote access
   - Monitor server logs

2. **Docker Security**
   - Keep Docker updated
   - Use official LibreTranslate image
   - Regular security updates

### Client Security
1. **Data Protection**
   - All translation data goes to server
   - Consider local server for sensitive data
   - Use HTTPS for production

2. **Application Security**
   - Keep Java updated
   - Regular application updates
   - User access controls

---

## Maintenance

### Daily Tasks
- Monitor server logs
- Check client connectivity
- Backup configuration files

### Weekly Tasks
- Update Docker containers
- Review server performance
- Check for security updates

### Monthly Tasks
- Update translation models
- Review user feedback
- Performance optimization

This setup provides a robust two-computer translation system with clear separation of server and client responsibilities.
