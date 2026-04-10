package com.translator.engine.libretranslate;

import java.net.URI;
import java.util.Objects;

public final class LibreTranslateConfig {
    private final URI baseUri;

    public LibreTranslateConfig(URI baseUri) {
        this.baseUri = Objects.requireNonNull(baseUri, "baseUri");
    }

    public URI baseUri() {
        return baseUri;
    }

    public URI translateUri() {
        return baseUri.resolve("/translate");
    }
}
