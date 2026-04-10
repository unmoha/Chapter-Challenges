package com.translator.server.service;

import com.translator.server.ai.LibreTranslateClient;
import com.translator.shared.rmi.TranslationService;
import com.translator.shared.rmi.Language;
import com.translator.shared.rmi.DetectedLanguage;
import java.rmi.RemoteException;
import java.rmi.server.UnicastRemoteObject;
import java.util.List;

public class TranslationServiceImpl extends UnicastRemoteObject implements TranslationService {
    private final LibreTranslateClient translateClient;
    
    public TranslationServiceImpl() throws RemoteException {
        super();
        this.translateClient = new LibreTranslateClient("http://localhost:5000");
    }
    
    @Override
    public String translateText(String text, String sourceLang, String targetLang) throws RemoteException {
        try {
            return translateClient.translate(text, sourceLang, targetLang);
        } catch (Exception e) {
            throw new RemoteException("Translation failed: " + e.getMessage(), e);
        }
    }
    
    @Override
    public DetectedLanguage detectLanguage(String text) throws RemoteException {
        try {
            com.translator.server.ai.LibreTranslateClient.DetectedLanguage detected = 
                translateClient.detectLanguage(text);
            return toRmiDetectedLanguage(detected);
        } catch (Exception e) {
            throw new RemoteException("Language detection failed", e);
        }
    }
    
    private DetectedLanguage toRmiDetectedLanguage(
            com.translator.server.ai.LibreTranslateClient.DetectedLanguage libretranslateDetected) {
        return new DetectedLanguage(
            libretranslateDetected.language(),
            libretranslateDetected.confidence()
        );
    }
    
    @Override
    public byte[] translateSpeech(byte[] audioData, String sourceLang, String targetLang) throws RemoteException {
        throw new UnsupportedOperationException("Speech translation not implemented yet");
    }
    
    @Override
    public String translateImage(byte[] imageData, String sourceLang, String targetLang) throws RemoteException {
        throw new UnsupportedOperationException("Image translation not implemented yet");
    }
    
    @Override
    public List<Language> getSupportedLanguages() throws RemoteException {
        try {
            return translateClient.getSupportedLanguages();
        } catch (Exception e) {
            throw new RemoteException("Failed to get supported languages", e);
        }
    }
}
