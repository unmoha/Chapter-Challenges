package com.translator.ui.theme;

import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;

/**
 * Manages application fonts and provides utility methods for loading and using custom fonts.
 */
public class FontManager {
    // Font families
    private static final String PRIMARY_FONT = "Segoe UI";
    private static final String SECONDARY_FONT = "Roboto";
    private static final String CODE_FONT = "JetBrains Mono";
    
    // Font weights - using JavaFX's FontWeight enum
    public enum FontWeight {
        THIN("Thin"),
        EXTRA_LIGHT("Extra Light"),
        LIGHT("Light"),
        NORMAL("Normal"),
        MEDIUM("Medium"),
        SEMI_BOLD("Semi Bold"),
        BOLD("Bold"),
        EXTRA_BOLD("Extra Bold"),
        BLACK("Black");
        
        private final String weightName;
        
        FontWeight(String weightName) {
            this.weightName = weightName;
        }
        
        @Override
        public String toString() {
            return weightName;
        }
    }
    
    // Font weight constants
    public static final FontWeight THIN = FontWeight.THIN;
    public static final FontWeight EXTRA_LIGHT = FontWeight.EXTRA_LIGHT;
    public static final FontWeight LIGHT = FontWeight.LIGHT;
    public static final FontWeight NORMAL = FontWeight.NORMAL;
    public static final FontWeight MEDIUM = FontWeight.MEDIUM;
    public static final FontWeight SEMI_BOLD = FontWeight.SEMI_BOLD;
    public static final FontWeight BOLD = FontWeight.BOLD;
    public static final FontWeight EXTRA_BOLD = FontWeight.EXTRA_BOLD;
    public static final FontWeight BLACK = FontWeight.BLACK;
    
    // Font sizes
    public static final double H1 = 32.0;
    public static final double H2 = 24.0;
    public static final double H3 = 20.0;
    public static final double H4 = 18.0;
    public static final double BODY = 14.0;
    public static final double CAPTION = 12.0;
    public static final double SMALL = 10.0;
    
    private FontManager() {
        // Private constructor to prevent instantiation
    }
    
    /**
     * Loads a font with the specified size and weight.
     * @param size The font size
     * @param weight The font weight
     * @return A Font object with the specified properties
     */
    public static Font getFont(double size, FontWeight weight) {
        // Convert our custom FontWeight to JavaFX's FontWeight
        javafx.scene.text.FontWeight fxWeight = javafx.scene.text.FontWeight.findByName(weight.toString().toUpperCase());
        if (fxWeight == null) {
            fxWeight = javafx.scene.text.FontWeight.NORMAL;
        }
        return Font.font(PRIMARY_FONT, fxWeight, size);
    }
    
    /**
     * Loads the primary font with the specified size and weight.
     * @param size The font size
     * @param weight The font weight
     * @return A Font object with the specified properties
     */
    public static Font primaryFont(double size, FontWeight weight) {
        return getFont(size, weight);
    }
    
    /**
     * Loads the primary font with the specified size and normal weight.
     * @param size The font size
     * @return A Font object with normal weight
     */
    public static Font primaryFont(double size) {
        return getFont(size, NORMAL);
    }
    
    /**
     * Loads a font with normal weight.
     * @param size The font size
     * @return A Font object with normal weight
     */
    public static Font getFont(double size) {
        return getFont(size, NORMAL);
    }
    
    /**
     * Loads the secondary font with the specified size and weight.
     * @param size The font size
     * @param weight The font weight
     * @return A secondary Font object with the specified properties
     */
    public static Font getSecondaryFont(double size, FontWeight weight) {
        // Convert our custom FontWeight to JavaFX's FontWeight
        javafx.scene.text.FontWeight fxWeight = javafx.scene.text.FontWeight.findByName(weight.toString().toUpperCase());
        if (fxWeight == null) {
            fxWeight = javafx.scene.text.FontWeight.NORMAL;
        }
        return Font.font(SECONDARY_FONT, fxWeight, size);
    }
    
    /**
     * Loads the code/monospace font.
     * @param size The font size
     * @return A monospace Font object
     */
    public static Font getCodeFont(double size) {
        return Font.font(CODE_FONT, size);
    }
    
    /**
     * Loads the Material Icons font at the specified size.
     * @param size The font size
     * @return The Material Icons Font object
     */
    public static Font loadMaterialIcons(double size) {
        try {
            return Font.loadFont(FontManager.class.getResourceAsStream(
                "/fonts/MaterialIcons-Regular.ttf"), size);
        } catch (Exception e) {
            System.err.println("Error loading Material Icons font: " + e.getMessage());
            return Font.font("System", size);
        }
    }
    
    /**
     * Converts FontWeight to a string representation for CSS.
     * @param weight The FontWeight
     * @return The CSS font weight value
     */
    public static String getWeightName(FontWeight weight) {
        if (weight == null) {
            return "normal";
        }
        
        if (weight == THIN) return "100";
        if (weight == EXTRA_LIGHT) return "200";
        if (weight == LIGHT) return "300";
        if (weight == NORMAL) return "400";
        if (weight == MEDIUM) return "500";
        if (weight == SEMI_BOLD) return "600";
        if (weight == BOLD) return "700";
        if (weight == EXTRA_BOLD) return "800";
        if (weight == BLACK) return "900";
        
        return "400";
    }
    
    /**
     * Gets the CSS font family string for the primary font.
     * @return A string with font family fallbacks
     */
    public static String getPrimaryFontFamily() {
        return String.format("'%s', '%s', system-ui, -apple-system, BlinkMacSystemFont, sans-serif", 
            PRIMARY_FONT, SECONDARY_FONT);
    }
    
    /**
     * Gets the CSS font family string for the monospace font.
     * @return A string with monospace font family fallbacks
     */
    public static String getMonospaceFontFamily() {
        return String.format("'%s', 'SF Mono', 'Roboto Mono', 'Courier New', monospace", 
            CODE_FONT);
    }

}
