package com.example.java_server.service;

import com.example.java_server.dto.ApplicationProcessingRequest;
import com.example.java_server.dto.ApplicationProcessingResponse;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Service;

import java.util.concurrent.CompletableFuture;

/**
 * Main service for orchestrating application processing
 */
@Service
public class ApplicationProcessingService {

    private static final Logger logger = LoggerFactory.getLogger(ApplicationProcessingService.class);

    private final PdfProcessingService pdfProcessingService;
    private final LaravelApiService laravelApiService;

    public ApplicationProcessingService(PdfProcessingService pdfProcessingService,
                                      LaravelApiService laravelApiService) {
        this.pdfProcessingService = pdfProcessingService;
        this.laravelApiService = laravelApiService;
    }

    /**
     * Process application synchronously
     */
    public ApplicationProcessingResponse processApplication(ApplicationProcessingRequest request) {
        logger.info("Starting synchronous processing for application: {}",
                   request.getApplicationReference());

        return pdfProcessingService.processApplication(request);
    }

    /**
     * Process application asynchronously and send results to Laravel
     */
    @Async
    public CompletableFuture<Boolean> processApplicationAsync(ApplicationProcessingRequest request) {
        logger.info("Starting asynchronous processing for application: {}",
                   request.getApplicationReference());

        try {
            // Process the PDF
            ApplicationProcessingResponse response = pdfProcessingService.processApplication(request);

            // Send results back to Laravel
            Boolean success = laravelApiService.sendProcessingResults(response).block();

            if (Boolean.TRUE.equals(success)) {
                logger.info("Successfully completed async processing for application: {}",
                           request.getApplicationReference());
            } else {
                logger.error("Failed to send results to Laravel for application: {}",
                           request.getApplicationReference());
            }

            return CompletableFuture.completedFuture(Boolean.TRUE.equals(success));

        } catch (Exception e) {
            logger.error("Error in async processing for application {}: {}",
                        request.getApplicationReference(), e.getMessage(), e);

            // Send error response to Laravel
            ApplicationProcessingResponse errorResponse = ApplicationProcessingResponse.error(
                request.getApplicationId(),
                request.getApplicationReference(),
                "Async processing failed: " + e.getMessage()
            );

            laravelApiService.sendProcessingResults(errorResponse).block();

            return CompletableFuture.completedFuture(false);
        }
    }

    /**
     * Process multiple applications in batch
     */
    @Async
    public CompletableFuture<Integer> processBatch(ApplicationProcessingRequest[] requests) {
        logger.info("Starting batch processing for {} applications", requests.length);

        int successCount = 0;

        for (ApplicationProcessingRequest request : requests) {
            try {
                Boolean success = processApplicationAsync(request).get();
                if (Boolean.TRUE.equals(success)) {
                    successCount++;
                }
            } catch (Exception e) {
                logger.error("Error processing application {} in batch: {}",
                           request.getApplicationReference(), e.getMessage(), e);
            }
        }

        logger.info("Batch processing completed. Success: {}/{}", successCount, requests.length);
        return CompletableFuture.completedFuture(successCount);
    }
}
