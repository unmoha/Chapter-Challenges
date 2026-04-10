package com.translator.core.model;

/**
 * Represents a translation request containing the text to be translated
 * and the source and target languages.
 * 
 * @param text The text to be translated
 * @param sourceLanguage The source language (can be Language.AUTO for auto-detection)
 * @param targetLanguage The target language for translation
 */
public record TranslationRequest(
    String text,
    Language sourceLanguage,
    Language targetLanguage
) {
    /**
     * Creates a new TranslationRequest with auto-detection of the source language.
     */
    public static TranslationRequest autoDetect(String text, Language targetLanguage) {
        return new TranslationRequest(text, Language.AUTO, targetLanguage);
    }
}
