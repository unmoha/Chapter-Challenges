package com.translator.core.service.impl;

import com.translator.core.model.Language;
import com.translator.core.model.TranslationRequest;
import com.translator.core.model.TranslationResult;
import com.translator.core.service.TranslationService;
import com.translator.libretranslate.client.LibreTranslateClient;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.util.List;
import java.util.Map;
import java.util.concurrent.CompletableFuture;
import java.util.concurrent.ConcurrentHashMap;

/**
 * Implementation of TranslationService using the LibreTranslate API.
 */
public class LibreTranslateService implements TranslationService {
    private static final Logger log = LoggerFactory.getLogger(LibreTranslateService.class);
    
    private final LibreTranslateClient client;
    private final Map<String, Language> languageCache = new ConcurrentHashMap<>();
    private volatile boolean languagesLoaded = false;
    
    public LibreTranslateService(String baseUrl) {
        this(baseUrl, null);
    }
    
    public LibreTranslateService(String baseUrl, String apiKey) {
        this.client = new LibreTranslateClient(baseUrl, apiKey);
        // Preload languages
        preloadLanguages();
    }
    
    @Override
    public CompletableFuture<TranslationResult> translate(TranslationRequest request) {
        // If source language is AUTO, detect it first
        if (request.sourceLanguage().equals(Language.AUTO)) {
            return detectAndTranslate(request);
        }
        
        return client.translate(request)
            .thenApply(result -> {
                // Update the detected language name if we have it in cache
                if (languageCache.containsKey(result.detectedSourceLanguage().code())) {
                    Language cached = languageCache.get(result.detectedSourceLanguage().code());
                    return new TranslationResult(
                        result.translatedText(),
                        cached,
                        result.confidence()
                    );
                }
                return result;
            });
    }
    
    @Override
    public CompletableFuture<List<Language>> getSupportedLanguages() {
        if (languagesLoaded) {
            return CompletableFuture.completedFuture(List.copyOf(languageCache.values()));
        }
        
        return client.getSupportedLanguages()
            .thenApply(languages -> {
                languageCache.clear();
                languages.forEach(lang -> languageCache.put(lang.code(), lang));
                languagesLoaded = true;
                return languages;
            });
    }
    
    @Override
    public boolean isLanguageDetectionSupported() {
        return true;
    }
    
    private CompletableFuture<TranslationResult> detectAndTranslate(TranslationRequest request) {
        return client.detectLanguage(request.text())
            .thenCompose(detectedLanguage -> {
                // Create a new request with the detected language
                TranslationRequest newRequest = new TranslationRequest(
                    request.text(),
                    detectedLanguage,
                    request.targetLanguage()
                );
                
                // Now translate with the detected language
                return client.translate(newRequest)
                    .thenApply(result -> {
                        // Update the detected language name if we have it in cache
                        if (languageCache.containsKey(detectedLanguage.code())) {
                            Language cached = languageCache.get(detectedLanguage.code());
                            return new TranslationResult(
                                result.translatedText(),
                                cached,
                                result.confidence()
                            );
                        }
                        return result;
                    });
            });
    }
    
    private void preloadLanguages() {
        getSupportedLanguages()
            .exceptionally(ex -> {
                log.warn("Failed to preload languages: {}", ex.getMessage());
                return List.of();
            });
    }
}
