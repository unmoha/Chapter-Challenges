package com.translator.libretranslate.client.dto;

import com.fasterxml.jackson.annotation.JsonProperty;

/**
 * Data Transfer Object for translation requests to the LibreTranslate API.
 */
public record TranslationRequestDTO(
    @JsonProperty("q") String text,
    @JsonProperty("source") String source,
    @JsonProperty("target") String target,
    @JsonProperty("format") String format,
    @JsonProperty("api_key") String apiKey
) {
    public TranslationRequestDTO(String text, String source, String target) {
        this(text, source, target, "text", null);
    }
}
