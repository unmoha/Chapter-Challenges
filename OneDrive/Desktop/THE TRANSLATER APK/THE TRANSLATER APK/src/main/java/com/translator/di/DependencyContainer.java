package com.translator.di;

import com.translator.config.AppConfig;
import com.translator.core.service.TranslationService;
import com.translator.core.service.impl.LibreTranslateService;
import com.translator.libretranslate.client.LibreTranslateClient;
import javafx.fxml.FXMLLoader;
import javafx.util.Callback;

import java.io.IOException;
import java.util.Objects;
import java.util.concurrent.Executor;
import java.util.concurrent.Executors;

/**
 * Simple dependency injection container for the application.
 */
public class DependencyContainer {
    private static final DependencyContainer INSTANCE = new DependencyContainer();
    
    private final Executor backgroundExecutor;
    private final TranslationService translationService;
    private final AppConfig appConfig;
    
    private DependencyContainer() {
        // Initialize configuration
        this.appConfig = new AppConfig();
        
        // Initialize background executor
        this.backgroundExecutor = Executors.newCachedThreadPool();
        
        // Initialize services
        this.translationService = createTranslationService();
    }
    
    public static DependencyContainer getInstance() {
        return INSTANCE;
    }
    
    public TranslationService getTranslationService() {
        return translationService;
    }
    
    public Executor getBackgroundExecutor() {
        return backgroundExecutor;
    }
    
    public AppConfig getAppConfig() {
        return appConfig;
    }
    
    /**
     * Creates a controller factory that injects dependencies into controllers.
     */
    public Callback<Class<?>, Object> getControllerFactory() {
        return clazz -> {
            try {
                // Create a new instance of the controller
                Object controller = clazz.getDeclaredConstructor().newInstance();
                
                // Here you can inject dependencies into the controller
                // For example:
                // if (controller instanceof MainController) {
                //     ((MainController) controller).setTranslationService(translationService);
                // }
                
                return controller;
            } catch (Exception e) {
                throw new RuntimeException("Failed to create controller: " + clazz.getName(), e);
            }
        };
    }
    
    /**
     * Loads an FXML file with dependency injection.
     */
    public <T> T loadFXML(String fxmlPath) throws IOException {
        FXMLLoader loader = new FXMLLoader();
        loader.setControllerFactory(getControllerFactory());
        loader.setLocation(Objects.requireNonNull(
            getClass().getResource(fxmlPath)
        ));
        return loader.load();
    }
    
    /**
     * Creates a new translation service with the current configuration.
     */
    public TranslationService createTranslationService() {
        return new LibreTranslateService(
            appConfig.getApiUrl(),
            appConfig.getApiKey()
        );
    }
}
