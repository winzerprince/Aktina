# Aktina PDF Processing Service - Java Microservice

## Overview

The Aktina PDF Processing Service is a Spring Boot microservice designed to analyze vendor application PDFs and generate intelligent scores based on multiple business criteria. This service integrates with the main Laravel application to provide automated vendor evaluation capabilities.

## Architecture

### Technology Stack
- **Spring Boot 3.5.3** - Main framework
- **Apache PDFBox 3.0.1** - PDF text extraction and analysis
- **Spring WebFlux** - Reactive HTTP client for Laravel communication
- **Spring Security** - Basic authentication
- **Maven** - Dependency management
- **Java 17** - Runtime environment

### Service Components

#### 1. PDF Processing Service (`PdfProcessingService`)
**Purpose**: Extracts text from PDF files and calculates scores based on business criteria.

**Scoring Algorithm**:
The service uses a weighted scoring system with the following criteria:

| Criterion | Weight | Description |
|-----------|--------|-------------|
| Financial Strength | 25% | Bank balance, revenue, financial statements |
| Business Experience | 20% | Years in business, partnerships, projects |
| Company Size | 15% | Employee count, facilities, offices |
| Certifications | 15% | ISO, quality standards, licenses |
| Contact Completeness | 10% | Email, phone, address, website |
| Document Quality | 10% | Structure, length, professionalism |
| Industry Relevance | 5% | Electronics, technology, supplier terms |

**Scoring Logic**:
- **Keyword Analysis**: Searches for relevant terms with weighted importance
- **Pattern Recognition**: Identifies specific patterns (years, employee counts, financial figures)
- **Frequency Scoring**: More mentions increase score with diminishing returns
- **Quality Assessment**: Evaluates document structure and completeness

#### 2. Laravel API Service (`LaravelApiService`)
**Purpose**: Handles communication with the main Laravel application.

**Features**:
- Sends processing results back to Laravel via REST API
- Connection testing and health checks
- Timeout handling and error recovery
- Structured payload formatting

#### 3. Application Processing Service (`ApplicationProcessingService`)
**Purpose**: Orchestrates the processing workflow.

**Capabilities**:
- **Synchronous Processing**: Immediate results for testing
- **Asynchronous Processing**: Background processing with Laravel callbacks
- **Batch Processing**: Multiple applications in parallel
- **Error Handling**: Comprehensive error recovery and reporting

#### 4. REST Controller (`PdfProcessingController`)
**Purpose**: Exposes HTTP endpoints for external integration.

**Endpoints**:
- `GET /api/v1/health` - Service health check
- `GET /api/v1/info` - Service information
- `POST /api/v1/process-application` - Synchronous processing
- `POST /api/v1/process-application-async` - Asynchronous processing
- `POST /api/v1/process-batch` - Batch processing
- `GET /api/v1/test-laravel-connection` - Test Laravel connectivity

## Scoring Methodology

### Financial Strength Analysis (25 points)
Analyzes financial indicators to assess vendor stability:

**Keywords Scored**:
- "bank balance" (8 points) - Direct financial position
- "credit rating" (7 points) - Creditworthiness
- "financial statement" (6 points) - Financial transparency
- "cash flow" (6 points) - Liquidity management
- "revenue" (5 points) - Business income
- "audit" (5 points) - Financial verification
- "asset" (4 points) - Company resources
- "profit" (4 points) - Profitability
- "investment" (3 points) - Growth funding
- "funding" (3 points) - Capital access

### Business Experience Analysis (20 points)
Evaluates company experience and track record:

**Year-based Scoring**:
- 20+ years: +10 bonus points
- 10-19 years: +7 bonus points
- 5-9 years: +4 bonus points
- 2-4 years: +2 bonus points

**Experience Keywords**:
- "supply chain" (6 points) - Relevant experience
- "experience" (5 points) - General experience
- "established" (4 points) - Company maturity
- "partnership" (4 points) - Business relationships
- "contract" (4 points) - Contractual experience

### Company Size Assessment (15 points)
Determines company scale and capacity:

**Employee-based Scoring**:
- 1000+ employees: +10 bonus points
- 500-999 employees: +8 bonus points
- 100-499 employees: +6 bonus points
- 50-99 employees: +4 bonus points
- 10-49 employees: +2 bonus points

**Infrastructure Keywords**:
- "manufacturing" (5 points) - Production capability
- "warehouse" (4 points) - Storage capacity
- "distribution" (4 points) - Logistics capability

### Certification & Quality (15 points)
Assesses quality standards and certifications:

**High-Value Certifications**:
- "iso" (8 points) - International standards
- "certification" (6 points) - General certifications
- "accredited" (6 points) - Official recognition
- "certified" (5 points) - Certified processes
- "compliance" (5 points) - Regulatory compliance

### Contact Information (10 points)
Evaluates completeness of contact details:

**Contact Elements**:
- Email address presence (3 points)
- Phone number presence (3 points)
- Physical address (2 points)
- Website URL (2 points)

### Document Quality (10 points)
Assesses document professionalism and structure:

**Quality Indicators**:
- Document length (1-3 points based on character count)
- Table of contents (2 points)
- Executive summary (2 points)
- Conclusion section (1 point)
- Professional language usage (2 points)

### Industry Relevance (5 points)
Matches vendor to electronics/technology industry:

**Industry Keywords**:
- "supplier" (6 points) - Supply role
- "vendor" (5 points) - Vendor role
- "manufacturer" (5 points) - Manufacturing capability
- "electronics" (5 points) - Industry match
- "technology" (4 points) - Tech relevance

## Integration with Laravel

### Request Flow
1. **Laravel Application** uploads PDF and creates application record
2. **Laravel** sends processing request to Java service via HTTP
3. **Java Service** processes PDF asynchronously
4. **Java Service** calculates score and generates analysis
5. **Java Service** sends results back to Laravel API
6. **Laravel** updates application record and triggers notifications

### API Communication

**Request Format**:
```json
{
  "applicationId": 123,
  "pdfFilePath": "/path/to/application.pdf",
  "applicationReference": "APP-123456",
  "vendorEmail": "vendor@example.com",
  "vendorName": "Vendor Company"
}
```

**Response Format**:
```json
{
  "applicationId": 123,
  "applicationReference": "APP-123456",
  "score": 85,
  "status": "scored",
  "processingNotes": "Detailed analysis notes...",
  "processedAt": "2025-06-30T12:00:00",
  "success": true,
  "scoringDetails": {
    "detailedScores": {
      "financial_strength": 20,
      "business_experience": 18,
      "company_size": 12,
      "certifications": 10,
      "contact_completeness": 8,
      "document_quality": 9,
      "industry_relevance": 4
    },
    "textLength": 5230,
    "pageCount": 12
  }
}
```

## Configuration

### Application Properties
```properties
# Server Configuration
server.port=8081
spring.servlet.multipart.max-file-size=10MB

# Laravel Integration
laravel.api.base-url=http://localhost:8000
laravel.api.token=your-api-token-here

# PDF Processing
pdf.processing.temp-dir=/tmp/aktina-pdf-processing
pdf.processing.max-pages=50

# Security
spring.security.user.name=aktina
spring.security.user.password=aktina123
```

## Deployment

### Running the Service

1. **Build the application**:
   ```bash
   mvn clean install
   ```

2. **Run the service**:
   ```bash
   java -jar target/java-server-0.0.1-SNAPSHOT.jar
   ```

3. **Verify service is running**:
   ```bash
   curl http://localhost:8081/api/v1/health
   ```

### Docker Support (Future Enhancement)
```dockerfile
FROM openjdk:17-jre-slim
COPY target/java-server-0.0.1-SNAPSHOT.jar app.jar
EXPOSE 8081
ENTRYPOINT ["java", "-jar", "/app.jar"]
```

## Error Handling

### PDF Processing Errors
- **File not found**: Returns error response with clear message
- **Corrupted PDF**: Attempts partial processing, reports limitations
- **Large files**: Limits processing to first 50 pages for performance
- **Memory issues**: Implements streaming processing for large documents

### Network Errors
- **Laravel API unavailable**: Queues results for retry
- **Timeout issues**: Configurable timeout with fallback handling
- **Authentication failures**: Clear error reporting with resolution steps

## Monitoring & Logging

### Health Monitoring
- **Service Health**: `/api/v1/health` endpoint
- **Laravel Connectivity**: `/api/v1/test-laravel-connection` endpoint
- **Processing Metrics**: Detailed logging of processing times and success rates

### Logging Configuration
- **INFO**: Processing start/completion, API calls
- **DEBUG**: Detailed scoring breakdown, PDF analysis steps
- **ERROR**: Processing failures, network issues, validation errors
- **WARN**: Large files, partial processing, connectivity issues

## Security

### Authentication
- **Basic Authentication** for API endpoints
- **CORS Configuration** for cross-origin requests
- **Input Validation** for all request parameters

### File Security
- **Path Validation** to prevent directory traversal
- **File Type Validation** to ensure only PDFs are processed
- **Size Limits** to prevent resource exhaustion

## Performance

### Optimization Features
- **Async Processing** for non-blocking operations
- **Thread Pool** management for concurrent processing
- **Memory Management** for large PDF files
- **Connection Pooling** for HTTP client operations

### Scalability
- **Stateless Design** allows horizontal scaling
- **Configurable Thread Pools** for load adjustment
- **Batch Processing** for high-volume scenarios
- **Resource Limits** prevent system overload

## Future Enhancements

### Planned Features
1. **Machine Learning Integration** - Train models on historical scoring data
2. **OCR Support** - Process scanned PDFs with image text
3. **Multi-language Support** - Analyze documents in multiple languages
4. **Advanced Analytics** - Industry-specific scoring algorithms
5. **Webhook Support** - Real-time notifications to multiple endpoints
6. **Database Integration** - Store processing history and analytics
7. **Monitoring Dashboard** - Real-time processing metrics and health status

### Performance Improvements
1. **Caching Layer** - Cache processed results for duplicate documents
2. **PDF Streaming** - Process large documents in chunks
3. **Parallel Processing** - Multi-threaded PDF analysis
4. **Cloud Storage Integration** - Direct cloud file processing

This microservice provides a robust, scalable solution for automated vendor application analysis, significantly reducing manual review time while maintaining consistent evaluation criteria.
