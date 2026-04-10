package com.translator.application;

import com.translator.domain.TranslationEngine;
import com.translator.domain.TranslationRequest;
import com.translator.domain.TranslationResult;

import java.io.IOException;
import java.util.Objects;

public final class TranslationService {
    private volatile TranslationEngine engine;

    public TranslationService(TranslationEngine engine) {
        this.engine = Objects.requireNonNull(engine, "engine");
    }

    public TranslationEngine engine() {
        return engine;
    }

    public void setEngine(TranslationEngine engine) {
        this.engine = Objects.requireNonNull(engine, "engine");
    }

    public TranslationResult translate(TranslationRequest request) throws IOException, InterruptedException {
        return engine.translate(request);
    }
}
