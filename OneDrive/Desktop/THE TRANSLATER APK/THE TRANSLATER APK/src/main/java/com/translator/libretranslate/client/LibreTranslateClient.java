package com.translator.libretranslate.client;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.translator.core.model.Language;
import com.translator.core.model.TranslationRequest;
import com.translator.core.model.TranslationResult;
import com.translator.libretranslate.client.dto.LanguageDTO;
import com.translator.libretranslate.client.dto.TranslationRequestDTO;
import com.translator.libretranslate.client.dto.TranslationResponseDTO;
import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import retrofit2.Call;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.jackson.JacksonConverterFactory;

import java.io.IOException;
import java.util.List;
import java.util.Objects;
import java.util.concurrent.CompletableFuture;
import java.util.stream.Collectors;

/**
 * Client for interacting with the LibreTranslate API.
 */
public class LibreTranslateClient {
    private static final Logger log = LoggerFactory.getLogger(LibreTranslateClient.class);
    
    private final LibreTranslateAPI api;
    private final String apiKey;
    
    public LibreTranslateClient(String baseUrl) {
        this(baseUrl, null);
    }
    
    public LibreTranslateClient(String baseUrl, String apiKey) {
        this.apiKey = apiKey;
        
        // Set up logging
        HttpLoggingInterceptor logging = new HttpLoggingInterceptor();
        logging.setLevel(HttpLoggingInterceptor.Level.BASIC);
        
        // Create HTTP client with logging
        OkHttpClient.Builder httpClient = new OkHttpClient.Builder()
            .addInterceptor(logging);
        
        // Create Retrofit instance
        ObjectMapper objectMapper = new ObjectMapper();
        
        Retrofit retrofit = new Retrofit.Builder()
            .baseUrl(baseUrl.endsWith("/") ? baseUrl : baseUrl + "/")
            .addConverterFactory(JacksonConverterFactory.create(objectMapper))
            .client(httpClient.build())
            .build();
        
        this.api = retrofit.create(LibreTranslateAPI.class);
    }
    
    /**
     * Translates the given text from the source language to the target language.
     */
    public CompletableFuture<TranslationResult> translate(TranslationRequest request) {
        return CompletableFuture.supplyAsync(() -> {
            try {
                TranslationRequestDTO dto = new TranslationRequestDTO(
                    request.text(),
                    request.sourceLanguage().code(),
                    request.targetLanguage().code()
                );
                
                Call<TranslationResponseDTO> call = api.translate(dto);
                Response<TranslationResponseDTO> response = call.execute();
                
                if (!response.isSuccessful()) {
                    throw new RuntimeException("Translation failed: " + response.message());
                }
                
                TranslationResponseDTO responseBody = response.body();
                if (responseBody == null) {
                    throw new RuntimeException("Empty response from translation service");
                }
                
                // Get the detected language or use the provided one
                Language detectedLanguage = request.sourceLanguage();
                double confidence = 1.0;
                
                if (responseBody.detectedLanguage() != null) {
                    detectedLanguage = new Language(
                        responseBody.detectedLanguage().language(),
                        "" // Name will be set later when we have the full language list
                    );
                    confidence = responseBody.detectedLanguage().confidence();
                }
                
                return new TranslationResult(
                    responseBody.translatedText(),
                    detectedLanguage,
                    confidence
                );
                
            } catch (IOException e) {
                log.error("Error during translation", e);
                throw new RuntimeException("Failed to translate text: " + e.getMessage(), e);
            }
        });
    }
    
    /**
     * Gets the list of supported languages.
     */
    public CompletableFuture<List<Language>> getSupportedLanguages() {
        return CompletableFuture.supplyAsync(() -> {
            try {
                Call<List<LanguageDTO>> call = api.getLanguages();
                Response<List<LanguageDTO>> response = call.execute();
                
                if (!response.isSuccessful()) {
                    throw new RuntimeException("Failed to get supported languages: " + response.message());
                }
                
                List<LanguageDTO> dtos = response.body();
                if (dtos == null) {
                    throw new RuntimeException("Empty response when getting supported languages");
                }
                
                return dtos.stream()
                    .map(dto -> new Language(dto.code(), dto.name()))
                    .collect(Collectors.toList());
                
            } catch (IOException e) {
                log.error("Error getting supported languages", e);
                throw new RuntimeException("Failed to get supported languages: " + e.getMessage(), e);
            }
        });
    }
    
    /**
     * Detects the language of the given text.
     */
    public CompletableFuture<Language> detectLanguage(String text) {
        return CompletableFuture.supplyAsync(() -> {
            try {
                Call<List<LibreTranslateAPI.DetectResponse>> call = api.detectLanguage(text, apiKey);
                Response<List<LibreTranslateAPI.DetectResponse>> response = call.execute();
                
                if (!response.isSuccessful()) {
                    throw new RuntimeException("Language detection failed: " + response.message());
                }
                
                List<LibreTranslateAPI.DetectResponse> detections = response.body();
                if (detections == null || detections.isEmpty()) {
                    throw new RuntimeException("No language detected");
                }
                
                // Get the detection with highest confidence
                LibreTranslateAPI.DetectResponse bestMatch = detections.get(0);
                return new Language(bestMatch.language, ""); // Name will be set later
                
            } catch (IOException e) {
                log.error("Error detecting language", e);
                throw new RuntimeException("Failed to detect language: " + e.getMessage(), e);
            }
        });
    }
}
