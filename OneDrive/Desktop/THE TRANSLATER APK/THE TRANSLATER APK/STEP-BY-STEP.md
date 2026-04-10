# Step-by-Step Guide: Running Translator Desktop on Multiple Computers

## Prerequisites (for each computer)

### Required Software
1. **Java 17+** - Download from https://java.com
2. **JavaFX SDK 21.0.5** - Download from https://gluonhq.com/products/javafx/
3. **LibreTranslate Server** (optional, for offline use)

### Optional Software (for advanced features)
- **Tesseract OCR** - For image text recognition
- **Vosk Models** - For offline voice recognition

---

## Method 1: Development/Quick Testing (Single Computer)

### Step 1: Build the Application
```bash
# Navigate to project directory
cd "c:\Users\hp\OneDrive\Desktop\THE TRANSLATER APK"

# Compile all modules
mvn clean compile

# Run the application
run-app.bat
```

### Step 2: Test the Application
- The app should open with a JavaFX interface
- Test translation with the default LibreTranslate server
- Check if all features work (text input, translation, copy, etc.)

---

## Method 2: Portable JAR Distribution (Multiple Computers)

### Step 1: Create Executable JAR
```bash
# Build with all dependencies
mvn clean package

# The JAR will be created at:
# ui-fx/target/translator-desktop-0.1.0-jar-with-dependencies.jar
```

### Step 2: Prepare Distribution Package
Create a folder containing:
```
translator-desktop/
├── translator-desktop-0.1.0-jar-with-dependencies.jar
├── run-portable.bat
├── README.txt
└── config/
    └── libretranslate.properties
```

### Step 3: Create run-portable.bat
```batch
@echo off
echo Starting Translator Desktop...
java -jar translator-desktop-0.1.0-jar-with-dependencies.jar
pause
```

### Step 4: Distribute to Target Computers
1. Copy the entire `translator-desktop/` folder to each computer
2. Ensure Java 17+ is installed on target computers
3. Double-click `run-portable.bat` to start the application

---

## Method 3: Native Installer (Professional Distribution)

### Step 1: Build Native Installers
```bash
# Build the application
mvn clean package

# Create native installers (requires jpackage)
mvn jpackage:jpackage
```

### Step 2: Distribute Installers
- **Windows**: `ui-fx/target/dist/*.exe` or `*.msi`
- **Mac**: `ui-fx/target/dist/*.dmg`
- **Linux**: `ui-fx/target/dist/*.deb` or `*.rpm`

### Step 3: Installation on Target Computers
- **Windows**: Double-click `.exe` installer
- **Mac**: Open `.dmg` and drag to Applications
- **Linux**: Use package manager or `dpkg -i *.deb`

---

## Method 4: Web-Based Distribution

### Step 1: Create Web Distribution
```bash
# Create a simple web server folder
mkdir web-dist
cp ui-fx/target/translator-desktop-0.1.0-jar-with-dependencies.jar web-dist/
cp run-portable.bat web-dist/
```

### Step 2: Host on Web Server
Upload `web-dist/` to any web server and provide download link.

---

## Configuration for Multiple Computers

### Step 1: LibreTranslate Server Setup

#### Option A: Use Free Public Server
- URL: `https://libretranslate.de/`
- No setup required
- Rate limited

#### Option B: Self-Hosted Server (Recommended for organizations)
```bash
# On a server computer
docker run -ti --rm -p 5000:5000 libretranslate/libretranslate

# Or install locally
pip install libretranslate
libretranslate --host 0.0.0.0 --port 5000
```

### Step 2: Configure Application
Create `config.properties` in the application directory:
```properties
# Translation server configuration
translation.engine=libretranslate
translation.url=http://your-server-ip:5000/
translation.timeout=30000

# UI settings
ui.theme=light
ui.language=en

# Feature flags
feature.voice=true
feature.ocr=true
feature.history=true
```

---

## Step-by-Step Deployment Checklist

### For Development Team
- [ ] Compile and test application locally
- [ ] Create distribution package (JAR or installer)
- [ ] Test on clean development machine
- [ ] Verify all dependencies are included

### For Each Target Computer
- [ ] Install Java 17+ (if using JAR method)
- [ ] Copy application files
- [ ] Configure translation server URL
- [ ] Test network connectivity to translation server
- [ ] Run application test
- [ ] Create desktop shortcut (optional)

### Network Configuration
- [ ] Ensure port 5000 is open (if using self-hosted LibreTranslate)
- [ ] Test firewall settings
- [ ] Verify internet connectivity (if using public server)

---

## Troubleshooting Guide

### Common Issues and Solutions

#### "Java not found"
```bash
# Check Java version
java -version

# Should show Java 17+
```

#### "JavaFX not found"
```bash
# Install JavaFX SDK
# Set JAVAFX_PATH environment variable
set JAVAFX_PATH=C:\Program Files\Java\javafx-sdk-21.0.5\lib
```

#### "Connection refused" to LibreTranslate
1. Check if LibreTranslate server is running
2. Verify server URL in configuration
3. Test server accessibility in browser
4. Check firewall settings

#### "Application won't start"
1. Verify Java installation
2. Check file permissions
3. Run from command line to see error messages
4. Ensure all dependencies are available

#### Performance Issues
1. Use local LibreTranslate server for faster translation
2. Ensure stable internet connection
3. Check server resources
4. Consider offline models for sensitive data

---

## Advanced Configuration

### Multiple Translation Engines
The application supports multiple translation engines:
- LibreTranslate (default)
- Google Translate (requires API key - not recommended)
- Microsoft Translator (requires API key - not recommended)
- Custom engines (implement TranslationEngine interface)

### Offline Capabilities
1. **Voice Recognition**: Download Vosk models
2. **OCR**: Install Tesseract with language data
3. **Translation**: Host local LibreTranslate instance

### Enterprise Deployment
For large organizations:
1. Set up centralized LibreTranslate server
2. Create custom installers with pre-configured settings
3. Use network configuration management
4. Implement automatic updates

---

## Support and Maintenance

### Regular Tasks
- Monitor LibreTranslate server performance
- Update translation models
- Backup user history and settings
- Check for security updates

### User Support
Provide users with:
- Installation guide
- Configuration instructions
- Troubleshooting FAQ
- Contact information for technical support

This guide covers all methods for running the translation application on multiple computers, from simple testing to enterprise deployment.
