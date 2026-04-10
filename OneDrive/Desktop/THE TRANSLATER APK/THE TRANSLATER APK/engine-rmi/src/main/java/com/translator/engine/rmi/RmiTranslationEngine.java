package com.translator.engine.rmi;

import com.translator.domain.Language;
import com.translator.domain.TranslationEngine;
import com.translator.domain.TranslationRequest;
import com.translator.domain.TranslationResult;

import java.io.IOException;
import java.net.MalformedURLException;
import java.rmi.Naming;
import java.rmi.NotBoundException;
import java.rmi.RemoteException;
import java.util.List;
import java.util.Objects;

public final class RmiTranslationEngine implements TranslationEngine {
    private final String host;
    private final int port;
    private final String serviceName;

    private volatile RmiTranslationRemote remote;

    public RmiTranslationEngine(String host) {
        this(host, 1099, "TranslatorService");
    }

    public RmiTranslationEngine(String host, int port, String serviceName) {
        this.host = Objects.requireNonNull(host, "host");
        this.port = port;
        this.serviceName = Objects.requireNonNull(serviceName, "serviceName");
    }

    private RmiTranslationRemote remote() throws IOException {
        if (remote != null) return remote;
        synchronized (this) {
            if (remote != null) return remote;
            try {
                String url = String.format("rmi://%s:%d/%s", host, port, serviceName);
                remote = (RmiTranslationRemote) Naming.lookup(url);
                return remote;
            } catch (NotBoundException | MalformedURLException | RemoteException e) {
                throw new IOException("RMI connect failed: " + e.getMessage(), e);
            }
        }
    }

    @Override
    public String id() {
        try {
            return remote() == null ? "rmi" : remote().engineId();
        } catch (IOException e) {
            return "rmi";
        }
    }

    @Override
    public String displayName() {
        try {
            return remote() == null ? "RMI Remote" : remote().displayName();
        } catch (IOException e) {
            return "RMI Remote";
        }
    }

    @Override
    public TranslationResult translate(TranslationRequest request) throws IOException, InterruptedException {
        try {
            return remote().translate(request);
        } catch (RemoteException e) {
            throw new IOException(e.getMessage(), e);
        }
    }

    @Override
    public List<Language> supportedLanguages() {
        try {
            return remote().supportedLanguages();
        } catch (IOException e) {
            return List.of(new Language("en", "English"), new Language("ar", "Arabic"));
        }
    }
}
