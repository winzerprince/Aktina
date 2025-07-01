package com.example.java_server.controller;

import com.example.java_server.dto.ApplicationProcessingRequest;
import com.example.java_server.dto.ApplicationProcessingResponse;
import com.example.java_server.service.ApplicationProcessingService;
import com.example.java_server.service.LaravelApiService;
import jakarta.validation.Valid;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.Map;

/**
 * REST Controller for PDF processing endpoints
 */
@RestController
@RequestMapping("/api/v1")
@CrossOrigin(origins = "*")
public class PdfProcessingController {

    private static final Logger logger = LoggerFactory.getLogger(PdfProcessingController.class);

    private final ApplicationProcessingService applicationProcessingService;
    private final LaravelApiService laravelApiService;

    public PdfProcessingController(ApplicationProcessingService applicationProcessingService,
                                 LaravelApiService laravelApiService) {
        this.applicationProcessingService = applicationProcessingService;
        this.laravelApiService = laravelApiService;
    }

    /**
     * Health check endpoint
     */
    @GetMapping("/health")
    public ResponseEntity<Map<String, Object>> healthCheck() {
        Map<String, Object> response = new HashMap<>();
        response.put("status", "UP");
        response.put("service", "Aktina PDF Processing Service");
        response.put("timestamp", System.currentTimeMillis());

        return ResponseEntity.ok(response);
    }

    /**
     * Process application synchronously
     */
    @PostMapping("/process-application")
    public ResponseEntity<ApplicationProcessingResponse> processApplication(
            @Valid @RequestBody ApplicationProcessingRequest request) {

        logger.info("Received sync processing request for application: {}",
                   request.getApplicationReference());

        try {
            ApplicationProcessingResponse response = applicationProcessingService.processApplication(request);

            if (response.isSuccess()) {
                return ResponseEntity.ok(response);
            } else {
                return ResponseEntity.status(HttpStatus.UNPROCESSABLE_ENTITY).body(response);
            }

        } catch (Exception e) {
            logger.error("Error processing application {}: {}",
                        request.getApplicationReference(), e.getMessage(), e);

            ApplicationProcessingResponse errorResponse = ApplicationProcessingResponse.error(
                request.getApplicationId(),
                request.getApplicationReference(),
                "Processing failed: " + e.getMessage()
            );

            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(errorResponse);
        }
    }

    /**
     * Process application asynchronously
     */
    @PostMapping("/process-application-async")
    public ResponseEntity<Map<String, Object>> processApplicationAsync(
            @Valid @RequestBody ApplicationProcessingRequest request) {

        logger.info("Received async processing request for application: {}",
                   request.getApplicationReference());

        try {
            // Start async processing
            applicationProcessingService.processApplicationAsync(request);

            Map<String, Object> response = new HashMap<>();
            response.put("message", "Processing started");
            response.put("applicationId", request.getApplicationId());
            response.put("applicationReference", request.getApplicationReference());
            response.put("status", "PROCESSING");

            return ResponseEntity.accepted().body(response);

        } catch (Exception e) {
            logger.error("Error starting async processing for application {}: {}",
                        request.getApplicationReference(), e.getMessage(), e);

            Map<String, Object> errorResponse = new HashMap<>();
            errorResponse.put("error", "Failed to start processing");
            errorResponse.put("message", e.getMessage());

            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(errorResponse);
        }
    }

    /**
     * Process multiple applications in batch
     */
    @PostMapping("/process-batch")
    public ResponseEntity<Map<String, Object>> processBatch(
            @Valid @RequestBody ApplicationProcessingRequest[] requests) {

        logger.info("Received batch processing request for {} applications", requests.length);

        try {
            applicationProcessingService.processBatch(requests);

            Map<String, Object> response = new HashMap<>();
            response.put("message", "Batch processing started");
            response.put("batchSize", requests.length);
            response.put("status", "PROCESSING");

            return ResponseEntity.accepted().body(response);

        } catch (Exception e) {
            logger.error("Error starting batch processing: {}", e.getMessage(), e);

            Map<String, Object> errorResponse = new HashMap<>();
            errorResponse.put("error", "Failed to start batch processing");
            errorResponse.put("message", e.getMessage());

            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(errorResponse);
        }
    }

    /**
     * Test Laravel API connection
     */
    @GetMapping("/test-laravel-connection")
    public ResponseEntity<Map<String, Object>> testLaravelConnection() {
        logger.info("Testing Laravel API connection");

        try {
            Boolean connected = laravelApiService.testConnection().block();

            Map<String, Object> response = new HashMap<>();
            response.put("connected", Boolean.TRUE.equals(connected));
            response.put("message", Boolean.TRUE.equals(connected) ?
                        "Successfully connected to Laravel API" :
                        "Failed to connect to Laravel API");

            return ResponseEntity.ok(response);

        } catch (Exception e) {
            logger.error("Error testing Laravel connection: {}", e.getMessage(), e);

            Map<String, Object> errorResponse = new HashMap<>();
            errorResponse.put("connected", false);
            errorResponse.put("error", e.getMessage());

            return ResponseEntity.status(HttpStatus.SERVICE_UNAVAILABLE).body(errorResponse);
        }
    }

    /**
     * Get service information
     */
    @GetMapping("/info")
    public ResponseEntity<Map<String, Object>> getServiceInfo() {
        Map<String, Object> info = new HashMap<>();
        info.put("serviceName", "Aktina PDF Processing Service");
        info.put("version", "1.0.0");
        info.put("description", "Microservice for processing vendor application PDFs and calculating scores");
        info.put("capabilities", new String[]{
            "PDF text extraction",
            "Intelligent scoring based on multiple criteria",
            "Asynchronous processing",
            "Batch processing",
            "Laravel API integration"
        });

        return ResponseEntity.ok(info);
    }
}
