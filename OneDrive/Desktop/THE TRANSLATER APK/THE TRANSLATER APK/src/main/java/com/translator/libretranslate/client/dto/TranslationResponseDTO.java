package com.translator.libretranslate.client.dto;

import com.fasterxml.jackson.annotation.JsonProperty;

/**
 * Data Transfer Object for translation responses from the LibreTranslate API.
 */
public record TranslationResponseDTO(
    @JsonProperty("translatedText") String translatedText,
    @JsonProperty("detectedLanguage") DetectedLanguageDTO detectedLanguage
) {
    public record DetectedLanguageDTO(
        @JsonProperty("language") String language,
        @JsonProperty("confidence") double confidence
    ) {}
}
