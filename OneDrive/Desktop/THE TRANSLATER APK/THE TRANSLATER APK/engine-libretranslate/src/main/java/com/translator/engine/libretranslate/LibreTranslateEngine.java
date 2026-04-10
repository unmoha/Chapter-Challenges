package com.translator.engine.libretranslate;

import com.translator.domain.Language;
import com.translator.domain.TranslationEngine;
import com.translator.domain.TranslationRequest;
import com.translator.domain.TranslationResult;

import java.io.IOException;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.nio.charset.StandardCharsets;
import java.time.Duration;
import java.util.ArrayList;
import java.util.List;
import java.util.Objects;

public final class LibreTranslateEngine implements TranslationEngine {
    private final LibreTranslateConfig config;
    private final HttpClient httpClient;

    public LibreTranslateEngine(LibreTranslateConfig config) {
        this(config, HttpClient.newBuilder()
                .connectTimeout(Duration.ofSeconds(10))
                .build());
    }

    public LibreTranslateEngine(LibreTranslateConfig config, HttpClient httpClient) {
        this.config = Objects.requireNonNull(config, "config");
        this.httpClient = Objects.requireNonNull(httpClient, "httpClient");
    }

    @Override
    public String id() {
        return "libretranslate";
    }

    @Override
    public String displayName() {
        return "LibreTranslate";
    }

    @Override
    public TranslationResult translate(TranslationRequest request) throws IOException, InterruptedException {
        Objects.requireNonNull(request, "request");

        String jsonBody = "{" +
                "\"q\":" + toJsonString(request.text()) + "," +
                "\"source\":" + toJsonString(request.sourceLanguageCode()) + "," +
                "\"target\":" + toJsonString(request.targetLanguageCode()) + "," +
                "\"format\":" + toJsonString("text") +
                "}";

        HttpRequest httpRequest = HttpRequest.newBuilder()
                .uri(config.translateUri())
                .timeout(Duration.ofSeconds(30))
                .header("Content-Type", "application/json")
                .POST(HttpRequest.BodyPublishers.ofString(jsonBody, StandardCharsets.UTF_8))
                .build();

        HttpResponse<String> response = httpClient.send(httpRequest, HttpResponse.BodyHandlers.ofString(StandardCharsets.UTF_8));

        if (response.statusCode() / 100 != 2) {
            throw new IOException("LibreTranslate HTTP " + response.statusCode() + ": " + response.body());
        }

        String translatedText = extractJsonStringValue(response.body(), "translatedText");
        return new TranslationResult(translatedText, id());
    }

    @Override
    public List<Language> supportedLanguages() {
        try {
            HttpRequest request = HttpRequest.newBuilder()
                    .uri(config.baseUri().resolve("/languages"))
                    .timeout(Duration.ofSeconds(5))
                    .GET()
                    .build();
            HttpResponse<String> response = httpClient.send(request, HttpResponse.BodyHandlers.ofString(StandardCharsets.UTF_8));
            if (response.statusCode() / 100 != 2) {
                throw new IOException("LibreTranslate languages HTTP " + response.statusCode() + ": " + response.body());
            }
            List<Language> languages = new ArrayList<>();
            languages.add(new Language("auto", "Auto"));
            String body = response.body();
            // Simple JSON array parser: [{"code":"en","name":"English"},...]
            int i = 0;
            while (i < body.length()) {
                int objStart = body.indexOf('{', i);
                if (objStart < 0) break;
                int objEnd = findMatchingBrace(body, objStart);
                if (objEnd < 0) break;
                String obj = body.substring(objStart, objEnd + 1);
                String code = extractJsonStringValue(obj, "code");
                String name = extractJsonStringValue(obj, "name");
                languages.add(new Language(code, name));
                i = objEnd + 1;
            }
            return languages;
        } catch (IOException | InterruptedException e) {
            // Fallback to minimal hardcoded list if server is down
            return List.of(
                    new Language("auto", "Auto"),
                    new Language("en", "English"),
                    new Language("ar", "Arabic"),
                    new Language("fr", "French"),
                    new Language("es", "Spanish"),
                    new Language("de", "German"),
                    new Language("it", "Italian"),
                    new Language("pt", "Portuguese"),
                    new Language("ru", "Russian"),
                    new Language("tr", "Turkish")
            );
        }
    }

    private static String toJsonString(String value) {
        String escaped = value
                .replace("\\", "\\\\")
                .replace("\"", "\\\"")
                .replace("\r", "\\r")
                .replace("\n", "\\n")
                .replace("\t", "\\t");
        return "\"" + escaped + "\"";
    }

    private static String extractJsonStringValue(String json, String key) throws IOException {
        String quotedKey = "\"" + key + "\"";
        int keyIndex = json.indexOf(quotedKey);
        if (keyIndex < 0) {
            throw new IOException("Missing key in response: " + key);
        }
        int colon = json.indexOf(':', keyIndex + quotedKey.length());
        if (colon < 0) {
            throw new IOException("Invalid JSON response (no colon after key): " + key);
        }
        int firstQuote = json.indexOf('"', colon + 1);
        if (firstQuote < 0) {
            throw new IOException("Invalid JSON response (no opening quote for value): " + key);
        }
        StringBuilder sb = new StringBuilder();
        boolean escaping = false;
        for (int i = firstQuote + 1; i < json.length(); i++) {
            char c = json.charAt(i);
            if (escaping) {
                switch (c) {
                    case '"' -> sb.append('"');
                    case '\\' -> sb.append('\\');
                    case 'n' -> sb.append('\n');
                    case 'r' -> sb.append('\r');
                    case 't' -> sb.append('\t');
                    default -> sb.append(c);
                }
                escaping = false;
                continue;
            }
            if (c == '\\') {
                escaping = true;
                continue;
            }
            if (c == '"') {
                return sb.toString();
            }
            sb.append(c);
        }
        throw new IOException("Invalid JSON response (unterminated string) for key: " + key);
    }

    private static int findMatchingBrace(String s, int openPos) {
        int depth = 0;
        for (int i = openPos; i < s.length(); i++) {
            char c = s.charAt(i);
            if (c == '{') depth++;
            else if (c == '}') {
                depth--;
                if (depth == 0) return i;
            }
        }
        return -1;
    }
}
