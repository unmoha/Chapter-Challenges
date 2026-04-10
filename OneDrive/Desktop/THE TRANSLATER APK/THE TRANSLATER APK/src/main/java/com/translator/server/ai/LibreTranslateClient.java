package com.translator.server.ai;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;
import com.translator.shared.rmi.Language;
import okhttp3.*;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Objects;
import java.util.concurrent.TimeUnit;

public class LibreTranslateClient {
    private static final MediaType JSON = MediaType.get("application/json; charset=utf-8");
    private final OkHttpClient httpClient;
    private final String baseUrl;
    private final Gson gson = new Gson();

    public LibreTranslateClient(String baseUrl) {
        this.baseUrl = baseUrl.endsWith("/") ? baseUrl : baseUrl + "/";
        this.httpClient = new OkHttpClient.Builder()
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .build();
    }

    public String translate(String text, String sourceLang, String targetLang) throws IOException {
        String json = String.format("{\"q\":\"%s\",\"source\":\"%s\",\"target\":\"%s\"}",
            text.replace("\"", "\\\""), sourceLang, targetLang);
            
        RequestBody body = RequestBody.create(json, JSON);
        Request request = new Request.Builder()
            .url(baseUrl + "translate")
            .post(body)
            .build();

        try (Response response = httpClient.newCall(request).execute()) {
            if (!response.isSuccessful()) {
                throw new IOException("Unexpected code " + response);
            }
            
            String responseBody = Objects.requireNonNull(response.body()).string();
            JsonObject jsonResponse = JsonParser.parseString(responseBody).getAsJsonObject();
            return jsonResponse.get("translatedText").getAsString();
        }
    }

    public DetectedLanguage detectLanguage(String text) throws IOException {
        String json = String.format("{\"q\":\"%s\"}", text.replace("\"", "\\\""));
        RequestBody body = RequestBody.create(json, JSON);
        
        Request request = new Request.Builder()
            .url(baseUrl + "detect")
            .post(body)
            .build();

        try (Response response = httpClient.newCall(request).execute()) {
            if (!response.isSuccessful()) {
                throw new IOException("Unexpected code " + response);
            }
            
            String responseBody = Objects.requireNonNull(response.body()).string();
            JsonArray detections = JsonParser.parseString(responseBody).getAsJsonArray();
            JsonObject detection = detections.get(0).getAsJsonObject();
            
            return new DetectedLanguage(
                detection.get("language").getAsString(),
                detection.get("confidence").getAsFloat()
            );
        }
    }

    public List<Language> getSupportedLanguages() throws IOException {
        Request request = new Request.Builder()
            .url(baseUrl + "languages")
            .build();

        try (Response response = httpClient.newCall(request).execute()) {
            if (!response.isSuccessful()) {
                throw new IOException("Unexpected code " + response);
            }
            
            String responseBody = Objects.requireNonNull(response.body()).string();
            JsonArray languages = JsonParser.parseString(responseBody).getAsJsonArray();
            
            List<Language> result = new ArrayList<>();
            for (JsonElement element : languages) {
                JsonObject lang = element.getAsJsonObject();
                result.add(new Language(
                    lang.get("code").getAsString(),
                    lang.get("name").getAsString()
                ));
            }
            return result;
        }
    }

    public record DetectedLanguage(String language, float confidence) {}
}
