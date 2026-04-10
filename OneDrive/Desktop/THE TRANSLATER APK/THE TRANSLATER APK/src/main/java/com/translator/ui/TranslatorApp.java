package com.translator.ui;

import com.translator.shared.rmi.TranslationService;
import com.translator.ui.controller.MainController;
import javafx.application.Application;
import javafx.application.Platform;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.ButtonType;
import javafx.stage.Stage;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.util.List;
import java.util.Objects;
import com.translator.shared.rmi.Language;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;

/**
 * Main application class for the OpenTranslate application.
 */
public class TranslatorApp extends Application {
    private static final Logger log = LoggerFactory.getLogger(TranslatorApp.class);
    private static final String APP_TITLE = "OpenTranslate";
    private static final double WINDOW_WIDTH = 1000;
    private static final double WINDOW_HEIGHT = 700;
    private static final String SERVER_HOST = "localhost";
    private static final int SERVER_PORT = 1099;
    private static final String SERVICE_NAME = "TranslationService";
    
    private MainController mainController;
    
    @Override
    public void start(Stage primaryStage) {
        try {
            // Load the main FXML file
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/main.fxml"));
            Parent root = loader.load();
            
            mainController = loader.getController();
            connectToServer();
            
            // Set up the primary stage
            primaryStage.setTitle(APP_TITLE);
            Scene scene = new Scene(root, WINDOW_WIDTH, WINDOW_HEIGHT);
            primaryStage.setScene(scene);
            primaryStage.setMinWidth(800);
            primaryStage.setMinHeight(600);
            
            // Apply CSS
            String mainCss = Objects.requireNonNull(
                getClass().getResource("/styles/main.css")
            ).toExternalForm();
            scene.getStylesheets().add(mainCss);
            
            // Set up window close handler
            primaryStage.setOnCloseRequest(event -> {
                event.consume();
                confirmExit(primaryStage);
            });
            
            // Show the stage
            primaryStage.show();
            
        } catch (Exception e) {
            log.error("Failed to start application", e);
            showError("Startup Error", "Failed to start application: " + e.getMessage());
            Platform.exit();
        }
    }
    
    private void connectToServer() {
        log.info("Connecting to RMI server at {}:{}/{}", SERVER_HOST, SERVER_PORT, SERVICE_NAME);
        
        new Thread(() -> {
            try {
                Registry registry = LocateRegistry.getRegistry(SERVER_HOST, SERVER_PORT);
                TranslationService service = (TranslationService) registry.lookup(SERVICE_NAME);
                
                // Test the connection by getting supported languages
                List<Language> languages = service.getSupportedLanguages();
                log.info("Successfully connected to RMI server. Found {} supported languages.", languages.size());
                
                Platform.runLater(() -> {
                    try {
                        mainController.setTranslationService(service);
                        mainController.updateStatus("Connected to translation server");
                    } catch (Exception e) {
                        log.error("Failed to set translation service", e);
                        showError("Connection Error", "Failed to initialize translation service: " + e.getMessage());
                    }
                });
                
            } catch (RemoteException e) {
                log.error("RMI communication error", e);
                Platform.runLater(() -> 
                    showError("Connection Error", 
                        "Failed to communicate with server: " + e.getMessage())
                );
            } catch (NotBoundException e) {
                log.error("Service not found", e);
                Platform.runLater(() -> showError("Service Error", 
                    "Translation service not found. Make sure the server is running."));
            } catch (Exception e) {
                log.error("Failed to connect to RMI server", e);
                Platform.runLater(() -> showError("Connection Error", 
                    "Failed to connect to translation server: " + e.getMessage()));
            }
        }).start();
    }
    
    private void confirmExit(Stage stage) {
        Alert alert = new Alert(Alert.AlertType.CONFIRMATION);
        alert.setTitle("Exit " + APP_TITLE);
        alert.setHeaderText("Are you sure you want to exit?");
        alert.setContentText("Any unsaved changes will be lost.");
        
        // Customize button text
        ButtonType yesButton = new ButtonType("Yes");
        ButtonType noButton = new ButtonType("No");
        alert.getButtonTypes().setAll(yesButton, noButton);
        
        Optional<ButtonType> result = alert.showAndWait();
        if (result.isPresent() && result.get() == yesButton) {
            // Save any settings or cleanup here
            stage.close();
            Platform.exit();
        }
    }
    
    private void showError(String title, String message) {
        if (!Platform.isFxApplicationThread()) {
            Platform.runLater(() -> showError(title, message));
            return;
        }
        
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("Error");
        alert.setHeaderText(title);
        alert.setContentText(message);
        alert.showAndWait();
    }
    
    private void showError(String title, Throwable throwable) {
        if (throwable == null) {
            showError(title, "An unknown error occurred");
            return;
        }
        
        log.error("Error: {}", title, throwable);
        String message = throwable.getMessage() != null ? throwable.getMessage() : throwable.toString();
        
        if (!Platform.isFxApplicationThread()) {
            Platform.runLater(() -> showError(title, message));
        } else {
            showError(title, message);
        }
    }
    
    private void showStatus(String message) {
        if (mainController != null) {
            mainController.updateStatus(message);
        }
    }

    public static void main(String[] args) {
        try {
            // Set up uncaught exception handler
            Thread.setDefaultUncaughtExceptionHandler((thread, throwable) -> {
                log.error("Uncaught exception in thread " + thread.getName(), throwable);
                if (Platform.isFxApplicationThread()) {
                    showErrorInFXThread(throwable);
                } else {
                    Platform.runLater(() -> showErrorInFXThread(throwable));
                }
            });
            
            // Launch the application
            launch(args);
            
        } catch (Exception e) {
            log.error("Fatal error in application", e);
            System.err.println("Fatal error: " + e.getMessage());
            e.printStackTrace();
            System.exit(1);
        }
    }
    
    private static void showErrorInFXThread(Throwable throwable) {
        Alert alert = new Alert(Alert.AlertType.ERROR);
        alert.setTitle("Error");
        alert.setHeaderText("An unexpected error occurred");
        
        String message = throwable.getMessage();
        if (message == null || message.isEmpty()) {
            message = throwable.toString();
        }
        
        alert.setContentText(message);
        alert.showAndWait();
    }
}
