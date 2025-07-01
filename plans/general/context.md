# Aktina Supply Chain Management System Context

Aktina SCM is a Laravel-based supply chain management system for managing the supply chain operations of Aktina Technologies. The system includes the following key features:

1. User management with different roles (admin, hr_manager, production_manager, supplier, vendor, retailer)
2. Product management
3. Order tracking and management
4. Resource and BOM (Bill of Materials) management
5. Production planning and tracking
6. Vendor and supplier management
7. Retailer management and listings

The system follows Laravel best practices, including:
- Service-Repository pattern for code organization
- Eloquent ORM for database interactions
- Blade templates for views
- Livewire for dynamic UI components
- **Modern HTML + Tailwind CSS components** (replaced Mary UI)
- ApexCharts for data visualization
- Tailwind CSS for styling

## Recent UI/UX Improvements (Latest Update)

**Mary UI Replacement Completed:**
1. **Removed all Mary UI dependencies** from Livewire components
2. **Fixed all "Trait not found" errors** by removing `use Toast;` statements
3. **Replaced with modern HTML + Tailwind CSS components** featuring:
   - Gradient stat cards with hover effects
   - Modern table designs with hover states
   - Clean button components with loading states
   - Status badges with consistent color schemes
   - Responsive card layouts with proper spacing
   - Modern timeline components for order tracking
   - Clean form controls and inputs

4. **Created new modern component library:**
   - `modern-dashboard.blade.php` - Complete dashboard layout
   - `modern-button.blade.php` - Versatile button component
   - `modern-card.blade.php` - Flexible card component
   - `modern-badge.blade.php` - Status badges
   - `modern-table.blade.php` - Responsive tables
   - `modern-input.blade.php` - Form inputs with validation
   - `modern-modal.blade.php` - Accessible modals

5. **Design improvements:**
   - Consistent color scheme (blue, amber, indigo, emerald, red)
   - Modern gradients and shadows
   - Smooth transitions and hover effects
   - Better responsive design
   - Improved accessibility

## Role-Based Pre-Verification System (Latest Major Feature)

**Comprehensive verification system implemented with:**

### Database & Models:
- Updated `applications` table with score, meeting_notes, and status fields
- Added `notifications` table for email and in-app notifications
- Enhanced `retailers` table with demographics fields
- Updated models for new relationships and functionality

### Service Layer:
- `VerificationService` for role verification logic
- `ApplicationService` for vendor application workflow
- Service-Repository pattern implementation with interfaces
- Automatic notifications at all workflow stages

### Pre-Verification Views:
- **Vendor Application System:** PDF upload, status tracking, progress indicators
- **Retailer Demographics:** Comprehensive form for business information
- **Basic Role Instructions:** Simple guidance for other roles
- **Real-time Notifications:** Both email and in-app notification system

### Admin Interface:
- **Application Management:** Complete CRUD operations for vendor applications
- **Verification Dashboard:** Stats overview and recent applications
- **PDF Viewer & Scoring:** Integrated application review workflow
- **Meeting Scheduler:** Meeting management for qualified applications

### Modern UI Components:
- **FileUpload Component:** Drag-and-drop with progress indicators
- **StatusBadge Component:** Consistent status visualization
- **ProgressIndicator Component:** Multi-step workflow visualization
- **Responsive Design:** Mobile-friendly layouts across all views

### Notification System:
- **Email Notifications:** 7 notification types for all workflow events
- **In-App Notifications:** Real-time updates with notification bell
- **Notification Management:** Mark as read, list view, dropdown view

### Security & Middleware:
- `EnsureRoleVerified` middleware for access control
- Role-based routing and permission checks
- File upload validation and security
- **Comprehensive Security System (Phase 7.2):**
  - PDF validation with header checks, size limits (10MB), MIME type validation
  - Input sanitization service preventing XSS and SQL injection
  - Policy-based authorization with role-specific permissions
  - Rate limiting: file uploads (3/hour), forms, admin actions
  - Security headers: CSP, XSS protection, HTTPS enforcement
  - Strict validation rules with regex patterns for all business data

**Status:** Fully implemented and integrated. Ready for Phase 7 testing and validation.

## Java PDF Processing Microservice (Phase 7.1 Complete)

**Comprehensive Spring Boot microservice for intelligent PDF analysis:**

### Core Features:
- **Advanced PDF Text Extraction:** Apache PDFBox 3.0.1 for robust PDF processing
- **Intelligent Scoring Algorithm:** Multi-criteria weighted scoring system (100-point scale)
- **Asynchronous Processing:** Non-blocking PDF analysis with Laravel callbacks
- **RESTful API:** Complete HTTP endpoints for integration
- **Security:** Basic authentication and input validation
- **Error Handling:** Comprehensive error recovery and reporting

### Scoring Methodology:
1. **Financial Strength (25%):** Bank balance, revenue, financial statements analysis
2. **Business Experience (20%):** Years in operation, partnerships, project history
3. **Company Size (15%):** Employee count, facilities, operational scale
4. **Certifications (15%):** ISO standards, quality certifications, licenses
5. **Contact Completeness (10%):** Email, phone, address, website presence
6. **Document Quality (10%):** Structure, length, professionalism assessment
7. **Industry Relevance (5%):** Electronics/technology sector alignment

### Technical Architecture:
- **Spring Boot 3.5.3** with reactive web stack
- **Apache PDFBox** for PDF text extraction and analysis
- **WebFlux** for asynchronous Laravel API communication
- **Maven** dependency management with Docker support
- **Comprehensive logging** and monitoring capabilities

### API Endpoints:
- `POST /api/v1/process-application` - Synchronous PDF processing
- `POST /api/v1/process-application-async` - Asynchronous processing with callbacks
- `POST /api/v1/process-batch` - Batch processing for multiple applications
- `GET /api/v1/health` - Service health monitoring
- `GET /api/v1/test-laravel-connection` - Laravel API connectivity test

### Integration Features:
- **Laravel API Integration:** Automatic result callbacks to main application
- **Scoring Details:** Detailed breakdown of scoring criteria and analysis
- **Processing Notes:** Human-readable analysis summaries for admin review
- **Status Management:** Automatic status updates based on score thresholds

### Performance & Scalability:
- **Thread Pool Management:** Configurable concurrent processing
- **Memory Optimization:** Streaming processing for large PDF files
- **File Size Limits:** 10MB maximum with page count restrictions
- **Connection Pooling:** Efficient HTTP client management
- **Stateless Design:** Horizontal scaling capability

### Deployment Ready:
- **Executable JAR:** Single file deployment with embedded Tomcat
- **Configuration:** Externalized properties for different environments
- **Startup Script:** Automated service management
- **Health Monitoring:** Built-in health checks and metrics

The Java microservice provides enterprise-grade PDF processing capabilities with sophisticated business logic for automated vendor qualification, significantly reducing manual review overhead while maintaining consistent evaluation standards.
