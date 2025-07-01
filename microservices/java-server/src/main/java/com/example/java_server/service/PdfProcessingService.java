package com.example.java_server.service;

import com.example.java_server.dto.ApplicationProcessingRequest;
import com.example.java_server.dto.ApplicationProcessingResponse;
import org.apache.pdfbox.Loader;
import org.apache.pdfbox.pdmodel.PDDocument;
import org.apache.pdfbox.text.PDFTextStripper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import java.io.File;
import java.io.IOException;
import java.util.*;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * Service for processing PDF vendor applications and calculating scores
 */
@Service
public class PdfProcessingService {

    private static final Logger logger = LoggerFactory.getLogger(PdfProcessingService.class);

    @Value("${pdf.processing.max-pages:50}")
    private int maxPages;

    // Scoring criteria weights (total should be 100)
    private static final Map<String, Integer> SCORING_WEIGHTS = Map.of(
        "financial_strength", 25,
        "business_experience", 20,
        "company_size", 15,
        "certifications", 15,
        "contact_completeness", 10,
        "document_quality", 10,
        "industry_relevance", 5
    );

    // Financial strength keywords and patterns
    private static final Map<String, Integer> FINANCIAL_KEYWORDS = Map.of(
        "revenue", 5,
        "profit", 4,
        "bank balance", 8,
        "financial statement", 6,
        "audit", 5,
        "asset", 4,
        "cash flow", 6,
        "credit rating", 7,
        "investment", 3,
        "funding", 3
    );

    // Business experience keywords
    private static final Map<String, Integer> EXPERIENCE_KEYWORDS = Map.of(
        "years", 3,
        "experience", 5,
        "established", 4,
        "founded", 3,
        "partnership", 4,
        "client", 3,
        "customer", 3,
        "project", 2,
        "contract", 4,
        "supply chain", 6
    );

    // Company size indicators
    private static final Map<String, Integer> SIZE_KEYWORDS = Map.of(
        "employee", 3,
        "staff", 3,
        "team", 2,
        "office", 2,
        "branch", 3,
        "facility", 3,
        "warehouse", 4,
        "distribution", 4,
        "manufacturing", 5
    );

    // Certifications and quality standards
    private static final Map<String, Integer> CERTIFICATION_KEYWORDS = Map.of(
        "iso", 8,
        "certification", 6,
        "certified", 5,
        "quality", 4,
        "standard", 3,
        "compliance", 5,
        "accredited", 6,
        "licensed", 4,
        "registered", 3
    );

    // Industry relevance keywords
    private static final Map<String, Integer> INDUSTRY_KEYWORDS = Map.of(
        "electronics", 5,
        "technology", 4,
        "supplier", 6,
        "vendor", 5,
        "manufacturer", 5,
        "distributor", 4,
        "retail", 3,
        "wholesale", 4
    );

    /**
     * Process PDF application and calculate score
     */
    public ApplicationProcessingResponse processApplication(ApplicationProcessingRequest request) {
        logger.info("Processing application: {}", request.getApplicationReference());

        try {
            // Extract text from PDF
            String pdfText = extractTextFromPdf(request.getPdfFilePath());

            // Calculate scores for different criteria
            Map<String, Integer> scores = calculateDetailedScores(pdfText);

            // Calculate final weighted score
            int finalScore = calculateFinalScore(scores);

            // Generate processing notes
            String processingNotes = generateProcessingNotes(scores, pdfText);

            // Determine status based on score
            String status = determineStatus(finalScore);

            ApplicationProcessingResponse response = new ApplicationProcessingResponse(
                request.getApplicationId(),
                request.getApplicationReference(),
                finalScore,
                status,
                processingNotes
            );

            // Add detailed scoring breakdown
            Map<String, Object> scoringDetails = new HashMap<>();
            scoringDetails.put("detailedScores", scores);
            scoringDetails.put("textLength", pdfText.length());
            scoringDetails.put("pageCount", getPageCount(request.getPdfFilePath()));
            response.setScoringDetails(scoringDetails);

            logger.info("Application {} processed successfully with score: {}",
                       request.getApplicationReference(), finalScore);

            return response;

        } catch (Exception e) {
            logger.error("Error processing application {}: {}",
                        request.getApplicationReference(), e.getMessage(), e);
            return ApplicationProcessingResponse.error(
                request.getApplicationId(),
                request.getApplicationReference(),
                "Failed to process PDF: " + e.getMessage()
            );
        }
    }

    /**
     * Extract text content from PDF file
     */
    private String extractTextFromPdf(String filePath) throws IOException {
        File pdfFile = new File(filePath);
        if (!pdfFile.exists()) {
            throw new IOException("PDF file not found: " + filePath);
        }

        try (PDDocument document = Loader.loadPDF(pdfFile)) {
            PDFTextStripper textStripper = new PDFTextStripper();

            // Limit pages to prevent processing huge documents
            int pageCount = document.getNumberOfPages();
            if (pageCount > maxPages) {
                logger.warn("PDF has {} pages, limiting to {}", pageCount, maxPages);
                textStripper.setEndPage(maxPages);
            }

            return textStripper.getText(document).toLowerCase();
        }
    }

    /**
     * Calculate detailed scores for different criteria
     */
    private Map<String, Integer> calculateDetailedScores(String text) {
        Map<String, Integer> scores = new HashMap<>();

        // Financial strength score
        scores.put("financial_strength", calculateKeywordScore(text, FINANCIAL_KEYWORDS, 30));

        // Business experience score
        scores.put("business_experience", calculateExperienceScore(text));

        // Company size score
        scores.put("company_size", calculateCompanySizeScore(text));

        // Certifications score
        scores.put("certifications", calculateKeywordScore(text, CERTIFICATION_KEYWORDS, 25));

        // Contact completeness score
        scores.put("contact_completeness", calculateContactScore(text));

        // Document quality score
        scores.put("document_quality", calculateDocumentQualityScore(text));

        // Industry relevance score
        scores.put("industry_relevance", calculateKeywordScore(text, INDUSTRY_KEYWORDS, 20));

        return scores;
    }

    /**
     * Calculate score based on keyword occurrences
     */
    private int calculateKeywordScore(String text, Map<String, Integer> keywords, int maxScore) {
        int score = 0;

        for (Map.Entry<String, Integer> entry : keywords.entrySet()) {
            String keyword = entry.getKey();
            int weight = entry.getValue();

            // Count occurrences (case-insensitive)
            long count = countOccurrences(text, keyword);
            if (count > 0) {
                // Score increases with occurrences but with diminishing returns
                score += Math.min(weight * Math.log(count + 1), weight * 2);
            }
        }

        return Math.min(score, maxScore);
    }

    /**
     * Calculate experience score based on years mentioned
     */
    private int calculateExperienceScore(String text) {
        int baseScore = calculateKeywordScore(text, EXPERIENCE_KEYWORDS, 15);

        // Look for specific year mentions
        Pattern yearPattern = Pattern.compile("(\\d+)\\s*years?");
        Matcher matcher = yearPattern.matcher(text);

        int maxYears = 0;
        while (matcher.find()) {
            try {
                int years = Integer.parseInt(matcher.group(1));
                maxYears = Math.max(maxYears, years);
            } catch (NumberFormatException e) {
                // Ignore invalid numbers
            }
        }

        // Bonus points for years of experience
        int yearBonus = 0;
        if (maxYears >= 20) yearBonus = 10;
        else if (maxYears >= 10) yearBonus = 7;
        else if (maxYears >= 5) yearBonus = 4;
        else if (maxYears >= 2) yearBonus = 2;

        return Math.min(baseScore + yearBonus, 25);
    }

    /**
     * Calculate company size score based on employee numbers and facilities
     */
    private int calculateCompanySizeScore(String text) {
        int baseScore = calculateKeywordScore(text, SIZE_KEYWORDS, 10);

        // Look for employee numbers
        Pattern employeePattern = Pattern.compile("(\\d+)\\s*(?:employees?|staff|workers?)");
        Matcher matcher = employeePattern.matcher(text);

        int maxEmployees = 0;
        while (matcher.find()) {
            try {
                int employees = Integer.parseInt(matcher.group(1));
                maxEmployees = Math.max(maxEmployees, employees);
            } catch (NumberFormatException e) {
                // Ignore invalid numbers
            }
        }

        // Bonus points for company size
        int sizeBonus = 0;
        if (maxEmployees >= 1000) sizeBonus = 10;
        else if (maxEmployees >= 500) sizeBonus = 8;
        else if (maxEmployees >= 100) sizeBonus = 6;
        else if (maxEmployees >= 50) sizeBonus = 4;
        else if (maxEmployees >= 10) sizeBonus = 2;

        return Math.min(baseScore + sizeBonus, 20);
    }

    /**
     * Calculate contact completeness score
     */
    private int calculateContactScore(String text) {
        int score = 0;

        // Check for email addresses
        if (text.matches(".*\\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Z|a-z]{2,}\\b.*")) {
            score += 3;
        }

        // Check for phone numbers
        if (text.matches(".*\\b\\+?[1-9]\\d{1,14}\\b.*") || text.contains("phone") || text.contains("tel")) {
            score += 3;
        }

        // Check for address
        if (text.contains("address") || text.contains("street") || text.contains("city")) {
            score += 2;
        }

        // Check for website
        if (text.contains("www.") || text.contains("http") || text.contains(".com")) {
            score += 2;
        }

        return Math.min(score, 10);
    }

    /**
     * Calculate document quality score
     */
    private int calculateDocumentQualityScore(String text) {
        int score = 0;

        // Length indicates completeness
        if (text.length() > 5000) score += 3;
        else if (text.length() > 2000) score += 2;
        else if (text.length() > 1000) score += 1;

        // Structure indicators
        if (text.contains("table of contents") || text.contains("index")) score += 2;
        if (text.contains("executive summary") || text.contains("overview")) score += 2;
        if (text.contains("conclusion") || text.contains("summary")) score += 1;

        // Professional language indicators
        if (countOccurrences(text, "company") > 3) score += 1;
        if (countOccurrences(text, "business") > 2) score += 1;

        return Math.min(score, 10);
    }

    /**
     * Calculate final weighted score
     */
    private int calculateFinalScore(Map<String, Integer> scores) {
        double finalScore = 0.0;

        for (Map.Entry<String, Integer> entry : SCORING_WEIGHTS.entrySet()) {
            String criterion = entry.getKey();
            int weight = entry.getValue();
            int score = scores.getOrDefault(criterion, 0);

            finalScore += (score * weight) / 100.0;
        }

        return Math.min((int) Math.round(finalScore), 100);
    }

    /**
     * Determine application status based on score
     */
    private String determineStatus(int score) {
        if (score >= 75) return "scored"; // High score - ready for meeting
        else if (score >= 50) return "scored"; // Medium score - needs review
        else return "scored"; // Low score - still scored but unlikely to proceed
    }

    /**
     * Generate detailed processing notes
     */
    private String generateProcessingNotes(Map<String, Integer> scores, String text) {
        StringBuilder notes = new StringBuilder();
        notes.append("PDF Application Analysis Results:\n\n");

        for (Map.Entry<String, Integer> entry : scores.entrySet()) {
            String criterion = entry.getKey().replace("_", " ");
            int score = entry.getValue();
            notes.append(String.format("• %s: %d points\n",
                                     capitalize(criterion), score));
        }

        notes.append("\nDocument Analysis:\n");
        notes.append(String.format("• Text length: %d characters\n", text.length()));

        // Add recommendations
        notes.append("\nRecommendations:\n");
        if (scores.get("financial_strength") < 15) {
            notes.append("• Request additional financial documentation\n");
        }
        if (scores.get("business_experience") < 10) {
            notes.append("• Verify business experience and references\n");
        }
        if (scores.get("certifications") < 8) {
            notes.append("• Inquire about relevant certifications\n");
        }

        return notes.toString();
    }

    /**
     * Count occurrences of a substring in text
     */
    private long countOccurrences(String text, String substring) {
        return text.split(Pattern.quote(substring), -1).length - 1;
    }

    /**
     * Get page count of PDF
     */
    private int getPageCount(String filePath) {
        try (PDDocument document = Loader.loadPDF(new File(filePath))) {
            return document.getNumberOfPages();
        } catch (IOException e) {
            logger.warn("Could not get page count for {}: {}", filePath, e.getMessage());
            return 0;
        }
    }

    /**
     * Capitalize first letter of string
     */
    private String capitalize(String str) {
        if (str == null || str.isEmpty()) return str;
        return str.substring(0, 1).toUpperCase() + str.substring(1);
    }
}
