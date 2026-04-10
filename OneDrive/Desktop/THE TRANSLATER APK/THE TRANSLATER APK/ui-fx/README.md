# Modern Translator Application

A modern, responsive JavaFX-based translation application with support for multiple languages and themes.

## Features

- Clean, modern UI with dark/light theme support
- Responsive design that works on different screen sizes
- Multiple language support
- Copy to clipboard functionality
- Character counter for input text
- Keyboard shortcuts for common actions

## Prerequisites

- Java 11 or later
- Maven 3.6 or later

## Setup

1. **Download Material Icons Font**:
   - Download the Material Icons font from [Google Fonts](https://fonts.google.com/icons)
   - Place the downloaded `MaterialIcons-Regular.ttf` file in `src/main/resources/fonts/`

2. **Build the Application**:
   ```bash
   mvn clean package
   ```

3. **Run the Application**:
   ```bash
   mvn javafx:run
   ```
   Or directly run the `TranslatorApp` class from your IDE.

## Project Structure

- `src/main/java/com/translator/` - Main application package
  - `ui/` - UI components and views
  - `theme/` - Theme and styling utilities
  - `components/` - Reusable UI components
- `src/main/resources/` - Resource files
  - `styles/` - CSS stylesheets
  - `fonts/` - Custom fonts
  - `images/` - Application images and icons

## Customization

### Themes

The application supports both light and dark themes. You can switch between them using the theme toggle in the top-right corner of the application.

### Adding New Languages

To add support for additional languages:

1. Update the `LANGUAGES` list in `MainView.java`
2. Add the corresponding language code mapping in the translation service implementation

## License

This project is licensed under the MIT License.
