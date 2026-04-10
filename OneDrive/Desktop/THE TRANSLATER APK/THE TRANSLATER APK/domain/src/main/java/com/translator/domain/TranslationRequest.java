package com.translator.domain;

import java.io.Serializable;
import java.util.Objects;

public final class TranslationRequest implements Serializable {
    private final String text;
    private final String sourceLanguageCode;
    private final String targetLanguageCode;

    public TranslationRequest(String text, String sourceLanguageCode, String targetLanguageCode) {
        this.text = Objects.requireNonNull(text, "text");
        this.sourceLanguageCode = Objects.requireNonNull(sourceLanguageCode, "sourceLanguageCode");
        this.targetLanguageCode = Objects.requireNonNull(targetLanguageCode, "targetLanguageCode");
    }

    public String text() {
        return text;
    }

    public String sourceLanguageCode() {
        return sourceLanguageCode;
    }

    public String targetLanguageCode() {
        return targetLanguageCode;
    }
}
