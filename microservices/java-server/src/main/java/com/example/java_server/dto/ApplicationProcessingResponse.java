package com.example.java_server.dto;

import java.time.LocalDateTime;
import java.util.Map;

/**
 * Response DTO for PDF application processing results
 */
public class ApplicationProcessingResponse {

    private Long applicationId;
    private String applicationReference;
    private int score;
    private String status;
    private String processingNotes;
    private LocalDateTime processedAt;
    private Map<String, Object> scoringDetails;
    private boolean success;
    private String errorMessage;

    // Constructors
    public ApplicationProcessingResponse() {}

    public ApplicationProcessingResponse(Long applicationId, String applicationReference,
                                       int score, String status, String processingNotes) {
        this.applicationId = applicationId;
        this.applicationReference = applicationReference;
        this.score = score;
        this.status = status;
        this.processingNotes = processingNotes;
        this.processedAt = LocalDateTime.now();
        this.success = true;
    }

    public static ApplicationProcessingResponse error(Long applicationId, String applicationReference, String errorMessage) {
        ApplicationProcessingResponse response = new ApplicationProcessingResponse();
        response.applicationId = applicationId;
        response.applicationReference = applicationReference;
        response.success = false;
        response.errorMessage = errorMessage;
        response.processedAt = LocalDateTime.now();
        return response;
    }

    // Getters and Setters
    public Long getApplicationId() {
        return applicationId;
    }

    public void setApplicationId(Long applicationId) {
        this.applicationId = applicationId;
    }

    public String getApplicationReference() {
        return applicationReference;
    }

    public void setApplicationReference(String applicationReference) {
        this.applicationReference = applicationReference;
    }

    public int getScore() {
        return score;
    }

    public void setScore(int score) {
        this.score = score;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getProcessingNotes() {
        return processingNotes;
    }

    public void setProcessingNotes(String processingNotes) {
        this.processingNotes = processingNotes;
    }

    public LocalDateTime getProcessedAt() {
        return processedAt;
    }

    public void setProcessedAt(LocalDateTime processedAt) {
        this.processedAt = processedAt;
    }

    public Map<String, Object> getScoringDetails() {
        return scoringDetails;
    }

    public void setScoringDetails(Map<String, Object> scoringDetails) {
        this.scoringDetails = scoringDetails;
    }

    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public String getErrorMessage() {
        return errorMessage;
    }

    public void setErrorMessage(String errorMessage) {
        this.errorMessage = errorMessage;
    }

    @Override
    public String toString() {
        return "ApplicationProcessingResponse{" +
                "applicationId=" + applicationId +
                ", applicationReference='" + applicationReference + '\'' +
                ", score=" + score +
                ", status='" + status + '\'' +
                ", processingNotes='" + processingNotes + '\'' +
                ", processedAt=" + processedAt +
                ", success=" + success +
                ", errorMessage='" + errorMessage + '\'' +
                '}';
    }
}
