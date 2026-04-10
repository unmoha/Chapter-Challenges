package com.translator.config;

import java.util.prefs.Preferences;

/**
 * Manages application configuration and preferences.
 */
public class AppConfig {
    private static final String NODE_NAME = "com.translator.app";
    private static final String KEY_API_URL = "api.url";
    private static final String KEY_API_KEY = "api.key";
    private static final String DEFAULT_API_URL = "https://libretranslate.de";
    
    private final Preferences prefs;
    
    public AppConfig() {
        this.prefs = Preferences.userRoot().node(NODE_NAME);
    }
    
    public String getApiUrl() {
        return prefs.get(KEY_API_URL, DEFAULT_API_URL);
    }
    
    public void setApiUrl(String url) {
        prefs.put(KEY_API_URL, url);
    }
    
    public String getApiKey() {
        return prefs.get(KEY_API_KEY, "");
    }
    
    public void setApiKey(String key) {
        prefs.put(KEY_API_KEY, key);
    }
}
