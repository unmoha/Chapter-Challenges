package com.translator.domain;

import java.io.Serializable;
import java.util.Objects;

public final class Language implements Serializable {
    private final String code;
    private final String displayName;

    public Language(String code, String displayName) {
        this.code = Objects.requireNonNull(code, "code");
        this.displayName = Objects.requireNonNull(displayName, "displayName");
    }

    public String code() {
        return code;
    }

    public String displayName() {
        return displayName;
    }

    @Override
    public String toString() {
        return displayName + " (" + code + ")";
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof Language that)) return false;
        return code.equals(that.code);
    }

    @Override
    public int hashCode() {
        return code.hashCode();
    }
}
