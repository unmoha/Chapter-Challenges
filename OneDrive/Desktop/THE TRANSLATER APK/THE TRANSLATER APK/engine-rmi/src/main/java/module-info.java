module translator.engine.rmi {
    requires transitive translator.domain;
    requires java.rmi;
    
    exports com.translator.engine.rmi;
}
