package com.translator.engine.rmi;

import com.translator.domain.Language;
import com.translator.domain.TranslationRequest;
import com.translator.domain.TranslationResult;

import java.rmi.Remote;
import java.rmi.RemoteException;
import java.util.List;

public interface RmiTranslationRemote extends Remote {
    TranslationResult translate(TranslationRequest request) throws RemoteException;
    List<Language> supportedLanguages() throws RemoteException;
    String engineId() throws RemoteException;
    String displayName() throws RemoteException;
}
