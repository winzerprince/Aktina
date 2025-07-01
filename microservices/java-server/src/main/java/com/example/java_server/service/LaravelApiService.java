package com.example.java_server.service;

import com.example.java_server.dto.ApplicationProcessingResponse;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.stereotype.Service;
import org.springframework.web.reactive.function.client.WebClient;
import reactor.core.publisher.Mono;

import java.time.Duration;
import java.util.HashMap;
import java.util.Map;

/**
 * Service for communicating with Laravel API
 */
@Service
public class LaravelApiService {

    private static final Logger logger = LoggerFactory.getLogger(LaravelApiService.class);

    private final WebClient webClient;

    @Value("${laravel.api.base-url}")
    private String laravelBaseUrl;

    @Value("${laravel.api.token}")
    private String apiToken;

    public LaravelApiService(WebClient.Builder webClientBuilder) {
        this.webClient = webClientBuilder
            .codecs(configurer -> configurer.defaultCodecs().maxInMemorySize(10 * 1024 * 1024)) // 10MB
            .build();
    }

    /**
     * Send processing results back to Laravel
     */
    public Mono<Boolean> sendProcessingResults(ApplicationProcessingResponse response) {
        logger.info("Sending processing results for application {} to Laravel",
                   response.getApplicationReference());

        try {
            Map<String, Object> payload = createPayload(response);

            return webClient.post()
                .uri(laravelBaseUrl + "/api/applications/processing-complete")
                .header(HttpHeaders.AUTHORIZATION, "Bearer " + apiToken)
                .header(HttpHeaders.CONTENT_TYPE, MediaType.APPLICATION_JSON_VALUE)
                .bodyValue(payload)
                .retrieve()
                .toBodilessEntity()
                .timeout(Duration.ofSeconds(30))
                .map(responseEntity -> {
                    logger.info("Successfully sent results for application {} to Laravel",
                               response.getApplicationReference());
                    return true;
                })
                .onErrorResume(error -> {
                    logger.error("Failed to send results for application {} to Laravel: {}",
                               response.getApplicationReference(), error.getMessage(), error);
                    return Mono.just(false);
                });

        } catch (Exception e) {
            logger.error("Error preparing payload for application {}: {}",
                        response.getApplicationReference(), e.getMessage(), e);
            return Mono.just(false);
        }
    }

    /**
     * Create payload for Laravel API
     */
    private Map<String, Object> createPayload(ApplicationProcessingResponse response) {
        Map<String, Object> payload = new HashMap<>();

        payload.put("application_id", response.getApplicationId());
        payload.put("application_reference", response.getApplicationReference());
        payload.put("success", response.isSuccess());

        if (response.isSuccess()) {
            payload.put("score", response.getScore());
            payload.put("status", response.getStatus());
            payload.put("processing_notes", response.getProcessingNotes());
            payload.put("processed_at", response.getProcessedAt().toString());

            if (response.getScoringDetails() != null) {
                payload.put("scoring_details", response.getScoringDetails());
            }
        } else {
            payload.put("error_message", response.getErrorMessage());
        }

        return payload;
    }

    /**
     * Test connection to Laravel API
     */
    public Mono<Boolean> testConnection() {
        logger.info("Testing connection to Laravel API at {}", laravelBaseUrl);

        return webClient.get()
            .uri(laravelBaseUrl + "/api/health")
            .header(HttpHeaders.AUTHORIZATION, "Bearer " + apiToken)
            .retrieve()
            .toBodilessEntity()
            .timeout(Duration.ofSeconds(10))
            .map(responseEntity -> {
                logger.info("Successfully connected to Laravel API");
                return true;
            })
            .onErrorResume(error -> {
                logger.warn("Failed to connect to Laravel API: {}", error.getMessage());
                return Mono.just(false);
            });
    }
}
