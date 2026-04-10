package com.translator.ui.theme;

/**
 * Contains UI style constants and utility methods for consistent styling.
 */
public final class UIStyles {
    // Private constructor to prevent instantiation
    private UIStyles() {}
    
    // Border radius
    public static final double BORDER_RADIUS_SM = 4.0;
    public static final double BORDER_RADIUS_MD = 8.0;
    public static final double BORDER_RADIUS_LG = 12.0;
    public static final double BORDER_RADIUS_XL = 16.0;
    public static final double BORDER_RADIUS_2XL = 24.0;
    public static final double BORDER_RADIUS_FULL = 9999.0;
    
    // Spacing (in pixels)
    public static final double SPACING_1 = 4.0;
    public static final double SPACING_2 = 8.0;
    public static final double SPACING_3 = 12.0;
    public static final double SPACING_4 = 16.0;
    public static final double SPACING_5 = 20.0;
    public static final double SPACING_6 = 24.0;
    public static final double SPACING_8 = 32.0;
    public static final double SPACING_10 = 40.0;
    public static final double SPACING_12 = 48.0;
    
    // Elevation (shadow) levels
    public static final String ELEVATION_1 = "dropshadow(gaussian, rgba(0, 0, 0, 0.05), 4, 0, 0, 1)";
    public static final String ELEVATION_2 = "dropshadow(gaussian, rgba(0, 0, 0, 0.1), 8, 0, 0, 2)";
    public static final String ELEVATION_3 = "dropshadow(gaussian, rgba(0, 0, 0, 0.1), 16, 0, 0, 4)";
    public static final String ELEVATION_4 = "dropshadow(gaussian, rgba(0, 0, 0, 0.1), 24, 0, 0, 8)";
    
    // Animation durations (in milliseconds)
    public static final int ANIMATION_FAST = 100;
    public static final int ANIMATION_NORMAL = 200;
    public static final int ANIMATION_SLOW = 300;
    
    // Common CSS style classes
    public static final String CARD_STYLE = "card";
    public static final String CARD_TITLE_STYLE = "card-title";
    public static final String PRIMARY_BUTTON_STYLE = "primary-button";
    public static final String SECONDARY_BUTTON_STYLE = "secondary-button";
    public static final String TEXT_INPUT_STYLE = "text-input";
    public static final String ICON_BUTTON_STYLE = "icon-button";
    
    // Text style classes
    public static final String TEXT_H1 = "text-h1";
    public static final String TEXT_H2 = "text-h2";
    public static final String TEXT_H3 = "text-h3";
    public static final String TEXT_H4 = "text-h4";
    public static final String TEXT_BODY_LARGE = "text-body-large";
    public static final String TEXT_BODY = "text-body";
    public static final String TEXT_BODY_SMALL = "text-body-small";
    public static final String TEXT_CAPTION = "text-caption";
    
    // Utility methods for creating style strings
    public static String createBorderRadius(double radius) {
        return String.format("-fx-background-radius: %f %f %f %f; -fx-border-radius: %f %f %f %f;",
            radius, radius, radius, radius, radius, radius, radius, radius);
    }
    
    public static String createPadding(double padding) {
        return String.format("-fx-padding: %f %f %f %f;", padding, padding, padding, padding);
    }
    
    public static String createPadding(double topBottom, double rightLeft) {
        return String.format("-fx-padding: %f %f %f %f;", topBottom, rightLeft, topBottom, rightLeft);
    }
    
    public static String createPadding(double top, double right, double bottom, double left) {
        return String.format("-fx-padding: %f %f %f %f;", top, right, bottom, left);
    }
    
    public static String createMargin(double margin) {
        return String.format("-fx-margin: %f %f %f %f;", margin, margin, margin, margin);
    }
    
    public static String createMargin(double topBottom, double rightLeft) {
        return String.format("-fx-margin: %f %f %f %f;", topBottom, rightLeft, topBottom, rightLeft);
    }
    
    public static String createMargin(double top, double right, double bottom, double left) {
        return String.format("-fx-margin: %f %f %f %f;", top, right, bottom, left);
    }
    
    // Common style combinations
    public static String createCardStyle() {
        return String.format(
            "-fx-background-color: -color-surface; %s %s %s",
            createBorderRadius(BORDER_RADIUS_LG),
            createPadding(SPACING_4),
            "-fx-effect: " + ELEVATION_2 + ";"
        );
    }
    
    public static String createPrimaryButtonStyle() {
        return String.format(
            "-fx-background-color: -color-primary; " +
            "-fx-text-fill: -color-on-primary; " +
            "-fx-font-weight: 600; " +
            "-fx-padding: %f %f %f %f; " +
            "-fx-cursor: hand; " +
            "%s " +
            "-fx-effect: %s; " +
            "-fx-font-size: %f;" +
            "-fx-background-radius: %f;"
            ,
            SPACING_2, SPACING_4, SPACING_2, SPACING_4, // Padding
            createBorderRadius(BORDER_RADIUS_MD),
            ELEVATION_1,
            FontManager.BODY,
            BORDER_RADIUS_MD
        );
    }
    
    public static String createSecondaryButtonStyle() {
        return String.format(
            "-fx-background-color: transparent; " +
            "-fx-text-fill: -color-primary; " +
            "-fx-border-color: -color-primary; " +
            "-fx-border-width: 1.5px; " +
            "-fx-border-radius: %f; " +
            "-fx-padding: %f %f %f %f; " +
            "-fx-cursor: hand; " +
            "-fx-font-size: %f;"
            ,
            BORDER_RADIUS_MD,
            SPACING_2 - 1.5, SPACING_4 - 1.5, SPACING_2 - 1.5, SPACING_4 - 1.5, // Padding (adjusted for border)
            FontManager.BODY
        );
    }
    
    public static String createTextInputStyle() {
        return String.format(
            "-fx-background-color: -color-surface; " +
            "-fx-text-fill: -color-text-primary; " +
            "-fx-prompt-text-fill: -color-text-disabled; " +
            "-fx-highlight-fill: -color-primary-light; " +
            "-fx-highlight-text-fill: white; " +
            "-fx-border-color: -color-border; " +
            "-fx-border-radius: %f; " +
            "-fx-padding: %f %f %f %f;"
            ,
            BORDER_RADIUS_SM,
            SPACING_2, SPACING_3, SPACING_2, SPACING_3 // Padding
        );
    }
    
    public static String createIconButtonStyle() {
        return String.format(
            "-fx-background-color: transparent; " +
            "-fx-background-radius: %f; " +
            "-fx-padding: %f; " +
            "-fx-cursor: hand; " +
            "-fx-text-fill: -color-text-secondary;"
            ,
            BORDER_RADIUS_FULL,
            SPACING_1
        );
    }
}
