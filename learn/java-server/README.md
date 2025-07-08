# Java Server Microservice in Aktina SCM ☕

The Java Spring Boot microservice handles vendor application processing and document validation.

## 📍 Location in Project
```
microservices/java-server/
├── src/main/java/com/example/java_server/
│   ├── controller/
│   │   └── ApplicationController.java
│   ├── dto/
│   │   └── ApplicationProcessingResponse.java
│   ├── service/
│   │   ├── ApplicationProcessingService.java
│   │   ├── LaravelApiService.java
│   │   └── PdfProcessingService.java
│   └── JavaServerApplication.java
├── pom.xml
├── start-service.sh
└── docs/
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: The Document Helper Robot**

Imagine a super smart robot that helps check important papers:

- **When someone wants to join our company** (vendor application), they send lots of papers
- **The robot reads all the papers** (PDF processing) and checks if everything is correct
- **It looks for important things** like signatures, money information, and company details
- **If the papers are good**, the robot tells the main system "This person can join!"
- **If something is wrong**, it explains what needs to be fixed

The robot is very fast and never gets tired of reading papers!

### 🎓 **CS Student Level: Microservice Architecture**

The Java microservice implements **Document Processing** and **Business Rule Validation**:

```java
@RestController
@RequestMapping("/api/applications")
public class ApplicationController {
    
    @Autowired
    private ApplicationProcessingService processingService;
    
    @PostMapping("/process")
    public ResponseEntity<ApplicationProcessingResponse> processApplication(
            @RequestParam("file") MultipartFile pdfFile,
            @RequestParam("applicationId") Long applicationId) {
        
        // Process PDF document
        ApplicationProcessingResponse response = 
            processingService.processApplication(pdfFile, applicationId);
            
        return ResponseEntity.ok(response);
    }
}

@Service
public class ApplicationProcessingService {
    
    public ApplicationProcessingResponse processApplication(
            MultipartFile pdfFile, Long applicationId) {
        
        // Extract data from PDF
        Map<String, Object> extractedData = pdfProcessingService.extractData(pdfFile);
        
        // Validate business rules
        ValidationResult validation = validateBusinessRules(extractedData);
        
        // Calculate score
        int score = calculateApplicationScore(extractedData, validation);
        
        // Send results back to Laravel
        laravelApiService.updateApplicationStatus(applicationId, score, validation);
        
        return new ApplicationProcessingResponse(score, validation.isValid());
    }
}
```

**Key Features:**
- **PDF Processing**: Extract text and data from vendor applications
- **Business Rule Validation**: Check financial requirements and compliance
- **Scoring Algorithm**: Calculate vendor suitability scores
- **API Integration**: Communicate with Laravel main application

### 👨‍🏫 **CS Professor Level: Domain-Driven Microservice Design**

The Java microservice implements **Bounded Context** for vendor processing:

```java
@Service
@Transactional
public class ApplicationProcessingService {
    
    private final PdfProcessingService pdfProcessor;
    private final BusinessRuleEngine ruleEngine;
    private final ScoringAlgorithm scoringAlgorithm;
    private final LaravelApiService laravelApi;
    
    public ApplicationProcessingResponse processApplication(
            MultipartFile pdfFile, Long applicationId) {
        
        // Domain model for application processing
        VendorApplication application = VendorApplication.fromPdf(
            pdfProcessor.extractStructuredData(pdfFile)
        );
        
        // Apply domain rules
        ValidationResult validation = ruleEngine.validate(application);
        
        // Calculate domain score
        ApplicationScore score = scoringAlgorithm.calculate(application, validation);
        
        // Publish domain event
        domainEventPublisher.publish(
            new ApplicationProcessed(applicationId, score, validation)
        );
        
        // Integration with bounded context (Laravel)
        laravelApi.updateApplicationStatus(
            applicationId, 
            score.getValue(), 
            validation.getStatus()
        );
        
        return ApplicationProcessingResponse.builder()
            .score(score.getValue())
            .isValid(validation.isValid())
            .validationErrors(validation.getErrors())
            .processingMetadata(application.getMetadata())
            .build();
    }
}

// Domain model
public class VendorApplication {
    private final CompanyInfo companyInfo;
    private final FinancialData financialData;
    private final ComplianceStatus complianceStatus;
    
    public static VendorApplication fromPdf(Map<String, Object> extractedData) {
        return new VendorApplication(
            CompanyInfo.fromData(extractedData),
            FinancialData.fromData(extractedData),
            ComplianceStatus.fromData(extractedData)
        );
    }
    
    public boolean meetsFinancialRequirements() {
        return financialData.getAnnualRevenue() >= MINIMUM_REVENUE_THRESHOLD
            && financialData.getCreditScore() >= MINIMUM_CREDIT_SCORE;
    }
}
```

## 🏗️ Architecture Patterns Used

### **1. Microservice Pattern**
Independent service with its own database and business logic:

```java
@SpringBootApplication
@EnableEurekaClient  // Service discovery
@EnableCircuitBreaker  // Resilience
public class JavaServerApplication {
    
    public static void main(String[] args) {
        SpringApplication.run(JavaServerApplication.class, args);
    }
    
    @Bean
    public RestTemplate restTemplate() {
        return new RestTemplate();
    }
}
```

### **2. Service Layer Pattern**
Clear separation of concerns:

```java
@Service
public class PdfProcessingService {
    
    public Map<String, Object> extractData(MultipartFile pdfFile) throws IOException {
        try (PDDocument document = PDDocument.load(pdfFile.getInputStream())) {
            PDFTextStripper stripper = new PDFTextStripper();
            String text = stripper.getText(document);
            
            return parseStructuredData(text);
        }
    }
    
    private Map<String, Object> parseStructuredData(String text) {
        // Extract company information
        String companyName = extractCompanyName(text);
        String revenue = extractRevenue(text);
        String employees = extractEmployeeCount(text);
        
        return Map.of(
            "companyName", companyName,
            "annualRevenue", parseRevenue(revenue),
            "employeeCount", parseEmployeeCount(employees)
        );
    }
}
```

### **3. DTO Pattern**
Data transfer between services:

```java
@Data
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class ApplicationProcessingResponse {
    private int score;
    private boolean isValid;
    private List<String> validationErrors;
    private Map<String, Object> extractedData;
    private String processingStatus;
    private LocalDateTime processedAt;
}
```

## 📋 Actual Implementation Examples

### **Application Controller**
```java
// File: src/main/java/com/example/java_server/controller/ApplicationController.java
@RestController
@RequestMapping("/api/applications")
@Slf4j
public class ApplicationController {
    
    @Autowired
    private ApplicationProcessingService processingService;
    
    @PostMapping("/process")
    public ResponseEntity<ApplicationProcessingResponse> processApplication(
            @RequestParam("file") MultipartFile pdfFile,
            @RequestParam("applicationId") Long applicationId) {
        
        try {
            log.info("Processing application {} with file {}", applicationId, pdfFile.getOriginalFilename());
            
            ApplicationProcessingResponse response = 
                processingService.processApplication(pdfFile, applicationId);
                
            log.info("Application {} processed with score {}", applicationId, response.getScore());
            
            return ResponseEntity.ok(response);
            
        } catch (Exception e) {
            log.error("Error processing application {}: {}", applicationId, e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                .body(ApplicationProcessingResponse.builder()
                    .isValid(false)
                    .validationErrors(List.of("Processing failed: " + e.getMessage()))
                    .build());
        }
    }
    
    @GetMapping("/health")
    public ResponseEntity<Map<String, String>> health() {
        return ResponseEntity.ok(Map.of(
            "status", "healthy",
            "timestamp", LocalDateTime.now().toString(),
            "service", "java-application-processor"
        ));
    }
}
```

### **Laravel API Service**
```java
// File: src/main/java/com/example/java_server/service/LaravelApiService.java
@Service
@Slf4j
public class LaravelApiService {
    
    private final WebClient webClient;
    
    @Value("${laravel.api.base-url}")
    private String laravelApiBaseUrl;
    
    public LaravelApiService(WebClient.Builder webClientBuilder) {
        this.webClient = webClientBuilder
            .codecs(configurer -> configurer.defaultCodecs().maxInMemorySize(10 * 1024 * 1024))
            .build();
    }
    
    public void updateApplicationStatus(Long applicationId, int score, ValidationResult validation) {
        try {
            Map<String, Object> updateData = Map.of(
                "score", score,
                "status", validation.isValid() ? "scored" : "rejected",
                "validation_errors", validation.getErrors(),
                "processed_at", LocalDateTime.now().toString()
            );
            
            webClient.put()
                .uri(laravelApiBaseUrl + "/api/applications/{id}/update-status", applicationId)
                .header(HttpHeaders.CONTENT_TYPE, MediaType.APPLICATION_JSON_VALUE)
                .bodyValue(updateData)
                .retrieve()
                .bodyToMono(String.class)
                .doOnSuccess(response -> log.info("Successfully updated Laravel application {}", applicationId))
                .doOnError(error -> log.error("Failed to update Laravel application {}: {}", applicationId, error.getMessage()))
                .timeout(Duration.ofSeconds(30))
                .subscribe();
                
        } catch (Exception e) {
            log.error("Error updating application status for {}: {}", applicationId, e.getMessage());
        }
    }
}
```

## 🔗 Interconnections

### **With Laravel Main Application**
```java
// HTTP API calls to Laravel
@Service
public class LaravelIntegrationService {
    
    public void notifyApplicationProcessed(Long applicationId, ApplicationResult result) {
        HttpHeaders headers = new HttpHeaders();
        headers.setContentType(MediaType.APPLICATION_JSON);
        
        HttpEntity<ApplicationResult> request = new HttpEntity<>(result, headers);
        
        restTemplate.postForEntity(
            laravelApiUrl + "/api/applications/" + applicationId + "/processed",
            request,
            String.class
        );
    }
}
```

### **With File Storage**
```java
// Process uploaded files from Laravel
@Service
public class FileProcessingService {
    
    public ProcessingResult processUploadedFile(String filePath) {
        try {
            File file = new File(filePath);
            return pdfProcessor.extractAndValidate(file);
        } catch (IOException e) {
            throw new FileProcessingException("Failed to process file: " + filePath);
        }
    }
}
```

## 🎯 Best Practices Used

### **1. Dependency Injection**
```java
@Service
public class ApplicationProcessingService {
    
    private final PdfProcessingService pdfProcessor;
    private final BusinessRuleEngine ruleEngine;
    private final LaravelApiService laravelApi;
    
    // Constructor injection
    public ApplicationProcessingService(
            PdfProcessingService pdfProcessor,
            BusinessRuleEngine ruleEngine,
            LaravelApiService laravelApi) {
        this.pdfProcessor = pdfProcessor;
        this.ruleEngine = ruleEngine;
        this.laravelApi = laravelApi;
    }
}
```

### **2. Configuration Management**
```java
// application.yml
spring:
  application:
    name: java-application-processor
  servlet:
    multipart:
      max-file-size: 10MB
      max-request-size: 10MB

laravel:
  api:
    base-url: ${LARAVEL_API_URL:http://localhost:8000}

logging:
  level:
    com.example.java_server: DEBUG
```

### **3. Error Handling**
```java
@ControllerAdvice
public class GlobalExceptionHandler {
    
    @ExceptionHandler(FileProcessingException.class)
    public ResponseEntity<ErrorResponse> handleFileProcessingException(FileProcessingException e) {
        return ResponseEntity.badRequest()
            .body(ErrorResponse.builder()
                .error("FILE_PROCESSING_ERROR")
                .message(e.getMessage())
                .timestamp(LocalDateTime.now())
                .build());
    }
}
```

## 🔧 Common Patterns

### **1. Builder Pattern**
```java
@Builder
@Data
public class ValidationResult {
    private boolean isValid;
    private List<String> errors;
    private Map<String, Object> metadata;
    
    public static ValidationResult success() {
        return ValidationResult.builder()
            .isValid(true)
            .errors(Collections.emptyList())
            .build();
    }
    
    public static ValidationResult failure(List<String> errors) {
        return ValidationResult.builder()
            .isValid(false)
            .errors(errors)
            .build();
    }
}
```

### **2. Strategy Pattern**
```java
// Different scoring strategies for different vendor types
interface ScoringStrategy {
    int calculateScore(VendorApplication application);
}

@Component
public class ManufacturingScoringStrategy implements ScoringStrategy {
    public int calculateScore(VendorApplication application) {
        // Manufacturing-specific scoring logic
        return baseScore + productionCapacityBonus + qualityBonus;
    }
}

@Component
public class TechnologyScoringStrategy implements ScoringStrategy {
    public int calculateScore(VendorApplication application) {
        // Technology-specific scoring logic
        return baseScore + innovationBonus + certificationBonus;
    }
}
```

### **3. Template Method Pattern**
```java
public abstract class ApplicationProcessor {
    
    public final ApplicationProcessingResponse process(MultipartFile file, Long applicationId) {
        // Template method
        Map<String, Object> data = extractData(file);
        ValidationResult validation = validateData(data);
        int score = calculateScore(data, validation);
        
        return buildResponse(score, validation);
    }
    
    protected abstract Map<String, Object> extractData(MultipartFile file);
    protected abstract ValidationResult validateData(Map<String, Object> data);
    protected abstract int calculateScore(Map<String, Object> data, ValidationResult validation);
}
```

## 📊 Performance Considerations

### **1. Asynchronous Processing**
```java
@Service
public class AsyncApplicationProcessor {
    
    @Async
    @Retryable(value = {Exception.class}, maxAttempts = 3)
    public CompletableFuture<ApplicationProcessingResponse> processAsync(
            MultipartFile file, Long applicationId) {
        
        ApplicationProcessingResponse response = processingService.process(file, applicationId);
        return CompletableFuture.completedFuture(response);
    }
}
```

### **2. Connection Pooling**
```java
@Configuration
public class WebClientConfig {
    
    @Bean
    public WebClient webClient() {
        ConnectionProvider provider = ConnectionProvider.builder("custom")
            .maxConnections(50)
            .maxIdleTime(Duration.ofSeconds(20))
            .maxLifeTime(Duration.ofSeconds(60))
            .pendingAcquireTimeout(Duration.ofSeconds(60))
            .build();
            
        HttpClient httpClient = HttpClient.create(provider);
        
        return WebClient.builder()
            .clientConnector(new ReactorClientHttpConnector(httpClient))
            .build();
    }
}
```

### **3. Caching**
```java
@Service
@EnableCaching
public class BusinessRuleService {
    
    @Cacheable(value = "businessRules", key = "#ruleType")
    public List<BusinessRule> getRulesByType(String ruleType) {
        // Expensive database or external API call
        return businessRuleRepository.findByType(ruleType);
    }
}
```

The Java microservice provides specialized document processing capabilities while maintaining loose coupling with the main Laravel application through well-defined APIs and asynchronous communication patterns.
