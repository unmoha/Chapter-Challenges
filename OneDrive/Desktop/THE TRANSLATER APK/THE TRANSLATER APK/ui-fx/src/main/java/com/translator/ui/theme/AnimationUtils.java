package com.translator.ui.theme;

import javafx.animation.*;
import javafx.scene.Node;
import javafx.scene.effect.DropShadow;
import javafx.scene.effect.GaussianBlur;
import javafx.scene.effect.Glow;
import javafx.scene.paint.Color;
import javafx.util.Duration;

/**
 * Utility class for creating and managing animations.
 */
public class AnimationUtils {
    // Animation durations (in milliseconds)
    public static final int DURATION_FAST = 100;
    public static final int DURATION_NORMAL = 200;
    public static final int DURATION_SLOW = 300;
    public static final int DURATION_VERY_SLOW = 500;
    
    // Common interpolators
    public static final Interpolator EASE_BOTH = Interpolator.SPLINE(0.4, 0, 0.2, 1);
    public static final Interpolator EASE_OUT = Interpolator.EASE_OUT;
    public static final Interpolator EASE_IN = Interpolator.EASE_IN;
    
    // Private constructor to prevent instantiation
    private AnimationUtils() {}
    
    /**
     * Creates a fade transition for a node.
     * @param node The node to animate
     * @param fromValue Starting opacity (0.0 to 1.0)
     * @param toValue Ending opacity (0.0 to 1.0)
     * @param durationMs Duration in milliseconds
     * @return The configured FadeTransition
     */
    public static FadeTransition fade(Node node, double fromValue, double toValue, int durationMs) {
        FadeTransition ft = new FadeTransition(Duration.millis(durationMs), node);
        ft.setFromValue(fromValue);
        ft.setToValue(toValue);
        ft.setInterpolator(EASE_BOTH);
        return ft;
    }
    
    /**
     * Creates a fade-in animation.
     * @param node The node to animate
     * @param durationMs Duration in milliseconds
     * @return The configured FadeTransition
     */
    public static FadeTransition fadeIn(Node node, int durationMs) {
        return fade(node, 0.0, 1.0, durationMs);
    }
    
    /**
     * Creates a fade-out animation.
     * @param node The node to animate
     * @param durationMs Duration in milliseconds
     * @return The configured FadeTransition
     */
    public static FadeTransition fadeOut(Node node, int durationMs) {
        return fade(node, 1.0, 0.0, durationMs);
    }
    
    /**
     * Creates a slide animation for a node.
     * @param node The node to animate
     * @param fromX Starting X position
     * @param toX Ending X position
     * @param fromY Starting Y position
     * @param toY Ending Y position
     * @param durationMs Duration in milliseconds
     * @return The configured TranslateTransition
     */
    public static TranslateTransition slide(Node node, double fromX, double toX, double fromY, double toY, int durationMs) {
        TranslateTransition tt = new TranslateTransition(Duration.millis(durationMs), node);
        tt.setFromX(fromX);
        tt.setToX(toX);
        tt.setFromY(fromY);
        tt.setToY(toY);
        tt.setInterpolator(EASE_BOTH);
        return tt;
    }
    
    /**
     * Creates a slide-in animation from the left.
     * @param node The node to animate
     * @param distance Distance to slide
     * @param durationMs Duration in milliseconds
     * @return The configured TranslateTransition
     */
    public static TranslateTransition slideInFromLeft(Node node, double distance, int durationMs) {
        node.setTranslateX(-distance);
        return slide(node, -distance, 0, 0, 0, durationMs);
    }
    
    /**
     * Creates a slide-in animation from the right.
     * @param node The node to animate
     * @param distance Distance to slide
     * @param durationMs Duration in milliseconds
     * @return The configured TranslateTransition
     */
    public static TranslateTransition slideInFromRight(Node node, double distance, int durationMs) {
        node.setTranslateX(distance);
        return slide(node, distance, 0, 0, 0, durationMs);
    }
    
    /**
     * Creates a scale animation for a node.
     * @param node The node to animate
     * @param fromX Starting X scale
     * @param toX Ending X scale
     * @param fromY Starting Y scale
     * @param toY Ending Y scale
     * @param durationMs Duration in milliseconds
     * @return The configured ScaleTransition
     */
    public static ScaleTransition scale(Node node, double fromX, double toX, double fromY, double toY, int durationMs) {
        ScaleTransition st = new ScaleTransition(Duration.millis(durationMs), node);
        st.setFromX(fromX);
        st.setToX(toX);
        st.setFromY(fromY);
        st.setToY(toY);
        st.setInterpolator(EASE_BOTH);
        return st;
    }
    
    /**
     * Creates a pop-in animation.
     * @param node The node to animate
     * @param durationMs Duration in milliseconds
     * @return The configured ScaleTransition
     */
    public static ScaleTransition popIn(Node node, int durationMs) {
        node.setScaleX(0);
        node.setScaleY(0);
        return scale(node, 0, 1, 0, 1, durationMs);
    }
    
    /**
     * Creates a pulse animation for a button or other interactive element.
     * @param node The node to animate
     * @param scaleFactor How much to scale (e.g., 1.1 for 10% larger)
     * @param durationMs Duration in milliseconds
     * @return The configured SequentialTransition
     */
    public static SequentialTransition createPulseAnimation(Node node, double scaleFactor, int durationMs) {
        ScaleTransition scaleUp = new ScaleTransition(Duration.millis(durationMs / 2), node);
        scaleUp.setToX(scaleFactor);
        scaleUp.setToY(scaleFactor);
        scaleUp.setInterpolator(EASE_OUT);
        
        ScaleTransition scaleDown = new ScaleTransition(Duration.millis(durationMs / 2), node);
        scaleDown.setToX(1.0);
        scaleDown.setToY(1.0);
        scaleDown.setInterpolator(EASE_IN);
        
        return new SequentialTransition(node, scaleUp, scaleDown);
    }
    
    /**
     * Creates a hover glow effect for a node.
     * @param node The node to apply the effect to
     * @param color The glow color
     * @param intensity The intensity of the glow (0.0 to 1.0)
     */
    public static void addHoverGlow(Node node, Color color, double intensity) {
        Glow glow = new Glow(intensity);
        glow.setInput(new DropShadow(10, color));
        
        node.hoverProperty().addListener((obs, oldVal, isHovering) -> {
            FadeTransition ft = new FadeTransition(Duration.millis(DURATION_NORMAL), node);
            if (isHovering) {
                node.setEffect(glow);
                ft.setFromValue(0.0);
                ft.setToValue(1.0);
            } else {
                ft.setFromValue(1.0);
                ft.setToValue(0.0);
                ft.setOnFinished(e -> node.setEffect(null));
            }
            ft.play();
        });
    }
    
    /**
     * Creates a press animation for a button.
     * @param node The node to animate
     * @return The configured ScaleTransition
     */
    public static ScaleTransition createPressAnimation(Node node) {
        ScaleTransition st = new ScaleTransition(Duration.millis(DURATION_FAST), node);
        st.setToX(0.95);
        st.setToY(0.95);
        st.setAutoReverse(true);
        st.setCycleCount(2);
        return st;
    }
    
    /**
     * Creates a shake animation for error feedback.
     * @param node The node to animate
     * @return The configured Timeline
     */
    public static Timeline createShakeAnimation(Node node) {
        Timeline timeline = new Timeline(
            new KeyFrame(Duration.millis(0), new KeyValue(node.translateXProperty(), 0, EASE_OUT)),
            new KeyFrame(Duration.millis(50), new KeyValue(node.translateXProperty(), -10, EASE_OUT)),
            new KeyFrame(Duration.millis(100), new KeyValue(node.translateXProperty(), 10, EASE_OUT)),
            new KeyFrame(Duration.millis(150), new KeyValue(node.translateXProperty(), -10, EASE_OUT)),
            new KeyFrame(Duration.millis(200), new KeyValue(node.translateXProperty(), 10, EASE_OUT)),
            new KeyFrame(Duration.millis(250), new KeyValue(node.translateXProperty(), -5, EASE_OUT)),
            new KeyFrame(Duration.millis(300), new KeyValue(node.translateXProperty(), 5, EASE_OUT)),
            new KeyFrame(Duration.millis(350), new KeyValue(node.translateXProperty(), 0, EASE_OUT))
        );
        
        // Add a red flash effect
        Glow glow = new Glow(0.5);
        glow.setInput(new GaussianBlur(20));
        
        Timeline flashTimeline = new Timeline(
            new KeyFrame(Duration.ZERO, new KeyValue(node.effectProperty(), null)),
            new KeyFrame(Duration.millis(50), new KeyValue(node.effectProperty(), glow)),
            new KeyFrame(Duration.millis(350), new KeyValue(node.effectProperty(), null))
        );
        
        ParallelTransition pt = new ParallelTransition(timeline, flashTimeline);
        pt.setCycleCount(1);
        
        return timeline;
    }
    
    /**
     * Creates a loading spinner animation.
     * @param size The size of the spinner in pixels
     * @param color The color of the spinner
     * @return A RotateTransition for the spinner
     */
    public static RotateTransition createLoadingSpinner(double size, Color color) {
        // In a real implementation, you would create a custom spinner node here
        // and return a rotation animation for it
        RotateTransition rt = new RotateTransition(Duration.millis(1000));
        rt.setByAngle(360);
        rt.setCycleCount(Animation.INDEFINITE);
        rt.setInterpolator(Interpolator.LINEAR);
        return rt;
    }
}
