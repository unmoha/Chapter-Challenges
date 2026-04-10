package com.translator.shared.rmi;

import java.io.Serializable;

public record DetectedLanguage(String language, float confidence) implements Serializable {
    public static final long serialVersionUID = 1L;
}
