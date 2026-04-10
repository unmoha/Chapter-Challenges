# Translator Desktop - Multi-Computer Deployment Guide

## Quick Start

### For Development Team
1. Run `deploy.bat` (Windows) or `deploy.sh` (Linux/Mac)
2. Distribute the generated files to target computers

### For End Users
Choose one of these methods:

## Method 1: Portable JAR (Easiest)

**Requirements:**
- Java 17+ installed on target computer

**Steps:**
1. Copy `ui-fx/target/ui-fx-0.1.0-jar-with-dependencies.jar` to target computer
2. Run: `java -jar ui-fx-0.1.0-jar-with-dependencies.jar`

**Pros:** Simple, no installation
**Cons:** Requires Java installation

## Method 2: Native Installer (Recommended)

**Requirements:**
- None on target computer (Java bundled)

**Steps:**
1. Copy the appropriate installer from `ui-fx/target/dist/`:
   - Windows: `.exe` or `.msi`
   - Mac: `.dmg`
   - Linux: `.deb` or `.rpm`
2. Run installer on target computer

**Pros:** Self-contained, professional appearance
**Cons:** Larger file size

## Method 3: Web Start (Advanced)

Create a simple web server to host the JAR:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Translator Desktop Launcher</title>
</head>
<body>
    <h1>Translator Desktop</h1>
    <a href="ui-fx-0.1.0-jar-with-dependencies.jar" download>
        Download Application
    </a>
    <p>Requires Java 17+ to run</p>
</body>
</html>
```

## Network Configuration

### LibreTranslate Server Options

**Option 1: Public Server (Free)**
- Use: `https://libretranslate.de/`
- No setup required
- Rate limited

**Option 2: Self-Hosted Server**
```bash
# On a server/computer in your network
docker run -ti --rm -p 5000:5000 libretranslate/libretranslate
```
- Configure app to use: `http://server-ip:5000`
- Unlimited usage
- Requires Docker

### Configuration File

Create `config.properties` in the same directory as the JAR:

```properties
# Translation engine configuration
translation.engine=libretranslate
translation.url=https://libretranslate.de/
translation.timeout=30000

# UI settings
ui.theme=light
ui.language=en
```

## Deployment Checklist

### Before Distribution
- [ ] Test on clean system
- [ ] Verify Java version compatibility
- [ ] Test network connectivity
- [ ] Check LibreTranslate server availability

### For Each Target Computer
- [ ] Copy application files
- [ ] Verify Java installation (Method 1)
- [ ] Test network access to translation server
- [ ] Run application test
- [ ] Create desktop shortcut (optional)

## Troubleshooting

### Common Issues

**"Java not found"**
- Install Java 17+ from java.com
- Add Java to PATH

**"Connection refused"**
- Check internet connection
- Verify LibreTranslate server URL
- Test server accessibility in browser

**"Application won't start"**
- Check Java version: `java -version`
- Verify file permissions
- Try command line: `java -jar app.jar --verbose`

### Performance Tips

1. **Local LibreTranslate Server** for faster translations
2. **SSD storage** for better startup time
3. **4GB+ RAM** recommended for smooth operation
4. **Stable internet connection** for online translation

## Security Considerations

- Translation data sent to external servers
- Use HTTPS for all communications
- Consider local server for sensitive data
- Regular updates recommended

## Support

For issues:
1. Check logs in application directory
2. Verify network connectivity
3. Test with minimal configuration
4. Contact development team
