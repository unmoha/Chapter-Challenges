package com.translator.domain;

import java.io.Serializable;
import java.util.Objects;

public final class TranslationResult implements Serializable {
    private final String translatedText;
    private final String engineId;

    public TranslationResult(String translatedText, String engineId) {
        this.translatedText = Objects.requireNonNull(translatedText, "translatedText");
        this.engineId = Objects.requireNonNull(engineId, "engineId");
    }

    public String translatedText() {
        return translatedText;
    }

    public String engineId() {
        return engineId;
    }
}
