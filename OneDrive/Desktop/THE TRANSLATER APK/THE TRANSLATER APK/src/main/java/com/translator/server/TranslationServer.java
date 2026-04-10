package com.translator.server;

import com.translator.server.service.TranslationServiceImpl;
import com.translator.shared.rmi.TranslationService;
import java.rmi.AlreadyBoundException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.rmi.RemoteException;
import java.rmi.server.ExportException;
import java.rmi.server.UnicastRemoteObject;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

public class TranslationServer {
    private static final Logger logger = LoggerFactory.getLogger(TranslationServer.class);
    private static final int RMI_PORT = 1099;
    private static Registry registry;
    
    public static void main(String[] args) {
        try {
            // Create service implementation
            TranslationService service = new TranslationServiceImpl();
            
            // Export the service
            TranslationService stub;
            try {
                stub = (TranslationService) UnicastRemoteObject.exportObject(service, 0);
            } catch (ExportException e) {
                logger.warn("Service already exported, reusing existing service");
                stub = service; // Try to use the existing service
            }
            
            // Get or create registry
            try {
                registry = LocateRegistry.createRegistry(RMI_PORT);
                logger.info("Created new RMI registry on port {}", RMI_PORT);
            } catch (ExportException e) {
                logger.info("RMI registry already exists, reusing existing registry");
                registry = LocateRegistry.getRegistry(RMI_PORT);
            }
            
            // Bind the service
            try {
                registry.rebind("TranslationService", stub);
                logger.info("Successfully bound TranslationService to RMI registry");
            } catch (Exception e) {
                logger.error("Failed to bind service", e);
                throw e;
            }
            
            logger.info("Translation Server is running on port {}", RMI_PORT);
            System.out.println("Server ready. Press Enter to stop the server...");
            System.in.read();
            
            // Cleanup on exit
            try {
                registry.unbind("TranslationService");
                UnicastRemoteObject.unexportObject(service, true);
                logger.info("Server stopped successfully");
            } catch (Exception e) {
                logger.warn("Error during server shutdown", e);
            }
            
        } catch (Exception e) {
            logger.error("Server error: {}", e.getMessage());
            System.err.println("Server error: " + e.getMessage());
            e.printStackTrace();
            System.exit(1);
        }
    }
}
