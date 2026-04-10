package com.translator.ui.theme;

import javafx.scene.Scene;

/**
 * Manages application themes and provides methods to switch between them.
 */
public class ThemeManager {
    private static final String BASE_CSS = "/styles/base.css";
    private static final String LIGHT_THEME = "/styles/light-theme.css";
    private static final String DARK_THEME = "/styles/dark-theme.css";
    
    public enum Theme {
        LIGHT,
        DARK
    }
    
    private static volatile Theme currentTheme = Theme.LIGHT;

    private ThemeManager() {
        // Private constructor to prevent instantiation
    }

    /**
     * Applies the specified theme to the given scene.
     * @param scene The scene to apply the theme to
     * @param theme The theme to apply (LIGHT or DARK)
     */
    public static void applyTheme(Scene scene, Theme theme) {
        if (scene == null) return;
        
        try {
            // Clear any existing stylesheets
            scene.getStylesheets().clear();
            
            // Add base styles and theme
            String themeCss = theme == Theme.DARK ? DARK_THEME : LIGHT_THEME;
            String baseUrl = ThemeManager.class.getResource(BASE_CSS).toExternalForm();
            String themeUrl = ThemeManager.class.getResource(themeCss).toExternalForm();
            
            System.out.println("Loading CSS: " + baseUrl);
            System.out.println("Loading CSS: " + themeUrl);
            
            scene.getStylesheets().addAll(baseUrl, themeUrl);
            
            currentTheme = theme;
        } catch (Exception e) {
            System.err.println("Error applying theme: " + e.getMessage());
        }
    }
    
    /**
     * Toggles between light and dark themes.
     * @param scene The scene to toggle the theme for
     * @return The new theme that was applied
     */
    public static Theme toggleTheme(Scene scene) {
        Theme newTheme = (currentTheme == Theme.LIGHT) ? Theme.DARK : Theme.LIGHT;
        applyTheme(scene, newTheme);
        return newTheme;
    }
    
    /**
     * Gets the current theme.
     * @return The current theme
     */
    public static Theme getCurrentTheme() {
        return currentTheme;
    }
    
    /**
     * Applies the current theme to the given scene.
     * @param scene The scene to apply the current theme to
     */
    public static void applyCurrentTheme(Scene scene) {
        applyTheme(scene, currentTheme);
    }
}
