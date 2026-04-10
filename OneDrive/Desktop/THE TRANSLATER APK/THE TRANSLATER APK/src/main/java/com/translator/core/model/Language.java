package com.translator.core.model;

/**
 * Represents a language that can be used for translation.
 * 
 * @param code The ISO 639-1 language code (e.g., "en" for English, "es" for Spanish)
 * @param name The display name of the language (e.g., "English", "Español")
 */
public record Language(String code, String name) {
    public static final Language AUTO = new Language("auto", "Auto Detect");
    
    @Override
    public String toString() {
        return name;
    }
}
