package com.translator.shared.rmi;

import java.rmi.Remote;
import java.rmi.RemoteException;
import java.util.List;

public interface TranslationService extends Remote {
    // Text Translation
    String translateText(String text, String sourceLang, String targetLang) 
        throws RemoteException;
        
    DetectedLanguage detectLanguage(String text) 
        throws RemoteException;
        
    // Speech
    byte[] translateSpeech(byte[] audioData, String sourceLang, String targetLang) 
        throws RemoteException;
        
    // OCR
    String translateImage(byte[] imageData, String sourceLang, String targetLang) 
        throws RemoteException;
        
    // Language Support
    List<Language> getSupportedLanguages() 
        throws RemoteException;
}
