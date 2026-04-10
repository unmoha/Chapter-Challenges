package com.translator.rmi.server;

import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;

public final class RmiServerMain {
    public static void main(String[] args) throws Exception {
        String ltUrl = System.getProperty("lt.url", "http://127.0.0.1:5000");
        int rmiPort = Integer.getInteger("rmi.port", 1099);
        String name = System.getProperty("rmi.name", "TranslatorService");

        System.out.println("Starting RMI Registry on port " + rmiPort + "...");
        Registry reg = LocateRegistry.createRegistry(rmiPort);

        System.out.println("Binding service '" + name + "' to LibreTranslate at " + ltUrl + "...");
        reg.rebind(name, new RmiTranslationRemoteImpl(ltUrl));

        System.out.println("RMI Server ready. Clients connect with: -Drmi.host=<server-ip> (port " + rmiPort + ")");
        // keep process alive
        Thread.currentThread().join();
    }
}
