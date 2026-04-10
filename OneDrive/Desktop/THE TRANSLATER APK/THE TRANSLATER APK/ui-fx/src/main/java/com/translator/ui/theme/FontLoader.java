package com.translator.ui.theme;

import javafx.scene.text.Font;
import java.io.InputStream;
import java.util.Objects;

/**
 * Handles loading of custom fonts for the application.
 */
public final class FontLoader {
    
    // Font paths
    public static final String MATERIAL_ICONS = "/fonts/MaterialIcons-Regular.ttf";
    
    // Font families
    public static final String MATERIAL_ICONS_FAMILY = "Material Icons";
    
    // Font sizes
    public static final double ICON_SIZE_SMALL = 18.0;
    public static final double ICON_SIZE_MEDIUM = 24.0;
    public static final double ICON_SIZE_LARGE = 36.0;
    
    // Private constructor to prevent instantiation
    private FontLoader() {}
    
    /**
     * Loads the Material Icons font at the specified size.
     * @param size The font size to load
     * @return The loaded Font object
     */
    public static Font loadMaterialIcons(double size) {
        return loadFont(MATERIAL_ICONS, size);
    }
    
    /**
     * Loads a font from the resources directory.
     * @param path The path to the font file in the resources directory
     * @param size The font size to load
     * @return The loaded Font object
     */
    public static Font loadFont(String path, double size) {
        try (InputStream is = FontLoader.class.getResourceAsStream(path)) {
            if (is == null) {
                System.err.println("Font not found: " + path);
                return Font.getDefault();
            }
            return Font.loadFont(is, size);
        } catch (Exception e) {
            System.err.println("Error loading font " + path + ": " + e.getMessage());
            return Font.getDefault();
        }
    }
    
    /**
     * Initializes all required fonts for the application.
     * This should be called during application startup.
     */
    public static void initializeFonts() {
        // Preload Material Icons at common sizes
        loadMaterialIcons(ICON_SIZE_SMALL);
        loadMaterialIcons(ICON_SIZE_MEDIUM);
        loadMaterialIcons(ICON_SIZE_LARGE);
        
        // Load any other fonts here
    }
}
