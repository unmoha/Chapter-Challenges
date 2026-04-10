package com.translator.ui.controller;

import com.translator.shared.rmi.TranslationService;
import com.translator.shared.rmi.Language;
import com.translator.shared.rmi.DetectedLanguage;
import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.input.Clipboard;
import javafx.scene.input.ClipboardContent;
import javafx.scene.input.KeyCode;
import javafx.scene.input.KeyEvent;
import javafx.scene.layout.VBox;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.List;
import java.util.Optional;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.prefs.Preferences;

/**
 * Main controller for the translation application UI.
 */
public class MainController {
    private static final Logger log = LoggerFactory.getLogger(MainController.class);
    private static final String LAST_SOURCE_LANG = "lastSourceLang";
    private static final String LAST_TARGET_LANG = "lastTargetLang";
    
    private static final String RMI_HOST = "localhost";
    private static final int RMI_PORT = 1099;
    private static final String SERVICE_NAME = "TranslationService";
    
    private final ExecutorService executor = Executors.newCachedThreadPool();
    private final Preferences prefs = Preferences.userNodeForPackage(MainController.class);
    private TranslationService translationService;
    private final ObservableList<Language> languages = FXCollections.observableArrayList();
    private boolean isDarkTheme = false;
    
    @FXML private ComboBox<Language> sourceLanguageCombo;
    @FXML private ComboBox<Language> targetLanguageCombo;
    @FXML private TextArea sourceTextArea;
    @FXML private TextArea targetTextArea;
    @FXML private Label statusLabel;
    @FXML private ProgressBar progressBar;
    @FXML private VBox root;
    @FXML private Label characterCount;
    @FXML private Button translateButton;
    @FXML private Button copyButton;
    
    public void setTranslationService(TranslationService translationService) throws RemoteException {
        this.translationService = translationService;
        loadSupportedLanguages();
    }
    
    public void connectToServer() {
        try {
            Registry registry = LocateRegistry.getRegistry(RMI_HOST, RMI_PORT);
            this.translationService = (TranslationService) registry.lookup(SERVICE_NAME);
            log.info("Connected to RMI server at {}:{}/{}", RMI_HOST, RMI_PORT, SERVICE_NAME);
            loadSupportedLanguages();
        } catch (RemoteException | NotBoundException e) {
            log.error("Failed to connect to RMI server", e);
            String errorMessage = "Failed to connect to translation server: " + e.getMessage();
            Platform.runLater(() -> showError("Connection Error", new Exception(errorMessage)));
        }
    }
    
    @FXML
    public void initialize() throws RemoteException {
        log.info("Initializing MainController");
        setupLanguageComboBoxes();
        setupTextListeners();
        loadSupportedLanguages();
    }
    
    private void setupLanguageComboBoxes() {
        log.debug("Setting up language combo boxes");
        
        // Configure source language combo
        sourceLanguageCombo.setItems(languages);
        sourceLanguageCombo.setCellFactory(lv -> new LanguageListCell());
        sourceLanguageCombo.setButtonCell(new LanguageListCell());
        
        // Configure target language combo
        targetLanguageCombo.setItems(languages);
        targetLanguageCombo.setCellFactory(lv -> new LanguageListCell());
        targetLanguageCombo.setButtonCell(new LanguageListCell());
        
        // Set default languages if available
        String lastSourceLang = prefs.get(LAST_SOURCE_LANG, "en");
        String lastTargetLang = prefs.get(LAST_TARGET_LANG, "es");
        
        // Select the languages if they exist in the list
        languages.stream()
            .filter(lang -> lang.code().equals(lastSourceLang))
            .findFirst()
            .ifPresent(lang -> sourceLanguageCombo.getSelectionModel().select(lang));
            
        languages.stream()
            .filter(lang -> lang.code().equals(lastTargetLang))
            .findFirst()
            .ifPresent(lang -> targetLanguageCombo.getSelectionModel().select(lang));
            
        // If no selection, select first two languages
        if (languages.size() >= 2) {
            if (sourceLanguageCombo.getSelectionModel().getSelectedItem() == null) {
                sourceLanguageCombo.getSelectionModel().select(0);
            }
            if (targetLanguageCombo.getSelectionModel().getSelectedItem() == null) {
                targetLanguageCombo.getSelectionModel().select(1);
            }
        }
    }
    
    private void setupTextListeners() {
        log.debug("Setting up text listeners");
        
        // Update character count
        sourceTextArea.textProperty().addListener((obs, oldVal, newVal) -> {
            int count = newVal == null ? 0 : newVal.length();
            characterCount.setText(String.valueOf(count));
            translateButton.setDisable(count == 0);
        });
        
        // Add keyboard shortcuts
        sourceTextArea.addEventFilter(KeyEvent.KEY_PRESSED, event -> {
            if (event.isControlDown() && event.getCode() == KeyCode.ENTER) {
                try {
                    translateText();
                } catch (RemoteException e) {
                    throw new RuntimeException(e);
                }
                event.consume();
            } else if (event.isControlDown() && event.getCode() == KeyCode.L) {
                swapLanguages();
                event.consume();
            }
        });
        
        // Add listener to language selection changes
        sourceLanguageCombo.getSelectionModel().selectedItemProperty().addListener((obs, oldVal, newVal) -> {
            if (newVal != null) {
                prefs.put(LAST_SOURCE_LANG, newVal.code());
                if (sourceTextArea.getText() != null && !sourceTextArea.getText().trim().isEmpty()) {
                    try {
                        translateText();
                    } catch (RemoteException e) {
                        throw new RuntimeException(e);
                    }
                }
            }
        });
        
        targetLanguageCombo.getSelectionModel().selectedItemProperty().addListener((obs, oldVal, newVal) -> {
            if (newVal != null && sourceTextArea.getText() != null && !sourceTextArea.getText().trim().isEmpty()) {
                try {
                    translateText();
                } catch (RemoteException e) {
                    throw new RuntimeException(e);
                }
            }
        });
    }
    
    private void loadSupportedLanguages() {
        if (translationService == null) {
            log.warn("Translation service not initialized");
            return;
        }
        
        log.info("Loading supported languages");
        progressBar.setVisible(true);
        updateStatus("Loading languages...");
        
        // Execute in background thread to avoid blocking the UI
        executor.execute(() -> {
            try {
                List<Language> langs = translationService.getSupportedLanguages();
                Platform.runLater(() -> {
                    log.debug("Loaded {} languages", langs.size());
                    languages.setAll(langs);
                    restoreLastUsedLanguages();
                    progressBar.setVisible(false);
                    updateStatus("Ready");
                });
            } catch (Exception ex) {
                log.error("Failed to load languages", ex);
                Platform.runLater(() -> {
                    showError("Failed to load languages", ex);
                    progressBar.setVisible(false);
                });
            }
        });
    }
    
    private void restoreLastUsedLanguages() {
        String lastSource = prefs.get(LAST_SOURCE_LANG, "auto");
        String lastTarget = prefs.get(LAST_TARGET_LANG, "en");
        
        log.debug("Restoring last used languages: source={}, target={}", lastSource, lastTarget);
        
        // Set source language
        if ("auto".equals(lastSource)) {
            sourceLanguageCombo.getSelectionModel().clearSelection();
        } else {
            languages.stream()
                .filter(lang -> lang.code().equals(lastSource))
                .findFirst()
                .ifPresent(lang -> sourceLanguageCombo.getSelectionModel().select(lang));
        }
        
        // Set target language
        languages.stream()
            .filter(lang -> lang.code().equals(lastTarget))
            .findFirst()
            .ifPresent(lang -> targetLanguageCombo.getSelectionModel().select(lang));
    }
    
    @FXML
    private void translateText() throws RemoteException {
        String text = sourceTextArea.getText().trim();
        if (text.isEmpty() || targetLanguageCombo.getSelectionModel().isEmpty()) {
            return;
        }
        
        Language sourceLang = sourceLanguageCombo.getValue() != null ? 
            sourceLanguageCombo.getValue() : new Language("auto", "Auto Detect");
        Language targetLang = targetLanguageCombo.getValue();
        
        if (targetLang == null) {
            log.warn("No target language selected");
            return;
        }
        
        log.debug("Translating from {} to {}: {}", 
            sourceLang.code(), targetLang.code(), 
            text.substring(0, Math.min(50, text.length())) + (text.length() > 50 ? "..." : ""));
        
        // Show progress
        progressBar.setVisible(true);
        updateStatus("Translating...");
        
        // Save language preferences
        prefs.put(LAST_SOURCE_LANG, sourceLang.code());
        prefs.put(LAST_TARGET_LANG, targetLang.code());
        
        // Perform translation using RMI in background thread
        executor.execute(() -> {
            try {
                String translatedText = translationService.translateText(text, sourceLang.code(), targetLang.code());
                Platform.runLater(() -> {
                    log.debug("Translation completed");
                    targetTextArea.setText(translatedText);
                    updateStatus("Translation complete");
                    progressBar.setVisible(false);
                });
            } catch (Exception ex) {
                log.error("Translation failed", ex);
                Platform.runLater(() -> {
                    showError("Translation failed", ex);
                    progressBar.setVisible(false);
                });
            }
        });
    }
    
    @FXML
    private void swapLanguages() {
        log.debug("Swapping languages");
        
        Language currentSource = sourceLanguageCombo.getValue();
        Language currentTarget = targetLanguageCombo.getValue();
        
        if (currentTarget != null) {
            sourceLanguageCombo.setValue(currentTarget);
        }
        if (currentSource != null && !currentSource.equals(Language.AUTO)) {
            targetLanguageCombo.setValue(currentSource);
        }
        
        // Also swap text if both fields have content
        if (sourceTextArea.getText() != null && targetTextArea.getText() != null) {
            if (!sourceTextArea.getText().isEmpty() && !targetTextArea.getText().isEmpty()) {
                String temp = sourceTextArea.getText();
                sourceTextArea.setText(targetTextArea.getText());
                targetTextArea.setText(temp);
            } else if (!targetTextArea.getText().isEmpty()) {
                sourceTextArea.setText(targetTextArea.getText());
                targetTextArea.clear();
            }
        }
    }
    
    @FXML
    private void clearSourceText() {
        log.debug("Clearing source text");
        sourceTextArea.clear();
        targetTextArea.clear();
    }
    
    @FXML
    private void startVoiceInput() {
        log.info("Voice input requested");
        updateStatus("Voice input not implemented yet");
        // TODO: Implement voice input
    }
    
    @FXML
    private void captureImage() {
        log.info("Image capture requested");
        updateStatus("Image capture not implemented yet");
        // TODO: Implement image capture and OCR
    }
    
    @FXML
    private void speakTranslation() {
        log.info("Text-to-speech requested");
        updateStatus("Text-to-speech not implemented yet");
        // TODO: Implement text-to-speech
    }
    
    @FXML
    private void copyToClipboard() {
        if (targetTextArea.getText() != null && !targetTextArea.getText().isEmpty()) {
            log.debug("Copying to clipboard");
            ClipboardContent content = new ClipboardContent();
            content.putString(targetTextArea.getText());
            Clipboard.getSystemClipboard().setContent(content);
            updateStatus("Copied to clipboard");
        }
    }
    
    @FXML
    private void toggleTheme() {
        isDarkTheme = !isDarkTheme;
        log.debug("Toggling theme to: {}", isDarkTheme ? "dark" : "light");
        
        if (root == null) {
            log.warn("Root VBox is not initialized. Cannot apply theme change.");
            return;
        }
        
        try {
            if (isDarkTheme) {
                root.getStylesheets().clear();
                root.getStylesheets().add(getClass().getResource("/styles/dark-theme.css").toExternalForm());
            } else {
                root.getStylesheets().clear();
                root.getStylesheets().add(getClass().getResource("/styles/light-theme.css").toExternalForm());
            }
        } catch (Exception e) {
            log.error("Error applying theme", e);
        }
    }
    
    /**
     * Updates the status label with the given message.
     * This method is thread-safe and can be called from any thread.
     *
     * @param message The status message to display
     */
    public void updateStatus(String message) {
        if (Platform.isFxApplicationThread()) {
            statusLabel.setText(message);
        } else {
            Platform.runLater(() -> statusLabel.setText(message));
        }
    }
    
    private void showError(String message, Throwable throwable) {
        log.error("{}: {}", message, throwable.getMessage(), throwable);
        updateStatus(message + ": " + throwable.getMessage());
        
        // Show error dialog for significant errors
        if (!(throwable instanceof java.util.concurrent.CancellationException)) {
            Alert alert = new Alert(Alert.AlertType.ERROR);
            alert.setTitle("Error");
            alert.setHeaderText(message);
            alert.setContentText(throwable.getMessage());
            alert.showAndWait();
        }
    }

    public void showSettings(ActionEvent actionEvent) {
    }

    /**
     * Custom ListCell for displaying Language objects in ComboBox.
     */
    private static class LanguageListCell extends ListCell<Language> {
        @Override
        protected void updateItem(Language item, boolean empty) {
            super.updateItem(item, empty);
            if (empty || item == null) {
                setText(null);
            } else if (item.equals(Language.AUTO)) {
                setText("Auto Detect");
            } else {
                setText(String.format("%s (%s)", item.name(), item.code().toUpperCase()));
            }
        }
    }
}
