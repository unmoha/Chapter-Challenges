package com.translator.domain;

import java.io.IOException;
import java.util.List;

public interface TranslationEngine {
    String id();

    String displayName();

    TranslationResult translate(TranslationRequest request) throws IOException, InterruptedException;

    List<Language> supportedLanguages();
}
