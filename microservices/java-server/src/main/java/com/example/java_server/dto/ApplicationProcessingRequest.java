package com.example.java_server.dto;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

/**
 * Request DTO for PDF application processing
 */
public class ApplicationProcessingRequest {

    @NotNull(message = "Application ID is required")
    private Long applicationId;

    @NotBlank(message = "PDF file path is required")
    private String pdfFilePath;

    @NotBlank(message = "Application reference is required")
    private String applicationReference;

    private String vendorEmail;
    private String vendorName;

    // Constructors
    public ApplicationProcessingRequest() {}

    public ApplicationProcessingRequest(Long applicationId, String pdfFilePath, String applicationReference) {
        this.applicationId = applicationId;
        this.pdfFilePath = pdfFilePath;
        this.applicationReference = applicationReference;
    }

    // Getters and Setters
    public Long getApplicationId() {
        return applicationId;
    }

    public void setApplicationId(Long applicationId) {
        this.applicationId = applicationId;
    }

    public String getPdfFilePath() {
        return pdfFilePath;
    }

    public void setPdfFilePath(String pdfFilePath) {
        this.pdfFilePath = pdfFilePath;
    }

    public String getApplicationReference() {
        return applicationReference;
    }

    public void setApplicationReference(String applicationReference) {
        this.applicationReference = applicationReference;
    }

    public String getVendorEmail() {
        return vendorEmail;
    }

    public void setVendorEmail(String vendorEmail) {
        this.vendorEmail = vendorEmail;
    }

    public String getVendorName() {
        return vendorName;
    }

    public void setVendorName(String vendorName) {
        this.vendorName = vendorName;
    }

    @Override
    public String toString() {
        return "ApplicationProcessingRequest{" +
                "applicationId=" + applicationId +
                ", pdfFilePath='" + pdfFilePath + '\'' +
                ", applicationReference='" + applicationReference + '\'' +
                ", vendorEmail='" + vendorEmail + '\'' +
                ", vendorName='" + vendorName + '\'' +
                '}';
    }
}
