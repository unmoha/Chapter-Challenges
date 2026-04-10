package com.translator.shared.rmi;

/**
 * Represents a language with its code and display name.
 * Includes a special AUTO constant for automatic language detection.
 */
public record Language(String code, String name) implements java.io.Serializable {
    public static final Language AUTO = new Language("auto", "Detect Language");
    
    @Override
    public String toString() {
        return name;
    }
}
