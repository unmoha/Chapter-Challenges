package com.translator.ui.theme;

import javafx.scene.Node;
import javafx.scene.control.Label;
import javafx.scene.text.Font;
import javafx.scene.text.Text;

/**
 * Utility class for managing icons in the application.
 * Uses Material Icons (https://fonts.google.com/icons).
 */
public class Icons {
    // Material Icons font family
    private static final String MATERIAL_ICONS = "Material Icons";
    
    // Icon constants (using Material Icons codepoints)
    public static final String TRANSLATE = "\uE8E2";
    public static final String SWAP = "\uE8D5";
    public static final String COPY = "\uE14D";
    public static final String CLEAR = "\uE5CD";
    public static final String SETTINGS = "\uE8B8";
    public static final String HISTORY = "\uE889";
    public static final String FAVORITE = "\uE87D";
    public static final String FAVORITE_BORDER = "\uE87E";
    public static final String MICROPHONE = "\uE029";
    public static final String VOLUME_UP = "\uE050";
    public static final String SUN = "\uE430";
    public static final String MOON = "\uE51D";
    public static final String DARK_MODE = "\uE51C";
    public static final String LIGHT_MODE = "\uE518";
    public static final String TEXT_FIELDS = "\uE262";
    public static final String MENU = "\uE5D2";
    public static final String CLOSE = "\uE5CD";
    
    /**
     * Creates an icon with the specified size and color.
     * @param icon The icon character/codepoint
     * @param size The font size in pixels
     * @param color The color in hex format (e.g., "#FFFFFF")
     * @return A Text node with the icon
     */
    public static Text createIcon(String icon, double size, String color) {
        Text iconText = new Text(icon);
        iconText.setFont(Font.font(MATERIAL_ICONS, size));
        iconText.setStyle("-fx-fill: " + color + ";");
        return iconText;
    }
    
    /**
     * Creates an icon with the specified size using the primary color.
     * @param icon The icon character/codepoint
     * @param size The font size in pixels
     * @return A Text node with the icon
     */
    public static Text createIcon(String icon, double size) {
        // Will use the current theme's primary color
        return createIcon(icon, size, "-color-primary");
    }
    
    /**
     * Creates a standard size icon (24px) with the specified color.
     * @param icon The icon character/codepoint
     * @param color The color in hex format (e.g., "#FFFFFF")
     * @return A Text node with the icon
     */
    public static Text createIcon(String icon, String color) {
        return createIcon(icon, 24, color);
    }
    
    /**
     * Creates a standard size icon (24px) with the primary color.
     * @param icon The icon character/codepoint
     * @return A Text node with the icon
     */
    public static Text createIcon(String icon) {
        return createIcon(icon, 24, "-color-primary");
    }
    
    /**
     * Creates an icon button with the specified icon and size.
     * @param icon The icon character/codepoint
     * @param size The font size in pixels
     * @param tooltipText The tooltip text to show on hover
     * @return A Label with the icon and button styling
     */
    public static Label createIconButton(String icon, double size, String tooltipText) {
        Label button = new Label(icon);
        button.setFont(Font.font(MATERIAL_ICONS, size));
        button.getStyleClass().add("icon-button");
        
        if (tooltipText != null && !tooltipText.isEmpty()) {
            button.setTooltip(new javafx.scene.control.Tooltip(tooltipText));
        }
        
        return button;
    }
    
    /**
     * Creates a standard size (24px) icon button.
     * @param icon The icon character/codepoint
     * @param tooltipText The tooltip text to show on hover
     * @return A Label with the icon and button styling
     */
    public static Label createIconButton(String icon, String tooltipText) {
        return createIconButton(icon, 24, tooltipText);
    }
    
    /**
     * Creates a standard size (24px) icon button without a tooltip.
     * @param icon The icon character/codepoint
     * @return A Label with the icon and button styling
     */
    public static Label createIconButton(String icon) {
        return createIconButton(icon, 24, null);
    }
    
    /**
     * Creates a primary action button with an icon and text.
     * @param icon The icon character/codepoint
     * @param text The button text
     * @return A styled button with icon and text
     */
    public static javafx.scene.control.Button createPrimaryButton(String icon, String text) {
        javafx.scene.control.Button button = new javafx.scene.control.Button(text);
        
        if (icon != null && !icon.isEmpty()) {
            Text iconNode = createIcon(icon, 20, "white");
            button.setGraphic(iconNode);
            button.setGraphicTextGap(8);
        }
        
        button.getStyleClass().add("primary-button");
        return button;
    }
    
    /**
     * Creates a primary action button with text only.
     * @param text The button text
     * @return A styled primary button
     */
    public static javafx.scene.control.Button createPrimaryButton(String text) {
        return createPrimaryButton("", text);
    }
}
