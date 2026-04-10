package com.translator.core.model;

/**
 * Represents the result of a translation operation.
 * 
 * @param translatedText The translated text
 * @param detectedSourceLanguage The detected source language (if auto-detection was used)
 * @param confidence The confidence score of the translation (0.0 to 1.0)
 */
public record TranslationResult(
    String translatedText,
    Language detectedSourceLanguage,
    double confidence
) {
    public TranslationResult {
        if (confidence < 0.0 || confidence > 1.0) {
            throw new IllegalArgumentException("Confidence must be between 0.0 and 1.0");
        }
    }
}
