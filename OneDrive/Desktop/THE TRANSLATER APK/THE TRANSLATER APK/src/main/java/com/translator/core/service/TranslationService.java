package com.translator.core.service;

import com.translator.core.model.Language;
import com.translator.core.model.TranslationRequest;
import com.translator.core.model.TranslationResult;

import java.util.List;
import java.util.concurrent.CompletableFuture;

/**
 * Defines the contract for translation services.
 */
public interface TranslationService {
    
    /**
     * Translates the given text from the source language to the target language.
     * 
     * @param request The translation request containing text and language information
     * @return A CompletableFuture that will complete with the translation result
     * @throws IllegalArgumentException if the request is invalid
     */
    CompletableFuture<TranslationResult> translate(TranslationRequest request);
    
    /**
     * Gets the list of supported languages for translation.
     * 
     * @return A CompletableFuture that will complete with the list of supported languages
     */
    CompletableFuture<List<Language>> getSupportedLanguages();
    
    /**
     * Checks if the service supports automatic language detection.
     * 
     * @return true if language detection is supported, false otherwise
     */
    boolean isLanguageDetectionSupported();
}
