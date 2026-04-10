package com.translator.ui.dialogs;

import com.translator.config.AppConfig;
import javafx.geometry.Insets;
import javafx.scene.control.*;
import javafx.scene.layout.GridPane;

/**
 * Dialog for configuring application settings.
 */
public class SettingsDialog extends Dialog<Boolean> {
    private final TextField apiUrlField;
    private final PasswordField apiKeyField;
    private final AppConfig config;
    private boolean settingsChanged = false;
    
    public SettingsDialog() {
        this.config = new AppConfig();
        
        setTitle("Settings");
        setHeaderText("API Configuration");
        
        // Create the dialog pane
        GridPane grid = new GridPane();
        grid.setHgap(10);
        grid.setVgap(10);
        grid.setPadding(new Insets(20, 150, 10, 10));
        
        // Add fields
        apiUrlField = new TextField(config.getApiUrl());
        apiKeyField = new PasswordField();
        apiKeyField.setText(config.getApiKey());
        
        grid.add(new Label("API URL:"), 0, 0);
        grid.add(apiUrlField, 1, 0);
        grid.add(new Label("API Key (if required):"), 0, 1);
        grid.add(apiKeyField, 1, 1);
        
        getDialogPane().setContent(grid);
        
        // Add buttons
        ButtonType saveButtonType = new ButtonType("Save", ButtonBar.ButtonData.OK_DONE);
        getDialogPane().getButtonTypes().addAll(saveButtonType, ButtonType.CANCEL);
        
        // Handle save button
        setResultConverter(buttonType -> {
            if (buttonType == saveButtonType) {
                return saveSettings();
            }
            return false;
        });
    }
    
    private boolean saveSettings() {
        String newUrl = apiUrlField.getText().trim();
        String newKey = apiKeyField.getText().trim();
        
        if (!newUrl.equals(config.getApiUrl()) || !newKey.equals(config.getApiKey())) {
            config.setApiUrl(newUrl);
            config.setApiKey(newKey);
            settingsChanged = true;
        }
        return settingsChanged;
    }
    
    public boolean isSettingsChanged() {
        return settingsChanged;
    }
}
