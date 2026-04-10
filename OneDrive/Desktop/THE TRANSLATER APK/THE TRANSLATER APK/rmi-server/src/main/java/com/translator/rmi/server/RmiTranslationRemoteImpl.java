package com.translator.rmi.server;

import com.translator.domain.Language;
import com.translator.domain.TranslationRequest;
import com.translator.domain.TranslationResult;
import com.translator.engine.libretranslate.LibreTranslateConfig;
import com.translator.engine.libretranslate.LibreTranslateEngine;
import com.translator.engine.rmi.RmiTranslationRemote;

import java.io.IOException;
import java.net.URI;
import java.rmi.RemoteException;
import java.rmi.server.UnicastRemoteObject;
import java.util.List;
import java.util.Objects;

public final class RmiTranslationRemoteImpl extends UnicastRemoteObject implements RmiTranslationRemote {
    private final LibreTranslateEngine engine;

    public RmiTranslationRemoteImpl(String baseUrl) throws RemoteException {
        super();
        Objects.requireNonNull(baseUrl, "baseUrl");
        this.engine = new LibreTranslateEngine(new LibreTranslateConfig(URI.create(baseUrl)));
    }

    @Override
    public TranslationResult translate(TranslationRequest request) throws RemoteException {
        try {
            return engine.translate(request);
        } catch (IOException | InterruptedException e) {
            throw new RemoteException(e.getMessage(), e);
        }
    }

    @Override
    public List<Language> supportedLanguages() throws RemoteException {
        return engine.supportedLanguages();
    }

    @Override
    public String engineId() throws RemoteException {
        return engine.id();
    }

    @Override
    public String displayName() throws RemoteException {
        return engine.displayName();
    }
}
