package com.translator.application;

import com.translator.domain.TranslationEngine;

import java.util.Collection;
import java.util.LinkedHashMap;
import java.util.Map;
import java.util.Objects;

public final class EngineRegistry {
    private final Map<String, TranslationEngine> enginesById = new LinkedHashMap<>();

    public EngineRegistry register(TranslationEngine engine) {
        Objects.requireNonNull(engine, "engine");
        enginesById.put(engine.id(), engine);
        return this;
    }

    public TranslationEngine getRequired(String engineId) {
        TranslationEngine engine = enginesById.get(engineId);
        if (engine == null) {
            throw new IllegalArgumentException("Unknown engineId: " + engineId);
        }
        return engine;
    }

    public Collection<TranslationEngine> all() {
        return enginesById.values();
    }
}
