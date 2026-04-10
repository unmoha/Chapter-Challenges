package com.translator.libretranslate.client;

import com.translator.libretranslate.client.dto.LanguageDTO;
import com.translator.libretranslate.client.dto.TranslationRequestDTO;
import com.translator.libretranslate.client.dto.TranslationResponseDTO;
import retrofit2.Call;
import retrofit2.http.*;

import java.util.List;

/**
 * Retrofit interface for the LibreTranslate API.
 */
public interface LibreTranslateAPI {
    
    @POST("/translate")
    Call<TranslationResponseDTO> translate(
        @Body TranslationRequestDTO request
    );
    
    @GET("/languages")
    Call<List<LanguageDTO>> getLanguages();
    
    @GET("/detect")
    Call<List<DetectResponse>> detectLanguage(
        @Query("q") String text,
        @Query("api_key") String apiKey
    );
    
    class DetectResponse {
        public String language;
        public double confidence;
        public boolean isReliable;
    }
}
