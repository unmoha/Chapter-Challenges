package com.translator.ui.components;

import com.translator.ui.theme.AnimationUtils;
import com.translator.ui.theme.FontManager;
import com.translator.ui.theme.UIStyles;
import javafx.animation.Animation;
import javafx.animation.FadeTransition;
import javafx.animation.PauseTransition;
import javafx.animation.RotateTransition;
import javafx.animation.SequentialTransition;
import javafx.geometry.Insets;
import javafx.geometry.Pos;
import javafx.scene.Node;
import javafx.scene.control.*;
import javafx.scene.effect.DropShadow;
import javafx.scene.layout.*;
import javafx.scene.paint.Color;
import javafx.scene.text.Text;
import javafx.scene.text.TextFlow;
import javafx.util.Duration;

/**
 * Provides factory methods for creating styled UI components.
 */
public final class UIComponents {
    
    // Private constructor to prevent instantiation
    private UIComponents() {}
    
    /**
     * Creates a styled button with the specified text.
     * @param text The button text
     * @return A styled Button
     */
    public static Button createButton(String text) {
        Button button = new Button(text);
        styleButton(button);
        return button;
    }
    
    /**
     * Creates a primary action button with the specified text.
     * @param text The button text
     * @return A styled primary Button
     */
    public static Button createPrimaryButton(String text) {
        Button button = new Button(text);
        button.getStyleClass().add("primary-button");
        styleButton(button);
        return button;
    }
    
    /**
     * Creates a secondary (outlined) button with the specified text.
     * @param text The button text
     * @return A styled secondary Button
     */
    public static Button createSecondaryButton(String text) {
        Button button = new Button(text);
        button.getStyleClass().add("secondary-button");
        styleButton(button);
        return button;
    }
    
    /**
     * Applies common button styles and effects.
     * @param button The button to style
     */
    private static void styleButton(ButtonBase button) {
        button.setFont(FontManager.primaryFont(FontManager.BODY));
        button.setPadding(new Insets(8, 16, 8, 16));
        button.setMinHeight(40);
        
        // Add hover and press effects
        button.hoverProperty().addListener((obs, oldVal, isHovering) -> {
            if (isHovering) {
                button.setEffect(new DropShadow(8, Color.color(0, 0, 0, 0.2)));
            } else {
                button.setEffect(null);
            }
        });
        
        // Add press animation
        button.setOnMousePressed(e -> {
            Animation pressAnim = AnimationUtils.createPressAnimation(button);
            pressAnim.play();
        });
    }
    
    /**
     * Creates a styled text field with optional prompt text.
     * @param promptText The prompt text to display when the field is empty
     * @return A styled TextField
     */
    public static TextField createTextField(String promptText) {
        TextField textField = new TextField();
        styleTextInput(textField);
        textField.setPromptText(promptText);
        return textField;
    }
    
    /**
     * Creates a styled password field with optional prompt text.
     * @param promptText The prompt text to display when the field is empty
     * @return A styled PasswordField
     */
    public static PasswordField createPasswordField(String promptText) {
        PasswordField passwordField = new PasswordField();
        styleTextInput(passwordField);
        passwordField.setPromptText(promptText);
        return passwordField;
    }
    
    /**
     * Creates a styled text area with optional prompt text.
     * @param promptText The prompt text to display when the area is empty
     * @return A styled TextArea
     */
    public static TextArea createTextArea(String promptText) {
        TextArea textArea = new TextArea();
        styleTextInput(textArea);
        textArea.setPromptText(promptText);
        textArea.setWrapText(true);
        return textArea;
    }
    
    /**
     * Applies common styles to text input controls.
     * @param input The text input control to style
     */
    private static void styleTextInput(TextInputControl input) {
        input.getStyleClass().add("text-input");
        input.setFont(FontManager.primaryFont(FontManager.BODY));
        input.setPadding(new Insets(12));
        
        // Add focus effects
        input.focusedProperty().addListener((obs, oldVal, hasFocus) -> {
            if (hasFocus) {
                input.setStyle(input.getStyle() + "-fx-border-color: -color-primary;");
            } else {
                input.setStyle(input.getStyle().replace("-fx-border-color: -color-primary;", ""));
            }
        });
    }
    
    /**
     * Creates a styled label with optional font size and weight.
     * @param text The label text
     * @param size The font size
     * @param weight The font weight
     * @return A styled Label
     */
    public static Label createLabel(String text, double size, FontManager.FontWeight weight) {
        Label label = new Label(text);
        label.setFont(FontManager.primaryFont(size, weight));
        label.setTextFill(javafx.scene.paint.Color.web("-color-text-primary"));
        return label;
    }
    
    /**
     * Creates a heading label with the specified level (h1-h4).
     * @param text The heading text
     * @param level The heading level (1-4)
     * @return A styled heading Label
     */
    public static Label createHeading(String text, int level) {
        double size;
        FontManager.FontWeight weight = FontManager.BOLD;
        
        switch (level) {
            case 1:
                size = FontManager.H1;
                break;
            case 2:
                size = FontManager.H2;
                break;
            case 3:
                size = FontManager.H3;
                break;
            case 4:
                size = FontManager.H4;
                weight = FontManager.SEMI_BOLD;
                break;
            default:
                size = FontManager.H4;
        }
        
        Label heading = createLabel(text, size, weight);
        heading.getStyleClass().add("heading-" + level);
        return heading;
    }
    
    /**
     * Creates a card container with the specified content.
     * @param content The content to display in the card
     * @return A styled VBox containing the card content
     */
    public static VBox createCard(Node... content) {
        VBox card = new VBox(content);
        card.getStyleClass().add("card");
        card.setSpacing(12);
        card.setPadding(new Insets(16));
        return card;
    }
    
    /**
     * Creates a loading indicator.
     * @param size The size of the loading indicator
     * @return A Node containing the loading animation
     */
    public static Node createLoadingIndicator(double size) {
        ProgressIndicator indicator = new ProgressIndicator();
        indicator.setPrefSize(size, size);
        indicator.setMinSize(size, size);
        indicator.setMaxSize(size, size);
        
        // Add rotation animation
        RotateTransition rotate = new RotateTransition(Duration.seconds(1), indicator);
        rotate.setByAngle(360);
        rotate.setCycleCount(Animation.INDEFINITE);
        rotate.play();
        
        return indicator;
    }
    
    /**
     * Creates a toast notification.
     * @param message The message to display
     * @param type The type of notification (success, error, info, warning)
     * @return A StackPane containing the toast notification
     */
    public static StackPane createToast(String message, String type) {
        Label label = new Label(message);
        label.setStyle("-fx-text-fill: white; -fx-font-weight: 500;");
        
        String backgroundColor;
        switch (type.toLowerCase()) {
            case "success":
                backgroundColor = "-color-success";
                break;
            case "error":
                backgroundColor = "-color-error";
                break;
            case "warning":
                backgroundColor = "-color-warning";
                break;
            case "info":
            default:
                backgroundColor = "-color-info";
        }
        
        StackPane toast = new StackPane(label);
        toast.getStyleClass().add("toast");
        toast.setStyle(
            "-fx-background-color: " + backgroundColor + "; " +
            "-fx-background-radius: 8px; " +
            "-fx-padding: 12px 24px; " +
            "-fx-effect: dropshadow(gaussian, rgba(0,0,0,0.2), 10, 0, 0, 2);"
        );
        
        // Set up animation
        toast.setOpacity(0);
        toast.setTranslateY(20);
        
        FadeTransition fadeIn = new FadeTransition(Duration.millis(200), toast);
        fadeIn.setToValue(1);
        
        FadeTransition fadeOut = new FadeTransition(Duration.millis(200), toast);
        fadeOut.setToValue(0);
        
        PauseTransition pause = new PauseTransition(Duration.seconds(3));
        
        SequentialTransition sequence = new SequentialTransition(
            fadeIn,
            pause,
            fadeOut
        );
        
        // Auto-hide after animation
        sequence.setOnFinished(e -> {
            if (toast.getParent() != null) {
                ((Pane) toast.getParent()).getChildren().remove(toast);
            }
        });
        
        // Start animation when added to scene
        toast.sceneProperty().addListener((obs, oldScene, newScene) -> {
            if (newScene != null) {
                sequence.play();
            }
        });
        
        return toast;
    }
    
    /**
     * Creates a horizontal divider.
     * @return A styled Region acting as a divider
     */
    public static Region createDivider() {
        Region divider = new Region();
        divider.setPrefHeight(1);
        divider.setMaxHeight(1);
        divider.setStyle("-fx-background-color: -color-border;");
        return divider;
    }
    
    /**
     * Creates a spacer that can be used to push elements apart in a layout.
     * @return A Region that will expand to fill available space
     */
    public static Region createSpacer() {
        Region spacer = new Region();
        HBox.setHgrow(spacer, Priority.ALWAYS);
        VBox.setVgrow(spacer, Priority.ALWAYS);
        return spacer;
    }
    
    /**
     * Creates a button with an icon and optional text.
     * @param icon The icon character or text
     * @param text The button text (optional)
     * @return A styled Button with an icon
     */
    public static Button createIconButton(String icon, String text) {
        Button button = new Button(text);
        
        if (icon != null && !icon.isEmpty()) {
            Text iconNode = new Text(icon);
            iconNode.setFont(FontManager.loadMaterialIcons(20));
            button.setGraphic(iconNode);
            
            if (text != null && !text.isEmpty()) {
                button.setGraphicTextGap(8);
            }
        }
        
        styleButton(button);
        return button;
    }
    
    /**
     * Creates a form field with a label and input control.
     * @param labelText The field label text
     * @param input The input control
     * @return A VBox containing the label and input
     */
    public static VBox createFormField(String labelText, Node input) {
        Label label = createLabel(labelText, FontManager.BODY, FontManager.MEDIUM);
        VBox container = new VBox(4, label, input);
        container.setAlignment(Pos.TOP_LEFT);
        return container;
    }
    
    /**
     * Creates a toolbar with the specified actions.
     * @param actions The actions to include in the toolbar
     * @return A styled ToolBar
     */
    public static ToolBar createToolBar(Node... actions) {
        ToolBar toolBar = new ToolBar(actions);
        toolBar.getStyleClass().add("tool-bar");
        toolBar.setPadding(new Insets(8));
        toolBar.setStyle("-fx-background-color: -color-surface;");
        return toolBar;
    }
}
