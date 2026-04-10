package com.translator.libretranslate.client.dto;

import com.fasterxml.jackson.annotation.JsonProperty;

/**
 * Data Transfer Object for language information from the LibreTranslate API.
 */
public record LanguageDTO(
    @JsonProperty("code") String code,
    @JsonProperty("name") String name
) {}
