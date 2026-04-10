package com.translator.ui.fx;

import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.StandardOpenOption;
import java.time.Instant;
import java.util.Objects;

final class TranslatorHistoryStore {
    private final Path file;

    TranslatorHistoryStore(Path file) {
        this.file = Objects.requireNonNull(file, "file");
    }

    void append(String engineId, String sourceLang, String targetLang, String input, String output) throws IOException {
        Objects.requireNonNull(engineId, "engineId");
        Objects.requireNonNull(sourceLang, "sourceLang");
        Objects.requireNonNull(targetLang, "targetLang");
        Objects.requireNonNull(input, "input");
        Objects.requireNonNull(output, "output");

        Path parent = file.getParent();
        if (parent != null) {
            Files.createDirectories(parent);
        }

        String line = Instant.now().toString()
                + "\t" + escape(engineId)
                + "\t" + escape(sourceLang)
                + "\t" + escape(targetLang)
                + "\t" + escape(input)
                + "\t" + escape(output)
                + System.lineSeparator();

        Files.writeString(file, line, StandardCharsets.UTF_8,
                StandardOpenOption.CREATE,
                StandardOpenOption.WRITE,
                StandardOpenOption.APPEND);
    }

    String readAll() throws IOException {
        if (!Files.exists(file)) {
            return "";
        }
        return Files.readString(file, StandardCharsets.UTF_8);
    }

    private static String escape(String s) {
        return s
                .replace("\\r", "\\\\r")
                .replace("\\n", "\\\\n")
                .replace("\t", "\\\\t");
    }
}
