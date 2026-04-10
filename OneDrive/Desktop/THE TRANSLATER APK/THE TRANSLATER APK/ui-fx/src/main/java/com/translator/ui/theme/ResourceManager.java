package com.translator.ui.theme;

import javafx.scene.image.Image;
import javafx.scene.text.Font;
import java.io.InputStream;
import java.util.HashMap;
import java.util.Map;
import java.util.Objects;

/**
 * Manages application resources such as fonts, images, and other assets.
 */
public final class ResourceManager {
    private static final Map<String, Font> loadedFonts = new HashMap<>();
    private static final Map<String, Image> loadedImages = new HashMap<>();
    
    // Font paths
    private static final String FONTS_DIR = "/fonts/";
    private static final String MATERIAL_ICONS_FONT = "MaterialIcons-Regular.ttf";
    
    // Image paths
    private static final String IMAGES_DIR = "/images/";
    
    // Private constructor to prevent instantiation
    private ResourceManager() {}
    
    /**
     * Loads a font from the resources.
     * @param path The path to the font file relative to the resources directory
     * @param size The size of the font to load
     * @return The loaded Font object
     * @throws RuntimeException if the font cannot be loaded
     */
    public static Font loadFont(String path, double size) {
        String key = path + "_" + size;
        return loadedFonts.computeIfAbsent(key, k -> {
            try (InputStream is = ResourceManager.class.getResourceAsStream(FONTS_DIR + path)) {
                if (is == null) {
                    throw new RuntimeException("Font not found: " + path);
                }
                return Font.loadFont(is, size);
            } catch (Exception e) {
                throw new RuntimeException("Failed to load font: " + path, e);
            }
        });
    }
    
    /**
     * Loads the Material Icons font with the specified size.
     * @param size The size of the font to load
     * @return The loaded Font object
     */
    public static Font loadMaterialIcons(double size) {
        return loadFont(MATERIAL_ICONS_FONT, size);
    }
    
    /**
     * Loads an image from the resources.
     * @param path The path to the image file relative to the resources directory
     * @return The loaded Image object
     * @throws RuntimeException if the image cannot be loaded
     */
    public static Image loadImage(String path) {
        return loadedImages.computeIfAbsent(path, k -> {
            try (InputStream is = ResourceManager.class.getResourceAsStream(IMAGES_DIR + path)) {
                if (is == null) {
                    throw new RuntimeException("Image not found: " + path);
                }
                return new Image(is);
            } catch (Exception e) {
                throw new RuntimeException("Failed to load image: " + path, e);
            }
        });
    }
    
    /**
     * Gets the URL of a resource as a string.
     * @param path The path to the resource relative to the resources directory
     * @return The URL of the resource as a string
     * @throws RuntimeException if the resource cannot be found
     */
    public static String getResourceUrl(String path) {
        return Objects.requireNonNull(
            ResourceManager.class.getResource("/" + path),
            "Resource not found: " + path
        ).toExternalForm();
    }
    
    /**
     * Initializes the ResourceManager by preloading commonly used resources.
     * This should be called during application startup.
     */
    public static void initialize() {
        // Preload Material Icons font at different sizes
        loadMaterialIcons(16);
        loadMaterialIcons(20);
        loadMaterialIcons(24);
        loadMaterialIcons(32);
        loadMaterialIcons(48);
        
        // Add any other resources that should be preloaded here
    }
}
