# Software Design Document (SDD)

**Aktina Supply Chain & Product Management Platform**

**Team:** Aktina Development Team  
**Project:** Supply Chain Management System  
**Date:** May 26, 2025  
**Version:** 2.0

---

## TABLE OF CONTENTS

1. [INTRODUCTION](#1-introduction)
   - 1.1 [Purpose](#11-purpose)
   - 1.2 [Scope](#12-scope)
   - 1.3 [Overview](#13-overview)
   - 1.4 [Reference Material](#14-reference-material)
   - 1.5 [Definitions and Acronyms](#15-definitions-and-acronyms)

2. [SYSTEM OVERVIEW](#2-system-overview)

3. [SYSTEM ARCHITECTURE](#3-system-architecture)
   - 3.1 [Architectural Design](#31-architectural-design)
   - 3.2 [Decomposition Description](#32-decomposition-description)
   - 3.3 [Design Rationale](#33-design-rationale)

4. [DATA DESIGN](#4-data-design)
   - 4.1 [Data Description](#41-data-description)
   - 4.2 [Data Dictionary](#42-data-dictionary)

5. [COMPONENT DESIGN](#5-component-design)

6. [HUMAN INTERFACE DESIGN](#6-human-interface-design)
   - 6.1 [Overview of User Interface](#61-overview-of-user-interface)
   - 6.2 [Screen Images](#62-screen-images)
   - 6.3 [Screen Objects and Actions](#63-screen-objects-and-actions)

7. [REQUIREMENTS MATRIX](#7-requirements-matrix)

8. [APPENDICES](#8-appendices)

---

## 1. INTRODUCTION

### 1.1 Purpose

This Software Design Document (SDD) describes the architecture and system design of the Aktina Supply Chain & Product Management Platform. It is intended for the development team, project managers, stakeholders, and system administrators who will be involved in the implementation, deployment, and maintenance of this enterprise-level supply chain management system.

The system is designed for Aktina, a tech hardware company specializing in outsourcing electronic components and assembling them into finished consumer technology products including smartphones, tablets, wireless headphones (Kina Pods), smart speakers, and future IoT devices. The platform supports efficient operations, analytics, vendor management, and machine learning capabilities for demand forecasting and customer segmentation.

### 1.2 Scope

The Aktina Supply Chain & Product Management Platform encompasses end-to-end supply chain processes with the following core functionalities:

**Primary Scope:**
- Supplier onboarding, validation, and management
- Comprehensive inventory tracking for raw materials, components, and finished goods
- Multi-level order processing (customer, internal, procurement)
- Workforce scheduling and distribution across multiple supply centers
- Product lifecycle management from Bill of Materials (BOM) to market delivery
- Real-time event tracking (shipments, assembly, stock movements)
- Role-based user authentication and authorization system
- Internal communication and messaging system
- Advanced analytics dashboards and automated reporting
- Machine learning integration for demand forecasting and customer segmentation

**Product Categories Supported:**
- Current Lines: Aktina Tab 25 Series, Kina Pods 25, Tna 25 Series, Akta 25 Series
- Future Lines: Smartphones (AktinaPhone, Kinatix), Tablets (AktinaTab), Laptops (AktiBook, KinaBook), Smartwatches (TikTina), Audio devices (KinaBuds, AktinaSound, KinaWave)

**Integration Capabilities:**
- Java-based vendor validation microservice
- Python machine learning microservices
- Third-party logistics (3PL) APIs
- External notification and communication gateways
- Real-time synchronization with Supabase (optional)

### 1.3 Overview

This document is organized into eight main sections following IEEE Std 1016-1998 guidelines:

- **Section 1 (Introduction):** Establishes the document's purpose, scope, and foundational information
- **Section 2 (System Overview):** Provides high-level system functionality and business context
- **Section 3 (System Architecture):** Details the overall system structure, component relationships, and design decisions
- **Section 4 (Data Design):** Describes data structures, database schema, and data flow
- **Section 5 (Component Design):** Explains individual modules, their interfaces, and implementation details
- **Section 6 (Human Interface Design):** Covers user interface design, screen layouts, and user interactions
- **Section 7 (Requirements Matrix):** Maps system requirements to design components
- **Section 8 (Appendices):** Contains supplementary information including technical specifications and additional documentation

### 1.4 Reference Material

- **Project Documentation:**
  - summary.md - Project overview and learning roadmap
  - ideas.md - Product roadmap and feature concepts
  - components.md - Comprehensive tech components and raw materials guide
  - sdd_template.txt - IEEE standard document structure

- **Industry Standards:**
  - IEEE Std 1016-1998 - Recommended Practice for Software Design Descriptions
  - ISO 9001:2015 - Quality Management Systems
  - GDPR - General Data Protection Regulation for data handling
  - WCAG 2.1 AA - Web Content Accessibility Guidelines

- **Technical References:**
  - Laravel 10.x Documentation
  - Spring Boot 3.x Documentation
  - MySQL 8.0 Reference Manual
  - Vue.js 3 Framework Documentation

- **Industry Best Practices:**
  - Supply chain methodologies from Nothing, Fairphone, OnePlus
  - Enterprise software design patterns
  - Microservices architecture guidelines

### 1.5 Definitions and Acronyms

**Business Terms:**
- **SCM**: Supply Chain Management - coordination of product flow from suppliers to customers
- **BOM**: Bill of Materials - detailed list of components, materials, and assemblies required to manufacture a product
- **SKU**: Stock Keeping Unit - unique identifier for each distinct product and service
- **ERP**: Enterprise Resource Planning - integrated management of business processes
- **3PL**: Third-Party Logistics - outsourced logistics and distribution services
- **KPI**: Key Performance Indicator - measurable values demonstrating organizational effectiveness
- **OEM**: Original Equipment Manufacturer - company that produces parts and equipment
- **ODM**: Original Design Manufacturer - company that designs and manufactures products

**Technical Terms:**
- **API**: Application Programming Interface - set of protocols for building software applications
- **ML**: Machine Learning - algorithms that improve through experience
- **IoT**: Internet of Things - network of interconnected computing devices
- **SoC**: System-on-Chip - integrated circuit incorporating multiple electronic components
- **MEMS**: Micro-Electro-Mechanical Systems - miniaturized mechanical and electro-mechanical elements
- **RF**: Radio Frequency - electromagnetic wave frequencies
- **CMOS**: Complementary Metal-Oxide-Semiconductor - technology for constructing integrated circuits
- **PCB**: Printed Circuit Board - mechanically supports and electrically connects electronic components
- **NAND**: Type of flash memory storage technology
- **DRAM**: Dynamic Random Access Memory - volatile memory type
- **VCM**: Voice Coil Motor - used in camera autofocus systems
- **ANC**: Active Noise Cancellation - technology reducing unwanted ambient sounds

---

## 2. SYSTEM OVERVIEW

The Aktina Supply Chain & Product Management Platform is a comprehensive enterprise-level solution designed to streamline and optimize the complex supply chain operations of a technology hardware company. The system addresses the challenges of managing diverse product portfolios, multiple supplier relationships, inventory optimization, and demand forecasting in the competitive consumer electronics market.

### 2.1 Business Context and Background

Aktina specializes in outsourcing electronic components and assembling them into finished consumer technology products. The company operates in a rapidly evolving market where supply chain efficiency, cost optimization, and time-to-market are critical success factors. The platform addresses several key business challenges:

**Market Challenges:**
- **Supply Chain Complexity:** Managing hundreds of suppliers across Asia-Pacific, Europe, and North America
- **Component Sourcing:** Coordinating procurement of specialized components (SoCs, displays, sensors, batteries)
- **Product Diversity:** Supporting multiple product categories with varying requirements and lifecycles
- **Quality Assurance:** Ensuring compliance with international standards (AEC-Q100, IEC, RoHS, REACH)
- **Demand Variability:** Predicting market demand across different product segments and regions

**Business Objectives:**
- Reduce inventory holding costs by 15-20% through optimized stock management
- Improve supplier performance visibility and vendor risk assessment
- Accelerate product development cycles through integrated BOM management
- Enhance decision-making through real-time analytics and predictive insights
- Ensure regulatory compliance and supply chain transparency

### 2.2 System Functionality Overview

The platform provides comprehensive functionality across five core operational areas:

**1. Supplier & Vendor Management**
- Automated vendor onboarding with PDF application processing
- Supplier performance scoring and risk assessment
- Contract management and compliance tracking
- Regional supplier diversification strategies

**2. Product Lifecycle Management**
- Multi-level Bill of Materials (BOM) creation and management
- Product category support (smartphones, tablets, laptops, audio devices)
- Component specification tracking and version control
- Assembly workflow coordination

**3. Inventory & Order Management**
- Real-time inventory tracking across multiple locations
- Automated reorder point calculations and alerts
- Multi-type order processing (customer, internal, procurement)
- Integration with third-party logistics (3PL) providers

**4. Workforce & Operations Management**
- Shift scheduling and resource allocation
- Location-based workforce distribution
- Productivity tracking and performance analytics
- Cross-functional communication tools

**5. Analytics & Intelligence**
- Machine learning-powered demand forecasting
- Customer segmentation and behavior analysis
- Supplier risk scoring and performance prediction
- Automated reporting and KPI dashboards

### 2.3 Current Product Portfolio

**Existing Product Lines:**

| Product Category | Series | Variants | Target Market |
|------------------|---------|-----------|---------------|
| **Tablets** | Aktina Tab 25 | Mini, Junior, Max | Consumer, Education, Professional |
| **Audio Devices** | Kina Pods 25 | Wireless, Wired | Consumer, Audiophile |
| **Mobile Devices** | Tna 25 | Pro, Max, Mini, Edge | Flagship, Mid-range, Budget |
| **Smart Devices** | Akta 25 | Pro, Mini, Max, Edge | IoT, Home Automation |

**Future Product Roadmap (2025-2027):**

| Timeline | Product Category | Planned Series | Innovation Focus |
|----------|------------------|----------------|------------------|
| **Q3 2025** | Smartphones | AktinaPhone (Pro/Neo/Lite), Kinatix (Elite/Plus/Go) | 5G, AI Photography, Sustainability |
| **Q4 2025** | Tablets | AktinaTab (Media/Edu/Work), Foldable variants | Productivity, Content Creation |
| **Q1 2026** | Laptops | AktiBook (Air/Pro/Enterprise), KinaBook (Legion/Studio/Scholar) | Performance, Portability |
| **Q2 2026** | Wearables | TikTina Watch (Elite/Sport/Essential), AktinaFit trackers | Health, Fitness, Integration |
| **Q3 2026** | Audio Expansion | KinaBuds, AktinaSound headphones, KinaWave speakers | Spatial Audio, ANC |
| **Q4 2026** | Emerging Tech | AktinaCast streaming, KinaVision smart glasses | AR/VR, Smart Home |

### 2.4 Technology Architecture Overview

The system employs a modern, scalable architecture that supports both current operations and future growth:

**Core Technologies:**
- **Backend Framework:** Laravel 10.x (PHP 8.2+) for rapid development and robust features
- **Database:** MySQL 8.0 with optimized indexing for supply chain queries
- **Frontend:** Vue.js 3 with Laravel Blade templates for responsive user interfaces
- **Caching:** Redis for session management and high-performance data caching
- **Authentication:** Laravel Breeze/Jetstream with role-based access control

**Microservices Architecture:**
- **Java Vendor Service:** Spring Boot 3.x for PDF processing and business rule validation
- **Python ML Services:** Flask/FastAPI for machine learning model serving
- **Real-time Services:** Laravel Echo with WebSocket support for live updates

**Integration Capabilities:**
- **External APIs:** Supplier pricing feeds, logistics tracking, market data
- **Data Pipeline:** ETL processes for machine learning and analytics
- **Third-party Services:** Email/SMS gateways, payment processors, compliance databases

### 2.5 User Roles and Access Control

The system implements a comprehensive role-based access control (RBAC) system following the principle of least privilege:

**Primary User Roles:**

1. **System Administrator**
   - Full system access and configuration
   - User management and role assignment
   - System monitoring and maintenance
   - Security policy enforcement

2. **Supply Chain Manager**
   - Product and inventory oversight
   - Supplier relationship management
   - Performance analytics and reporting
   - Strategic planning and forecasting

3. **Supplier/Vendor**
   - Limited portal access for application submission
   - Order tracking and communication
   - Performance feedback and metrics
   - Document upload and compliance verification

4. **Operations Staff**
   - Day-to-day operational tasks
   - Data entry and record maintenance
   - Basic reporting and alerts
   - Workflow execution and tracking

**Access Control Matrix:**
- Role-based menu and feature visibility
- Data-level security with row-level permissions
- API endpoint authorization with JWT tokens
- Audit logging for compliance and security

### 2.6 Performance and Scalability Requirements

**Performance Targets:**
- **Response Time:** <2 seconds for standard operations
- **Throughput:** Support for 10,000+ SKUs and 1M+ transactions annually
- **Availability:** 99.9% uptime with automated failover
- **Scalability:** Horizontal scaling to support business growth

**Non-Functional Requirements:**
- **Security:** End-to-end encryption, GDPR compliance, audit trails
- **Usability:** Responsive design, WCAG 2.1 AA accessibility
- **Maintainability:** Modular architecture, comprehensive documentation
- **Extensibility:** Plugin architecture for new product categories and features

---

## 3. SYSTEM ARCHITECTURE

### 3.1 Architectural Design

The Aktina Supply Chain & Product Management Platform employs a modern, scalable **microservices-oriented architecture** with a **layered design pattern** that ensures maintainability, scalability, and extensibility. The system is designed around the principles of **separation of concerns**, **loose coupling**, and **high cohesion**.

#### 3.1.1 High-Level System Architecture

The system consists of six major subsystems that collaborate to deliver comprehensive supply chain management functionality:

```text
┌─────────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  Web UI (Laravel Blade/Vue.js) │ Mobile App │ API Gateway       │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│ Supply Chain   │ Product       │ Analytics    │ Communication   │
│ Management     │ Lifecycle     │ & Reporting  │ & Messaging     │
│ Module         │ Module        │ Module       │ Module          │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                    BUSINESS LOGIC LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│ Laravel Core │ Java Vendor    │ Python ML    │ Workflow        │
│ Services     │ Validation     │ Services     │ Engine          │
│              │ Service        │              │                 │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                    DATA ACCESS LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│ Eloquent ORM │ Redis Cache    │ File Storage │ External APIs   │
│              │                │              │                 │
└─────────────────────────────────────────────────────────────────┘
                                │
┌─────────────────────────────────────────────────────────────────┐
│                    DATA LAYER                                   │
├─────────────────────────────────────────────────────────────────┤
│ MySQL Primary │ Supabase      │ File System  │ ML Data         │
│ Database       │ (Real-time)   │ Storage      │ Warehouse       │
└─────────────────────────────────────────────────────────────────┘
```

#### 3.1.2 Core Subsystems and Responsibilities

**1. Supply Chain Management Subsystem**
- **Responsibility**: Core supply chain operations including inventory management, order processing, supplier management, and logistics coordination
- **Components**: Inventory Controller, Order Management Engine, Supplier Portal, Logistics Tracker
- **Collaboration**: Interfaces with Product Lifecycle for BOM requirements, Analytics for performance metrics, and Communication for stakeholder notifications

**2. Product Lifecycle Management Subsystem**
- **Responsibility**: Product development, BOM management, component sourcing, and lifecycle tracking from concept to market
- **Components**: Product Designer, BOM Manager, Component Sourcer, Lifecycle Tracker
- **Collaboration**: Feeds requirements to Supply Chain, provides data to Analytics, coordinates with Communication for status updates

**3. Vendor Validation & Management Subsystem**
- **Responsibility**: Automated vendor onboarding, application processing, compliance verification, and performance evaluation
- **Components**: Java-based PDF Processor, Business Rules Engine, Audit Scheduler, Performance Evaluator
- **Collaboration**: Operates independently but integrates with Supply Chain for supplier data and Analytics for performance insights

**4. Machine Learning & Intelligence Subsystem**
- **Responsibility**: Predictive analytics, demand forecasting, customer segmentation, and intelligent insights
- **Components**: Python ML Services, Data Pipeline, Model Training Engine, Prediction API
- **Collaboration**: Consumes data from all other subsystems, provides insights to Analytics and recommendations to Supply Chain

**5. Analytics & Reporting Subsystem**
- **Responsibility**: Data visualization, KPI tracking, automated reporting, and business intelligence
- **Components**: Dashboard Engine, Report Generator, KPI Calculator, Data Visualizer
- **Collaboration**: Aggregates data from all subsystems, provides insights for decision-making across the organization

**6. Communication & Messaging Subsystem**
- **Responsibility**: Internal messaging, notifications, alerts, and external communication management
- **Components**: Chat Engine, Notification Service, Alert Manager, Email/SMS Gateway
- **Collaboration**: Provides communication infrastructure for all other subsystems

#### 3.1.3 Integration Patterns

**API-First Design**: All subsystems expose RESTful APIs with standardized request/response formats
**Event-Driven Architecture**: Critical system events trigger notifications across relevant subsystems
**Message Queuing**: Laravel Queues handle asynchronous processing for performance optimization
**Circuit Breaker Pattern**: Fault tolerance for external service integrations
**CQRS (Command Query Responsibility Segregation)**: Separate read and write operations for optimal performance

### 3.2 Decomposition Description

#### 3.2.1 Supply Chain Management Module Decomposition

**Inventory Management Component**
- **Functions**: `trackStock()`, `updateInventory()`, `calculateReorderPoints()`, `manageLocations()`
- **Data Structures**: Inventory, Location, StockMovement, ReorderAlert
- **Interfaces**: InventoryAPI, LocationAPI, AlertAPI
- **Dependencies**: Product data, Supplier information, Order processing

**Order Processing Component**
- **Functions**: `createOrder()`, `processOrder()`, `updateOrderStatus()`, `calculateOrderValue()`
- **Data Structures**: Order, OrderItem, OrderStatus, OrderHistory
- **Interfaces**: OrderAPI, PaymentAPI, FulfillmentAPI
- **Dependencies**: Customer data, Inventory system, Shipping services

**Supplier Management Component**
- **Functions**: `registerSupplier()`, `evaluatePerformance()`, `manageContracts()`, `trackCompliance()`
- **Data Structures**: Supplier, Contract, PerformanceMetric, ComplianceRecord
- **Interfaces**: SupplierAPI, ContractAPI, ComplianceAPI
- **Dependencies**: Vendor validation service, Performance analytics

#### 3.2.2 Product Lifecycle Management Module Decomposition

**Product Design Component**
- **Functions**: `createProduct()`, `defineSpecifications()`, `manageVariants()`, `trackLifecycle()`
- **Data Structures**: Product, ProductCategory, Specification, LifecycleStage
- **Interfaces**: ProductAPI, CategoryAPI, SpecificationAPI
- **Dependencies**: Component database, Market research data

**BOM Management Component**
- **Functions**: `createBOM()`, `manageLevels()`, `calculateCosts()`, `validateComponents()`
- **Data Structures**: BOM, BOMLevel, Component, ComponentCost
- **Interfaces**: BOMAPI, ComponentAPI, CostAPI
- **Dependencies**: Component supplier data, Pricing information

#### 3.2.3 Vendor Validation Service Decomposition (Java)

**PDF Processing Engine**
- **Functions**: `parsePDF()`, `extractData()`, `validateFormat()`, `sanitizeInput()`
- **Data Structures**: ApplicationDocument, ExtractedData, ValidationResult
- **Interfaces**: DocumentAPI, ValidationAPI
- **Dependencies**: Apache PDFBox, Security validation

**Business Rules Engine**
- **Functions**: `applyRules()`, `evaluateCriteria()`, `generateScore()`, `recommendAction()`
- **Data Structures**: BusinessRule, EvaluationCriteria, ScoreCard, Recommendation
- **Interfaces**: RulesAPI, ScoringAPI
- **Dependencies**: External compliance databases, Credit check services

#### 3.2.4 Machine Learning Services Decomposition (Python)

**Demand Forecasting Service**
- **Functions**: `trainModel()`, `predictDemand()`, `evaluateAccuracy()`, `updateForecast()`
- **Data Structures**: ForecastModel, PredictionResult, AccuracyMetric, TrainingData
- **Interfaces**: ForecastAPI, ModelAPI, EvaluationAPI
- **Dependencies**: Historical sales data, Market indicators, Seasonal patterns

**Customer Segmentation Service**
- **Functions**: `clusterCustomers()`, `analyzeSegments()`, `predictBehavior()`, `updateSegments()`
- **Data Structures**: CustomerSegment, BehaviorPattern, SegmentationModel, PredictionOutcome
- **Interfaces**: SegmentationAPI, BehaviorAPI, PredictionAPI
- **Dependencies**: Customer transaction data, Demographics, Purchase history

**Anomaly Detection Service**
- **Functions**: `detectAnomalies()`, `getAnomalyReports()`, `suppressAnomalyAlerts()`
- **Data Structures**: AnomalyReport, AnomalyAlert, SuppressionList
- **Interfaces**: AnomalyAPI, AlertAPI, ReportAPI
- **Dependencies**: Historical transaction data, Inventory data, User behavior data

#### 3.2.5 API Gateway Service Decomposition

**MainAPIGateway**
- **Functions**: `handleRequest()`, `forwardRequest()`, `authenticateRequest()`, `logRequest()`
- **Data Structures**: RequestContext, RouteDefinition, UserSession
- **Interfaces**: API Gateway Interface, Authentication Service, Logging Service
- **Dependencies**: None (gateway component)

#### 3.2.6 Message Queue Handler Decomposition

**MessageQueueService**
- **Functions**: `publishMessage()`, `consumeMessages()`, `invalidateCache()`, `warmupCache()`
- **Data Structures**: Message, QueueConfig, CacheKey
- **Interfaces**: Message Queue Interface, Cache Service
- **Dependencies**: None (handler component)

#### 3.2.7 Performance Components Decomposition

**DatabasePoolManager**
- **Functions**: `getConnection()`, `releaseConnection()`, `cleanup()`
- **Data Structures**: ConnectionPool, ConnectionData
- **Interfaces**: Database Connection Interface
- **Dependencies**: None (manager component)

**CacheManager**
- **Functions**: `get()`, `set()`, `invalidate()`, `warmup()`
- **Data Structures**: CacheItem, CacheConfig
- **Interfaces**: Cache Interface
- **Dependencies**: None (manager component)

**QueryOptimizer**
- **Functions**: `optimizeQuery()`, `analyzeAndOptimize()`, `executeOptimizedQuery()`
- **Data Structures**: QueryPlan, OptimizationRule
- **Interfaces**: Query Optimization Interface
- **Dependencies**: None (service component)

#### 3.2.8 Security Components Decomposition

**SecurityManager**
- **Functions**: `authenticateUser()`, `authorizeAction()`, `encryptSensitiveData()`, `validateInput()`
- **Data Structures**: UserCredential, Permission, EncryptedData
- **Interfaces**: Security Interface
- **Dependencies**: None (manager component)

### 3.3 Design Rationale

#### 3.3.1 Architecture Selection Rationale

**Microservices Architecture Choice**
The decision to adopt a microservices-oriented architecture was driven by several critical factors:

**Scalability Requirements**: Different subsystems have varying load patterns - inventory tracking requires high read throughput, while ML services need computational resources. Microservices allow independent scaling.

**Technology Diversity**: The system leverages different technologies (PHP/Laravel, Java, Python) optimized for specific use cases. Microservices enable the use of the best tool for each job.

**Team Structure**: Development teams can work independently on different services, reducing coordination overhead and enabling parallel development.

**Fault Isolation**: Failure in one service (e.g., ML forecasting) doesn't affect critical operations (e.g., order processing).

#### 3.3.2 Technology Stack Rationale

**Laravel (PHP) for Core Platform**
- **Pros**: Rapid development, robust ecosystem, excellent ORM, built-in authentication/authorization
- **Trade-offs**: PHP performance considerations addressed through caching and optimization
- **Alternatives Considered**: Spring Boot (Java), Django (Python) - Laravel chosen for development velocity and team expertise

**Java for Vendor Validation Service**
- **Pros**: Excellent PDF processing libraries (PDFBox), strong enterprise integration, robust security features
- **Trade-offs**: Additional deployment complexity - justified by specialized functionality requirements
- **Alternatives Considered**: Python (slower PDF processing), Node.js (less mature PDF libraries)

**Python for Machine Learning**
- **Pros**: Best-in-class ML libraries (scikit-learn, pandas, numpy), rapid prototyping, extensive community
- **Trade-offs**: Runtime performance - mitigated through API-based integration and caching
- **Alternatives Considered**: R (less production-ready), Scala (higher learning curve)

**MySQL for Primary Database**
- **Pros**: ACID compliance, excellent Laravel integration, mature ecosystem, strong performance
- **Trade-offs**: Horizontal scaling challenges - addressed through read replicas and partitioning strategies
- **Alternatives Considered**: PostgreSQL (more features but team unfamiliarity), MongoDB (less consistency guarantees)

#### 3.3.3 Integration Pattern Rationale

**RESTful APIs over RPC**
- **Decision**: Use REST for inter-service communication
- **Rationale**: Better caching, stateless nature, HTTP standard compliance, easier testing and debugging
- **Trade-offs**: Slightly higher overhead than RPC - acceptable for business application requirements

**Event-Driven vs. Request-Response**
- **Decision**: Hybrid approach - real-time events for critical notifications, request-response for data queries
- **Rationale**: Balance between real-time responsiveness and system complexity
- **Implementation**: Laravel Events for internal notifications, WebSockets for real-time UI updates

**Database per Service vs. Shared Database**
- **Decision**: Shared MySQL with service-specific schemas, separate databases for ML services
- **Rationale**: Maintains data consistency for core business operations while enabling ML service independence
- **Trade-offs**: Some coupling between services - mitigated through well-defined database interfaces

#### 3.3.4 Alternative Architectures Considered

**Monolithic Architecture**
- **Pros**: Simpler deployment, easier debugging, lower initial complexity
- **Cons**: Poor scalability, technology lock-in, difficult team collaboration
- **Rejection Reason**: Doesn't meet long-term scalability and team growth requirements

**Event Sourcing Architecture**
- **Pros**: Complete audit trail, temporal queries, better analytics
- **Cons**: Higher complexity, storage overhead, learning curve
- **Rejection Reason**: Complexity doesn't justify benefits for current requirements - could be future enhancement

**Serverless Architecture**
- **Pros**: Auto-scaling, pay-per-use, reduced operational overhead
- **Cons**: Vendor lock-in, cold starts, complex state management
- **Rejection Reason**: Team expertise and control requirements favor traditional deployment

### 3.4 Technology Stack

#### 3.4.1 Backend Technologies

**Laravel 10.x Framework (PHP 8.2+)**
- **Core Features**: Eloquent ORM, Artisan CLI, Blade templating, job queues, event system
- **Authentication**: Laravel Breeze/Jetstream with multi-guard support
- **API**: RESTful API with Laravel Sanctum for token-based authentication
- **Testing**: PHPUnit integration with feature and unit testing capabilities

**MySQL 8.0 Database Engine**
- **Features**: ACID compliance, JSON data types, full-text search, spatial data support
- **Performance**: Optimized indexing strategies, query cache, connection pooling
- **Scaling**: Read replicas, partitioning, horizontal sharding for large datasets

**Redis Cache & Session Store**
- **Caching**: Object caching, query result caching, session storage
- **Queues**: Laravel job queue implementation for background processing
- **Real-time**: Pub/Sub for real-time notifications and live updates

#### 3.4.2 Microservices Technologies

**Java Vendor Validation Service**
- **Framework**: Spring Boot 3.x for enterprise-grade microservice development
- **PDF Processing**: Apache PDFBox for document parsing and text extraction
- **Security**: Spring Security for authentication and authorization
- **Database**: Separate MySQL instance for vendor-specific data

**Python Machine Learning Services**
- **Framework**: FastAPI for high-performance API development
- **ML Libraries**: scikit-learn, pandas, numpy for data processing and model training
- **Model Serving**: MLflow for model versioning and deployment
- **Data Pipeline**: Apache Airflow for ETL workflow orchestration

#### 3.4.3 Frontend Technologies

**Vue.js 3 Composition API**
- **UI Components**: Tailwind CSS for utility-first styling
- **State Management**: Pinia for centralized state management
- **Charts**: Chart.js and ApexCharts for data visualization
- **Real-time**: WebSocket integration for live updates

**Laravel Blade Templates**
- **Server-side Rendering**: SEO-friendly page rendering
- **Component System**: Reusable Blade components
- **Form Handling**: Laravel Form Request validation

#### 3.4.4 Infrastructure & DevOps

**Containerization**: Docker and Docker Compose for development and production environments
**CI/CD**: GitHub Actions for automated testing, building, and deployment
**Monitoring**: Laravel Telescope for development, Sentry for error tracking
**Load Balancing**: Nginx with PHP-FPM for optimal performance and scalability

---

## 4. DATA DESIGN

### 4.1 Data Description

The Aktina Supply Chain & Product Management Platform manages complex relational data structures that represent the complete supply chain ecosystem. The data architecture is designed to support high-volume transactional operations while maintaining data integrity and enabling advanced analytics.

#### 4.1.1 Core Data Domains

**1. User Management Domain**
- Manages system users, roles, permissions, and authentication data
- Implements role-based access control (RBAC) with hierarchical permissions
- Stores user activity logs and session management information

**2. Product Management Domain**
- Contains product definitions, categories, specifications, and lifecycle data
- Manages Bill of Materials (BOM) with multi-level component relationships
- Tracks product variants, configurations, and market positioning

**3. Supply Chain Domain**
- Encompasses supplier information, contracts, and performance metrics
- Manages inventory across multiple locations with real-time tracking
- Handles complex order workflows from customer requests to fulfillment

**4. Component & Materials Domain**
- Detailed component specifications, sourcing information, and supplier relationships
- Material composition data for compliance and sustainability tracking
- Component lifecycle management from procurement to end-of-life

**5. Analytics & Intelligence Domain**
- Historical transaction data optimized for machine learning algorithms
- Aggregated performance metrics and KPI calculations
- Customer behavior data for segmentation and forecasting

#### 4.1.2 Data Storage Strategy

**Primary Database (MySQL 8.0)**
- **OLTP Optimization**: Normalized schema design for transactional efficiency
- **Index Strategy**: Composite indexes on frequently queried columns (product_id, supplier_id, created_at)
- **Partitioning**: Time-based partitioning for large transaction tables (orders, events, logs)
- **Constraints**: Foreign key constraints ensure data integrity across related entities

**Caching Layer (Redis)**
- **Session Storage**: User sessions and authentication tokens
- **Query Cache**: Frequently accessed product and supplier data
- **Real-time Data**: Live inventory counts and order status updates

**File Storage**
- **Document Storage**: Vendor applications, contracts, and compliance certificates
- **Media Assets**: Product images, technical specifications, and marketing materials
- **Reports**: Generated PDF reports and data export files

**Machine Learning Data Store**
- **Feature Store**: Preprocessed features for ML model training and inference
- **Model Registry**: Versioned ML models with performance metrics
- **Training Data**: Historical datasets with proper data lineage tracking

#### 4.1.3 Data Flow Architecture

**Transactional Data Flow**
1. **Input**: User actions, API calls, external integrations
2. **Processing**: Laravel application logic with business rule validation
3. **Storage**: MySQL database with immediate consistency
4. **Caching**: Redis updates for frequently accessed data
5. **Events**: Laravel events trigger notifications and downstream processing

**Analytics Data Flow**
1. **Extract**: Periodic ETL jobs extract transactional data
2. **Transform**: Data cleaning, normalization, and feature engineering
3. **Load**: Processed data loaded into ML-optimized data structures
4. **Analysis**: ML models generate predictions and insights
5. **Presentation**: Results displayed in dashboards and reports

### 4.2 Data Dictionary

#### 4.2.1 User Management Entities

**users**
- `id` (bigint, primary key): Unique user identifier
- `name` (varchar(255), not null): Full user name
- `email` (varchar(255), unique, not null): User email address
- `email_verified_at` (timestamp, nullable): Email verification timestamp
- `password` (varchar(255), not null): Hashed password
- `role` (enum: admin, manager, supplier, staff): User role for RBAC
- `status` (enum: active, inactive, suspended): Account status
- `department` (varchar(100), nullable): Department or division
- `phone` (varchar(20), nullable): Contact phone number
- `last_login_at` (timestamp, nullable): Last successful login
- `created_at` (timestamp): Account creation date
- `updated_at` (timestamp): Last profile update

**user_permissions**
- `id` (bigint, primary key): Permission record identifier
- `user_id` (bigint, foreign key): References users.id
- `permission` (varchar(100), not null): Specific permission name
- `granted_by` (bigint, foreign key): References users.id (who granted permission)
- `granted_at` (timestamp): When permission was granted
- `expires_at` (timestamp, nullable): Optional permission expiration

#### 4.2.2 Product Management Entities

**products**
- `id` (bigint, primary key): Unique product identifier
- `sku` (varchar(50), unique, not null): Stock Keeping Unit code
- `name` (varchar(255), not null): Product name
- `category_id` (bigint, foreign key): References product_categories.id
- `description` (text, nullable): Detailed product description
- `status` (enum: concept, development, production, discontinued): Product lifecycle status
- `target_market` (varchar(100), nullable): Target market segment
- `launch_date` (date, nullable): Market launch date
- `end_of_life_date` (date, nullable): Product discontinuation date
- `base_cost` (decimal(10,2), nullable): Manufacturing base cost
- `retail_price` (decimal(10,2), nullable): Suggested retail price
- `margin_percent` (decimal(5,2), nullable): Profit margin percentage
- `created_at` (timestamp): Record creation date
- `updated_at` (timestamp): Last modification date

**product_categories**
- `id` (bigint, primary key): Category identifier
- `name` (varchar(100), unique, not null): Category name (smartphone, tablet, laptop, etc.)
- `description` (text, nullable): Category description
- `specifications_template` (json, nullable): JSON schema for category-specific specifications
- `parent_category_id` (bigint, foreign key, nullable): References product_categories.id for hierarchy
- `sort_order` (int, default 0): Display order in listings

**bills_of_materials (BOMs)**
- `id` (bigint, primary key): BOM record identifier
- `product_id` (bigint, foreign key): References products.id
- `component_id` (bigint, foreign key): References components.id
- `quantity` (decimal(8,3), not null): Required quantity per product unit
- `level` (int, not null): BOM hierarchy level (0 = final assembly, 1+ = sub-assemblies)
- `supplier_id` (bigint, foreign key): References suppliers.id (preferred supplier)
- `cost_per_unit` (decimal(8,4), not null): Component cost per unit
- `total_cost` (decimal(10,2), generated): Calculated as quantity × cost_per_unit
- `lead_time_days` (int, nullable): Component procurement lead time
- `minimum_order_quantity` (int, nullable): Supplier MOQ requirement
- `created_at` (timestamp): Record creation date
- `updated_at` (timestamp): Last modification date

#### 4.2.3 Supply Chain Entities

**suppliers**
- `id` (bigint, primary key): Unique supplier identifier
- `name` (varchar(255), not null): Legal company name
- `contact_name` (varchar(255), not null): Primary contact person
- `email` (varchar(255), not null): Primary contact email
- `phone` (varchar(20), nullable): Primary contact phone
- `address` (text, not null): Complete business address
- `country` (varchar(100), not null): Country of operation
- `region` (varchar(100), not null): Geographic region (Asia-Pacific, Europe, North America)
- `specialization` (varchar(255), nullable): Primary component specialization
- `approval_status` (enum: pending, approved, rejected, suspended): Vendor approval status
- `performance_score` (decimal(3,2), default 0.00): Calculated performance rating (0-5.00)
- `risk_level` (enum: low, medium, high, critical): Assessed supplier risk
- `certifications` (json, nullable): Array of compliance certifications
- `payment_terms` (varchar(100), nullable): Standard payment terms
- `created_at` (timestamp): Supplier registration date
- `updated_at` (timestamp): Last profile update

**vendor_applications**
- `id` (bigint, primary key): Application record identifier
- `supplier_id` (bigint, foreign key): References suppliers.id
- `application_number` (varchar(50), unique, not null): Unique application reference
- `pdf_file_path` (varchar(500), not null): Stored application document path
- `status` (enum: submitted, under_review, approved, rejected): Application status
- `review_notes` (text, nullable): Reviewer comments and observations
- `submission_date` (timestamp): Application submission timestamp
- `reviewed_by` (bigint, foreign key, nullable): References users.id (reviewer)
- `reviewed_at` (timestamp, nullable): Review completion timestamp
- `audit_scheduled_date` (date, nullable): Planned on-site audit date
- `approval_date` (date, nullable): Final approval date

**inventory**
- `id` (bigint, primary key): Inventory record identifier
- `item_type` (enum: product, component): Type of inventory item
- `item_id` (bigint, not null): References products.id or components.id
- `location_id` (bigint, foreign key): References locations.id
- `quantity_available` (decimal(10,3), not null): Current available quantity
- `quantity_reserved` (decimal(10,3), default 0): Quantity reserved for orders
- `quantity_on_order` (decimal(10,3), default 0): Quantity in procurement pipeline
- `reorder_level` (decimal(10,3), not null): Automatic reorder trigger level
- `maximum_stock` (decimal(10,3), nullable): Maximum recommended stock level
- `unit_cost` (decimal(8,4), not null): Current cost per unit
- `last_movement_date` (timestamp, nullable): Date of last stock movement
- `created_at` (timestamp): Record creation date
- `updated_at` (timestamp): Last inventory update

#### 4.2.4 Component & Materials Entities

**components**
- `id` (bigint, primary key): Unique component identifier
- `part_number` (varchar(100), unique, not null): Manufacturer part number
- `name` (varchar(255), not null): Component name
- `category` (varchar(100), not null): Component category (SoC, display, battery, etc.)
- `type` (varchar(100), not null): Specific component type
- `specifications` (json, not null): Detailed technical specifications
- `material_composition` (json, nullable): Material composition for compliance
- `weight_grams` (decimal(6,3), nullable): Component weight
- `dimensions` (json, nullable): Physical dimensions (length, width, height)
- `operating_temperature` (varchar(50), nullable): Operating temperature range
- `compliance_certifications` (json, nullable): Regulatory compliance certifications
- `datasheet_url` (varchar(500), nullable): Link to technical datasheet
- `lifecycle_status` (enum: active, obsolete, discontinued): Component lifecycle status
- `created_at` (timestamp): Record creation date
- `updated_at` (timestamp): Last specification update

**component_suppliers**
- `id` (bigint, primary key): Relationship record identifier
- `component_id` (bigint, foreign key): References components.id
- `supplier_id` (bigint, foreign key): References suppliers.id
- `supplier_part_number` (varchar(100), nullable): Supplier's part number
- `lead_time_days` (int, not null): Typical procurement lead time
- `minimum_order_quantity` (int, not null): Minimum order quantity
- `cost_tier` (enum: tier1, tier2, tier3): Cost competitiveness tier
- `preferred_supplier` (boolean, default false): Preferred supplier flag
- `last_price_update` (date, nullable): Date of last price quotation
- `created_at` (timestamp): Relationship establishment date
- `updated_at` (timestamp): Last relationship update

#### 4.2.5 Order Management Entities

**orders**
- `id` (bigint, primary key): Unique order identifier
- `order_number` (varchar(50), unique, not null): Human-readable order number
- `order_type` (enum: customer, internal, procurement): Type of order
- `customer_id` (bigint, foreign key, nullable): References customers.id (for customer orders)
- `supplier_id` (bigint, foreign key, nullable): References suppliers.id (for procurement orders)
- `status` (enum: pending, confirmed, in_production, shipped, delivered, cancelled): Order status
- `order_date` (date, not null): Order placement date
- `required_date` (date, not null): Customer required delivery date
- `promised_date` (date, nullable): Promised delivery date
- `total_value` (decimal(12,2), not null): Total order value
- `currency` (varchar(3), default 'USD'): Order currency
- `payment_terms` (varchar(100), nullable): Payment terms and conditions
- `shipping_address` (text, not null): Delivery address
- `special_instructions` (text, nullable): Special handling instructions
- `created_by` (bigint, foreign key): References users.id (order creator)
- `created_at` (timestamp): Order creation timestamp
- `updated_at` (timestamp): Last order update

**order_items**
- `id` (bigint, primary key): Order line item identifier
- `order_id` (bigint, foreign key): References orders.id
- `item_type` (enum: product, component): Type of ordered item
- `item_id` (bigint, not null): References products.id or components.id
- `quantity` (decimal(10,3), not null): Ordered quantity
- `unit_price` (decimal(8,4), not null): Price per unit
- `line_total` (decimal(10,2), generated): Calculated as quantity × unit_price
- `delivery_date` (date, nullable): Specific delivery date for this line
- `status` (enum: pending, confirmed, in_production, shipped, delivered): Line item status
- `notes` (text, nullable): Line-specific notes

#### 4.2.6 Analytics & Events Entities

**events**
- `id` (bigint, primary key): Event record identifier
- `event_type` (varchar(100), not null): Type of event (shipment, assembly, stock_movement, etc.)
- `related_entity` (varchar(50), not null): Related entity type (order, product, supplier)
- `related_id` (bigint, not null): Related entity identifier
- `timestamp` (timestamp, not null): Event occurrence timestamp
- `details` (json, not null): Event-specific details and metadata
- `severity` (enum: info, warning, error, critical): Event severity level
- `user_id` (bigint, foreign key, nullable): References users.id (user who triggered event)
- `resolved_at` (timestamp, nullable): Event resolution timestamp
- `created_at` (timestamp): Event log creation

**performance_metrics**
- `id` (bigint, primary key): Metric record identifier
- `metric_type` (varchar(100), not null): Type of metric (supplier_performance, inventory_turnover, etc.)
- `entity_type` (varchar(50), not null): Entity type being measured
- `entity_id` (bigint, not null): Entity identifier
- `metric_name` (varchar(100), not null): Specific metric name
- `metric_value` (decimal(15,4), not null): Calculated metric value
- `measurement_period` (varchar(50), not null): Period of measurement (daily, weekly, monthly)
- `calculation_date` (date, not null): Date when metric was calculated
- `metadata` (json, nullable): Additional metric context and calculation details

#### 4.2.7 Communication Entities

**messages**
- `id` (bigint, primary key): Message identifier
- `sender_id` (bigint, foreign key): References users.id (message sender)
- `receiver_id` (bigint, foreign key, nullable): References users.id (specific recipient)
- `receiver_role` (varchar(50), nullable): Role-based recipient (if not specific user)
- `subject` (varchar(255), nullable): Message subject line
- `content` (text, not null): Message content
- `message_type` (enum: direct, broadcast, system, alert): Type of message
- `priority` (enum: low, normal, high, urgent): Message priority level
- `is_read` (boolean, default false): Read status flag
- `read_at` (timestamp, nullable): Message read timestamp
- `related_entity` (varchar(50), nullable): Related business entity type
- `related_id` (bigint, nullable): Related business entity identifier
- `created_at` (timestamp): Message creation timestamp
- `updated_at` (timestamp): Last message update

This comprehensive data dictionary provides the foundation for implementing a robust, scalable supply chain management system that can handle complex relationships between products, suppliers, components, and users while maintaining data integrity and supporting advanced analytics capabilities.

---

## 5. COMPONENT DESIGN

### 5.1 Laravel Core Application Components

#### 5.1.1 Authentication & Authorization Module

**AuthenticationController**
```pseudocode
FUNCTION authenticateUser(email, password)
    INPUT: email (string), password (string)
    BEGIN
        user = User.findByEmail(email)
        IF user AND password.verify(user.hashedPassword) THEN
            token = JWT.generate(user.id, user.role)
            user.updateLastLogin()
            RETURN { success: true, token: token, user: user }
        ELSE
            RETURN { success: false, error: "Invalid credentials" }
        END IF
    END
```

**RoleBasedMiddleware**
```pseudocode
FUNCTION checkPermission(request, requiredRole)
    INPUT: request (HttpRequest), requiredRole (string)
    BEGIN
        token = request.getAuthToken()
        user = JWT.decode(token)
        IF user.role HAS_PERMISSION requiredRole THEN
            CONTINUE_REQUEST
        ELSE
            RETURN HTTP_403_FORBIDDEN
        END IF
    END
```

**Key Methods:**
- `login(LoginRequest $request)`: Process user authentication with rate limiting
- `logout(Request $request)`: Invalidate session and clear authentication tokens
- `register(RegisterRequest $request)`: Create new user account with role assignment
- `authorize(string $permission, User $user)`: Check user permissions for specific actions

#### 5.1.2 Product Lifecycle Management Module

**ProductController**
```pseudocode
FUNCTION createProduct(productData)
    INPUT: productData (ProductRequest)
    BEGIN
        product = Product.create(productData)
        category = ProductCategory.find(productData.category_id)
        
        IF productData.specifications THEN
            product.validateSpecifications(category.template)
        END IF
        
        Event.fire(ProductCreated, product)
        RETURN product
    END
```

**BOMManager**
```pseudocode
FUNCTION createBOM(productId, components)
    INPUT: productId (integer), components (array)
    BEGIN
        totalCost = 0
        FOR EACH component IN components DO
            bomItem = BOM.create({
                product_id: productId,
                component_id: component.id,
                quantity: component.quantity,
                level: component.level,
                cost_per_unit: component.cost
            })
            totalCost += component.quantity * component.cost
        END FOR
        
        Product.updateCost(productId, totalCost)
        Event.fire(BOMUpdated, productId)
        RETURN BOM.getByProduct(productId)
    END
```

**Key Methods:**
- `store(ProductRequest $request)`: Create new product with validation and BOM initialization
- `update(UpdateProductRequest $request, Product $product)`: Update product specifications
- `manageBOM(Product $product, BOMRequest $request)`: Manage multi-level Bill of Materials
- `trackLifecycle(Product $product)`: Update product lifecycle stage and notifications

#### 5.1.3 Supply Chain Management Module

**InventoryController**
```pseudocode
FUNCTION updateInventory(itemId, itemType, locationId, quantityChange, movementType)
    INPUT: itemId (integer), itemType (enum), locationId (integer), 
           quantityChange (decimal), movementType (string)
    BEGIN
        inventory = Inventory.findByItem(itemId, itemType, locationId)
        
        IF movementType = "IN" THEN
            inventory.quantity_available += quantityChange
        ELSE IF movementType = "OUT" THEN
            IF inventory.quantity_available >= quantityChange THEN
                inventory.quantity_available -= quantityChange
            ELSE
                THROW InsufficientStockException
            END IF
        END IF
        
        inventory.save()
        
        IF inventory.quantity_available <= inventory.reorder_level THEN
            Event.fire(ReorderAlert, inventory)
        END IF
        
        Event.fire(InventoryMovement, {
            inventory: inventory,
            movement_type: movementType,
            quantity: quantityChange
        })
    END
```

**OrderProcessor**
```pseudocode
FUNCTION processOrder(orderData)
    INPUT: orderData (OrderRequest)
    BEGIN
        order = Order.create(orderData)
        
        FOR EACH item IN orderData.items DO
            inventory = Inventory.findByItem(item.id, item.type)
            
            IF inventory.quantity_available >= item.quantity THEN
                inventory.reserveQuantity(item.quantity)
                OrderItem.create({
                    order_id: order.id,
                    item_id: item.id,
                    quantity: item.quantity,
                    unit_price: item.price
                })
            ELSE
                order.addBackorderItem(item)
            END IF
        END FOR
        
        order.calculateTotal()
        Event.fire(OrderCreated, order)
        RETURN order
    END
```

**Key Methods:**
- `trackStock(Request $request)`: Real-time inventory tracking with location support
- `generateReorderAlerts()`: Automated reorder point monitoring and alert generation
- `processCustomerOrder(OrderRequest $request)`: Handle customer order workflow
- `manageProcurementOrder(ProcurementRequest $request)`: Supplier order management

#### 5.1.4 Supplier & Vendor Management Module

**SupplierController**
```pseudocode
FUNCTION evaluateSupplierPerformance(supplierId, period)
    INPUT: supplierId (integer), period (string)
    BEGIN
        supplier = Supplier.find(supplierId)
        orders = Order.getBySupplier(supplierId, period)
        
        metrics = {
            on_time_delivery: 0,
            quality_score: 0,
            cost_competitiveness: 0,
            communication_score: 0
        }
        
        FOR EACH order IN orders DO
            IF order.delivered_at <= order.promised_date THEN
                metrics.on_time_delivery += 1
            END IF
            
            metrics.quality_score += order.quality_rating
            metrics.cost_competitiveness += order.cost_rating
            metrics.communication_score += order.communication_rating
        END FOR
        
        totalOrders = orders.count()
        performanceScore = (
            (metrics.on_time_delivery / totalOrders * 0.4) +
            (metrics.quality_score / totalOrders * 0.3) +
            (metrics.cost_competitiveness / totalOrders * 0.2) +
            (metrics.communication_score / totalOrders * 0.1)
        )
        
        supplier.updatePerformanceScore(performanceScore)
        RETURN metrics
    END
```

**VendorApplicationProcessor**
```pseudocode
FUNCTION submitVendorApplication(applicationData)
    INPUT: applicationData (VendorApplicationRequest)
    BEGIN
        application = VendorApplication.create({
            supplier_id: applicationData.supplier_id,
            application_number: generateApplicationNumber(),
            pdf_file_path: applicationData.pdf_file.store(),
            status: 'submitted',
            submission_date: NOW()
        })
        
        // Send to Java microservice for processing
        javaServiceResponse = HTTP.post('java-service/api/validate-vendor', {
            application_id: application.id,
            pdf_url: application.pdf_file_path
        })
        
        Event.fire(VendorApplicationSubmitted, application)
        RETURN application
    END
```

**Key Methods:**
- `store(SupplierRequest $request)`: Register new supplier with validation
- `updatePerformance(Supplier $supplier)`: Calculate and update supplier performance metrics
- `manageContracts(Supplier $supplier, ContractRequest $request)`: Contract lifecycle management
- `trackCompliance(Supplier $supplier)`: Monitor regulatory compliance status

#### 5.1.5 Analytics & Reporting Module

**AnalyticsDashboardController**
```pseudocode
FUNCTION generateSupplyChainKPIs(dateRange)
    INPUT: dateRange (DateRange)
    BEGIN
        kpis = {}
        
        // Inventory Metrics
        kpis.inventory_turnover = calculateInventoryTurnover(dateRange)
        kpis.stockout_incidents = Inventory.getStockouts(dateRange).count()
        kpis.carrying_cost = calculateCarryingCost(dateRange)
        
        // Order Metrics
        kpis.order_fulfillment_rate = calculateFulfillmentRate(dateRange)
        kpis.average_order_value = Order.getAverageValue(dateRange)
        kpis.on_time_delivery_rate = calculateOnTimeDelivery(dateRange)
        
        // Supplier Metrics
        kpis.supplier_performance_avg = Supplier.getAveragePerformance()
        kpis.vendor_diversity_score = calculateVendorDiversity()
        
        // Financial Metrics
        kpis.cost_savings = calculateCostSavings(dateRange)
        kpis.procurement_spend = Order.getTotalProcurementSpend(dateRange)
        
        RETURN kpis
    END
```

**ReportGenerator**
```pseudocode
FUNCTION generateScheduledReport(reportType, parameters)
    INPUT: reportType (string), parameters (array)
    BEGIN
        SWITCH reportType
            CASE "inventory_summary":
                data = generateInventoryReport(parameters)
            CASE "supplier_performance":
                data = generateSupplierReport(parameters)
            CASE "financial_analysis":
                data = generateFinancialReport(parameters)
            DEFAULT:
                THROW InvalidReportTypeException
        END SWITCH
        
        pdf = PDFGenerator.create(data, reportType)
        report = Report.create({
            report_type: reportType,
            file_url: pdf.store(),
            parameters: parameters,
            generated_at: NOW()
        })
        
        // Send via email if configured
        IF parameters.email_recipients THEN
            Email.send(report.file_url, parameters.email_recipients)
        END IF
        
        RETURN report
    END
```

**Key Methods:**
- `getDashboardMetrics(Request $request)`: Real-time dashboard KPI calculation
- `generateCustomReport(ReportRequest $request)`: Custom report generation with filters
- `scheduleReport(ScheduleRequest $request)`: Automated report scheduling
- `exportData(ExportRequest $request)`: Data export in multiple formats (CSV, Excel, JSON)

### 5.2 Java Vendor Validation Service Components

#### 5.2.1 PDF Processing Engine

**PDFDocumentProcessor**
```java
public class PDFDocumentProcessor {
    
    public ParsedDocument processPDF(String filePath) {
        try (PDDocument document = PDDocument.load(new File(filePath))) {
            PDFTextStripper stripper = new PDFTextStripper();
            String text = stripper.getText(document);
            
            ParsedDocument parsed = new ParsedDocument();
            parsed.setRawText(text);
            parsed.setPageCount(document.getNumberOfPages());
            parsed.setExtractedData(extractStructuredData(text));
            
            return parsed;
        } catch (IOException e) {
            throw new DocumentProcessingException("Failed to process PDF", e);
        }
    }
    
    private Map<String, Object> extractStructuredData(String text) {
        Map<String, Object> data = new HashMap<>();
        
        // Extract company information using regex patterns
        data.put("companyName", extractPattern(text, COMPANY_NAME_PATTERN));
        data.put("registrationNumber", extractPattern(text, REGISTRATION_PATTERN));
        data.put("address", extractPattern(text, ADDRESS_PATTERN));
        data.put("contactInfo", extractContactInfo(text));
        data.put("financialData", extractFinancialData(text));
        data.put("certifications", extractCertifications(text));
        
        return data;
    }
}
```

#### 5.2.2 Business Rules Engine

**VendorValidationRules**
```java
public class VendorValidationRules {
    
    public ValidationResult validateVendor(ParsedDocument document) {
        ValidationResult result = new ValidationResult();
        
        // Financial validation
        result.addResult(validateFinancialStability(document));
        
        // Regulatory compliance
        result.addResult(validateRegulatory(document));
        
        // Reputation check
        result.addResult(validateReputation(document));
        
        // Capacity assessment
        result.addResult(validateCapacity(document));
        
        return result;
    }
    
    private RuleResult validateFinancialStability(ParsedDocument document) {
        Map<String, Object> financial = document.getFinancialData();
        
        if (financial == null || financial.isEmpty()) {
            return RuleResult.fail("No financial data provided");
        }
        
        Double revenue = (Double) financial.get("annualRevenue");
        Double assets = (Double) financial.get("totalAssets");
        Double ratio = (Double) financial.get("currentRatio");
        
        if (revenue != null && revenue < MINIMUM_REVENUE_THRESHOLD) {
            return RuleResult.fail("Revenue below minimum threshold");
        }
        
        if (ratio != null && ratio < MINIMUM_CURRENT_RATIO) {
            return RuleResult.warn("Current ratio indicates potential liquidity issues");
        }
        
        return RuleResult.pass("Financial validation successful");
    }
}
```

#### 5.2.3 API Integration Controller

**VendorValidationController**
```java
@RestController
@RequestMapping("/api/vendor-validation")
public class VendorValidationController {
    
    @PostMapping("/validate")
    public ResponseEntity<ValidationResponse> validateVendor(@RequestBody ValidationRequest request) {
        try {
            // Process PDF document
            ParsedDocument document = pdfProcessor.processPDF(request.getPdfFilePath());
            
            // Apply business rules
            ValidationResult result = rulesEngine.validateVendor(document);
            
            // Schedule audit if approved
            if (result.isApproved()) {
                auditScheduler.scheduleAudit(request.getSupplierId(), result.getAuditDate());
            }
            
            // Callback to main platform
            callbackService.notifyMainPlatform(request.getApplicationId(), result);
            
            return ResponseEntity.ok(new ValidationResponse(result));
            
        } catch (Exception e) {
            logger.error("Validation failed for application: " + request.getApplicationId(), e);
            return ResponseEntity.status(500).body(new ValidationResponse("Validation failed"));
        }
    }
}
```

### 5.3 Python Machine Learning Services Components

#### 5.3.1 Demand Forecasting Service

**DemandForecastingModel**
```python
class DemandForecastingService:
    
    def __init__(self):
        self.model = None
        self.feature_pipeline = Pipeline([
            ('scaler', StandardScaler()),
            ('selector', SelectKBest(k=10))
        ])
    
    def train_model(self, historical_data):
        """
        Train demand forecasting model using historical sales data
        """
        # Prepare features
        features = self.prepare_features(historical_data)
        target = historical_data['demand']
        
        # Apply feature engineering
        X_processed = self.feature_pipeline.fit_transform(features)
        
        # Train ensemble model
        self.model = VotingRegressor([
            ('rf', RandomForestRegressor(n_estimators=100)),
            ('gbm', GradientBoostingRegressor()),
            ('lstm', self.create_lstm_model(X_processed.shape[1]))
        ])
        
        self.model.fit(X_processed, target)
        
        return self.evaluate_model(X_processed, target)
    
    def predict_demand(self, product_id, forecast_period):
        """
        Generate demand forecast for specific product and time period
        """
        if not self.model:
            raise ModelNotTrainedException("Model must be trained before prediction")
        
        # Prepare prediction features
        features = self.prepare_prediction_features(product_id, forecast_period)
        X_processed = self.feature_pipeline.transform(features)
        
        # Generate predictions with confidence intervals
        predictions = self.model.predict(X_processed)
        confidence_intervals = self.calculate_confidence_intervals(X_processed)
        
        return {
            'product_id': product_id,
            'forecast_period': forecast_period,
            'predicted_demand': predictions.tolist(),
            'confidence_intervals': confidence_intervals,
            'model_accuracy': self.get_model_accuracy()
        }
    
    def prepare_features(self, data):
        """
        Feature engineering for demand forecasting
        """
        features = pd.DataFrame()
        
        # Time-based features
        features['month'] = data['date'].dt.month
        features['quarter'] = data['date'].dt.quarter
        features['day_of_week'] = data['date'].dt.dayofweek
        features['is_holiday'] = data['date'].isin(self.get_holidays())
        
        # Product features
        features['product_category'] = data['product_category']
        features['product_age'] = (data['date'] - data['launch_date']).dt.days
        features['price'] = data['price']
        features['price_change'] = data['price'].pct_change()
        
        # Market features
        features['competitor_price'] = data['competitor_avg_price']
        features['market_trend'] = data['market_trend_score']
        features['economic_indicator'] = data['gdp_growth']
        
        # Lag features
        for lag in [1, 7, 30, 90]:
            features[f'demand_lag_{lag}'] = data['demand'].shift(lag)
        
        return features
```

#### 5.3.2 Customer Segmentation Service

**CustomerSegmentationService**
```python
class CustomerSegmentationService:
    
    def __init__(self):
        self.segmentation_model = None
        self.feature_scaler = StandardScaler()
        self.cluster_model = KMeans(n_clusters=5, random_state=42)
    
    def perform_segmentation(self, customer_data):
        """
        Perform customer segmentation using RFM analysis and clustering
        """
        # Calculate RFM metrics
        rfm_data = self.calculate_rfm_metrics(customer_data)
        
        # Prepare features for clustering
        features = self.prepare_segmentation_features(rfm_data)
        scaled_features = self.feature_scaler.fit_transform(features)
        
        # Determine optimal number of clusters
        optimal_clusters = self.find_optimal_clusters(scaled_features)
        
        # Perform clustering
        self.segmentation_model = KMeans(n_clusters=optimal_clusters, random_state=42)
        clusters = self.segmentation_model.fit_predict(scaled_features)
        
        # Analyze segments
        segment_analysis = self.analyze_segments(rfm_data, clusters)
        
        return {
            'segments': segment_analysis,
            'cluster_assignments': clusters.tolist(),
            'optimal_clusters': optimal_clusters,
            'feature_importance': self.calculate_feature_importance(features, clusters)
        }
    
    def calculate_rfm_metrics(self, customer_data):
        """
        Calculate Recency, Frequency, Monetary (RFM) metrics
        """
        current_date = customer_data['order_date'].max()
        
        rfm = customer_data.groupby('customer_id').agg({
            'order_date': lambda x: (current_date - x.max()).days,  # Recency
            'order_id': 'count',  # Frequency
            'order_value': 'sum'  # Monetary
        })
        
        rfm.columns = ['recency', 'frequency', 'monetary']
        
        # Add additional behavioral metrics
        rfm['avg_order_value'] = customer_data.groupby('customer_id')['order_value'].mean()
        rfm['product_diversity'] = customer_data.groupby('customer_id')['product_category'].nunique()
        rfm['loyalty_score'] = self.calculate_loyalty_score(customer_data)
        
        return rfm
    
    def predict_customer_segment(self, customer_id):
        """
        Predict segment for a specific customer
        """
        if not self.segmentation_model:
            raise ModelNotTrainedException("Segmentation model must be trained")
        
        customer_features = self.get_customer_features(customer_id)
        scaled_features = self.feature_scaler.transform([customer_features])
        
        segment = self.segmentation_model.predict(scaled_features)[0]
        segment_probabilities = self.segmentation_model.predict_proba(scaled_features)[0]
        
        return {
            'customer_id': customer_id,
            'predicted_segment': segment,
            'segment_probabilities': segment_probabilities.tolist(),
            'segment_characteristics': self.get_segment_characteristics(segment)
        }
```

#### 5.3.3 Anomaly Detection Service

**AnomalyDetectionService**
```python
class AnomalyDetectionService:
    
    def __init__(self):
        self.isolation_forest = IsolationForest(contamination=0.1, random_state=42)
        self.autoencoder = None
        self.threshold_models = {}
    
    def detect_inventory_anomalies(self, inventory_data):
        """
        Detect anomalies in inventory levels and movements
        """
        # Prepare features for anomaly detection
        features = self.prepare_inventory_features(inventory_data)
        
        # Multiple anomaly detection approaches
        anomalies = {
            'statistical': self.statistical_anomaly_detection(features),
            'isolation_forest': self.isolation_forest_detection(features),
            'autoencoder': self.autoencoder_detection(features)
        }
        
        # Combine results with ensemble voting
        combined_anomalies = self.ensemble_anomaly_detection(anomalies)
        
        # Categorize anomalies by type
        categorized_anomalies = self.categorize_anomalies(combined_anomalies, inventory_data)
        
        return {
            'anomalies': categorized_anomalies,
            'anomaly_scores': combined_anomalies,
            'recommendations': self.generate_anomaly_recommendations(categorized_anomalies)
        }
    
    def prepare_inventory_features(self, data):
        """
        Prepare features for inventory anomaly detection
        """
        features = pd.DataFrame()
        
        # Stock level features
        features['current_stock'] = data['current_stock']
        features['stock_turnover'] = data['sales_last_30_days'] / data['avg_stock_last_30_days']
        features['days_of_supply'] = data['current_stock'] / data['avg_daily_usage']
        
        # Movement patterns
        features['inbound_rate'] = data['inbound_last_7_days'] / 7
        features['outbound_rate'] = data['outbound_last_7_days'] / 7
        features['movement_ratio'] = features['inbound_rate'] / (features['outbound_rate'] + 1e-6)
        
        # Temporal features
        features['stock_variance'] = data['stock_variance_last_30_days']
        features['demand_variance'] = data['demand_variance_last_30_days']
        features['seasonality_factor'] = data['current_month_avg'] / data['annual_avg']
        
        # Cost and value features
        features['inventory_value'] = data['current_stock'] * data['unit_cost']
        features['holding_cost_ratio'] = data['holding_cost'] / features['inventory_value']
        
        return features.fillna(0)
    
    def statistical_anomaly_detection(self, features):
        """
        Detect anomalies using statistical methods (Z-score, IQR)
        """
        anomaly_scores = pd.DataFrame(index=features.index)
        
        for column in features.columns:
            # Z-score method
            z_scores = np.abs(stats.zscore(features[column]))
            z_anomalies = z_scores > 3
            
            # IQR method
            Q1 = features[column].quantile(0.25)
            Q3 = features[column].quantile(0.75)
            IQR = Q3 - Q1
            iqr_anomalies = (features[column] < (Q1 - 1.5 * IQR)) | (features[column] > (Q3 + 1.5 * IQR))
            
            # Combine methods
            anomaly_scores[f'{column}_anomaly'] = (z_anomalies | iqr_anomalies).astype(int)
        
        return anomaly_scores.sum(axis=1) / len(features.columns)
    
    def isolation_forest_detection(self, features):
        """
        Detect anomalies using Isolation Forest
        """
        # Normalize features
        scaler = StandardScaler()
        scaled_features = scaler.fit_transform(features)
        
        # Fit isolation forest
        anomaly_scores = self.isolation_forest.fit_predict(scaled_features)
        anomaly_scores = (anomaly_scores == -1).astype(int)
        
        return pd.Series(anomaly_scores, index=features.index)
    
    def autoencoder_detection(self, features):
        """
        Detect anomalies using autoencoder reconstruction error
        """
        if self.autoencoder is None:
            self.autoencoder = self.build_autoencoder(features.shape[1])
        
        # Normalize features
        scaler = StandardScaler()
        scaled_features = scaler.fit_transform(features)
        
        # Train autoencoder
        self.autoencoder.fit(scaled_features, scaled_features, epochs=50, batch_size=32, verbose=0)
        
        # Calculate reconstruction error
        reconstructed = self.autoencoder.predict(scaled_features)
        reconstruction_error = np.mean(np.square(scaled_features - reconstructed), axis=1)
        
        # Determine threshold (95th percentile)
        threshold = np.percentile(reconstruction_error, 95)
        anomaly_scores = (reconstruction_error > threshold).astype(int)
        
        return pd.Series(anomaly_scores, index=features.index)
    
    def build_autoencoder(self, input_dim):
        """
        Build autoencoder neural network for anomaly detection
        """
        from tensorflow.keras.models import Model
        from tensorflow.keras.layers import Input, Dense
        
        # Encoder
        input_layer = Input(shape=(input_dim,))
        encoded = Dense(input_dim // 2, activation='relu')(input_layer)
        encoded = Dense(input_dim // 4, activation='relu')(encoded)
        
        # Decoder
        decoded = Dense(input_dim // 2, activation='relu')(encoded)
        decoded = Dense(input_dim, activation='linear')(decoded)
        
        autoencoder = Model(input_layer, decoded)
        autoencoder.compile(optimizer='adam', loss='mse')
        
        return autoencoder
    
    def ensemble_anomaly_detection(self, anomalies):
        """
        Combine multiple anomaly detection results using ensemble voting
        """
        # Convert to DataFrame for easier manipulation
        df = pd.DataFrame(anomalies)
        
        # Weighted voting (isolation forest gets higher weight)
        weights = {'statistical': 0.3, 'isolation_forest': 0.5, 'autoencoder': 0.2}
        ensemble_scores = sum(df[method] * weight for method, weight in weights.items())
        
        return ensemble_scores
    
    def categorize_anomalies(self, anomaly_scores, original_data):
        """
        Categorize detected anomalies by type
        """
        anomaly_threshold = 0.5
        anomalous_items = original_data[anomaly_scores > anomaly_threshold].copy()
        
        categories = {};
        
        // Stock level anomalies
        categories['overstock'] = anomalous_items[
            anomalous_items['current_stock'] > anomalous_items['max_stock_threshold']
        ]
        
        categories['understock'] = anomalous_items[
            anomalous_items['current_stock'] < anomalous_items['min_stock_threshold']
        ]
        
        // Movement anomalies
        categories['unusual_consumption'] = anomalous_items[
            abs(anomalous_items['outbound_last_7_days'] - anomalous_items['avg_weekly_outbound']) > 
            2 * anomalous_items['outbound_std']
        ]
        
        // Value anomalies
        categories['high_value_variance'] = anomalous_items[
            anomalous_items['inventory_value_change'] > anomalous_items['value_change_threshold']
        ]
        
        return categories;
    
    def generate_anomaly_recommendations(self, categorized_anomalies):
        """
        Generate recommendations based on detected anomalies
        """
        recommendations = {};
        
        for category, items in categorized_anomalies.items() {
            if category == 'overstock':
                recommendations[category] = [
                    "Consider promotional campaigns to reduce excess inventory",
                    "Review demand forecasting accuracy",
                    "Evaluate supplier order quantities"
                ]
            elif category == 'understock':
                recommendations[category] = [
                    "Expedite procurement for critical items",
                    "Review safety stock levels",
                    "Investigate supply chain disruptions"
                ]
            elif category == 'unusual_consumption':
                recommendations[category] = [
                    "Investigate sudden demand changes",
                    "Check for data quality issues",
                    "Review customer behavior patterns"
                ]
            elif category == 'high_value_variance':
                recommendations[category] = [
                    "Audit inventory valuation methods",
                    "Check for pricing errors",
                    "Review cost accounting procedures"
                ]
        }
        
        return recommendations;
    }
}
```

#### 5.3.4 API Gateway Service

**MLAPIGateway**
```python
from flask import Flask, request, jsonify
from functools import wraps
import jwt
import logging

class MLAPIGateway {
    
    def __init__(self):
        self.app = Flask(__name__)
        self.demand_service = DemandForecastingService()
        self.segmentation_service = CustomerSegmentationService()
        self.anomaly_service = AnomalyDetectionService()
        self.setup_routes()
        self.setup_logging()
    }
    
    def setup_routes(self):
        """
        Setup API routes for all ML services
        """
        @self.app.route('/api/ml/demand/forecast', methods=['POST'])
        @self.authenticate_token
        def forecast_demand():
            try:
                data = request.get_json()
                result = self.demand_service.predict_demand(
                    data['product_id'], 
                    data['forecast_period']
                )
                return jsonify(result), 200
            except Exception as e:
                return jsonify({'error': str(e)}), 500
            
        @self.app.route('/api/ml/customers/segment', methods=['POST'])
        @self.authenticate_token
        def segment_customers():
            try:
                data = request.get_json()
                result = self.segmentation_service.perform_segmentation(data['customer_data'])
                return jsonify(result), 200
            except Exception as e:
                return jsonify({'error': str(e)}), 500
            
        @self.app.route('/api/ml/anomalies/detect', methods=['POST'])
        @self.authenticate_token
        def detect_anomalies():
            try:
                data = request.get_json()
                result = self.anomaly_service.detect_inventory_anomalies(data['inventory_data'])
                return jsonify(result), 200
            except Exception as e:
                return jsonify({'error': str(e)}), 500
            
        @self.app.route('/api/ml/health', methods=['GET'])
        def health_check():
            return jsonify({
                'status': 'healthy',
                'services': {
                    'demand_forecasting': 'active',
                    'customer_segmentation': 'active',
                    'anomaly_detection': 'active'
                }
            }), 200
    }
    
    def authenticate_token(self, f):
        """
        Decorator for API token authentication
        """
        @wraps(f)
        def decorated(*args, **kwargs):
            token = request.headers.get('Authorization')
            if not token:
                return jsonify({'error': 'No token provided'}), 401
            
            try:
                # Remove 'Bearer ' prefix
                token = token.replace('Bearer ', '')
                payload = jwt.decode(token, self.app.config['SECRET_KEY'], algorithms=['HS256'])
                request.user_id = payload['user_id']
            except jwt.ExpiredSignatureError:
                return jsonify({'error': 'Token has expired'}), 401
            except jwt.InvalidTokenError:
                return jsonify({'error': 'Invalid token'}), 401
            
            return f(*args, **kwargs)
        return decorated
    
    def setup_logging(self):
        """
        Setup comprehensive logging for ML services
        """
        logging.basicConfig(
            level=logging.INFO,
            format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
            handlers=[
                logging.FileHandler('/var/log/ml-api-gateway.log'),
                logging.StreamHandler()
            ]
        )
        self.logger = logging.getLogger(__name__)
    
    def run(self, host='0.0.0.0', port=5000, debug=False):
        """
        Run the ML API Gateway service
        """
        self.logger.info(f"Starting ML API Gateway on {host}:{port}")
        self.app.run(host=host, port=port, debug=debug)
}
```

### 5.4 Integration Components

#### 5.4.1 Message Queue Handler

**MessageQueueService**
```python
import pika
import json
from enum import Enum

class MessageType(Enum):
    INVENTORY_UPDATE = "inventory.update"
    ORDER_CREATED = "order.created"
    SUPPLIER_VALIDATED = "supplier.validated"
    DEMAND_FORECAST = "demand.forecast"
    ANOMALY_DETECTED = "anomaly.detected"

class MessageQueueService {
    
    def __init__(self, rabbitmq_url):
        self.connection = pika.BlockingConnection(pika.URLParameters(rabbitmq_url))
        self.channel = self.connection.channel()
        self.setup_exchanges_and_queues()
    }
    
    def setup_exchanges_and_queues(self):
        """
        Setup exchanges and queues for different message types
        """
        # Declare exchanges
        self.channel.exchange_declare(exchange='aktina.events', exchange_type='topic')
        self.channel.exchange_declare(exchange='aktina.commands', exchange_type='direct')
        
        # Declare queues
        queue_configs = [
            ('inventory.queue', 'aktina.events', 'inventory.*'),
            ('orders.queue', 'aktina.events', 'order.*'),
            ('suppliers.queue', 'aktina.events', 'supplier.*'),
            ('ml.queue', 'aktina.events', 'ml.*'),
            ('notifications.queue', 'aktina.events', 'notification.*')
        ]
        
        for queue_name, exchange, routing_key in queue_configs:
            self.channel.queue_declare(queue=queue_name, durable=True)
            self.channel.queue_bind(exchange=exchange, queue=queue_name, routing_key=routing_key)
    }
    
    def publish_message(self, message_type: MessageType, payload: dict, routing_key: str = None):
        """
        Publish message to appropriate exchange
        """
        if routing_key is None:
            routing_key = message_type.value
        
        message = {
            'type': message_type.value,
            'timestamp': datetime.utcnow().isoformat(),
            'payload': payload
        }
        
        self.channel.basic_publish(
            exchange='aktina.events',
            routing_key=routing_key,
            body=json.dumps(message),
            properties=pika.BasicProperties(
                delivery_mode=2,  # Make message persistent
                content_type='application/json'
            )
        )
    }
    
    def consume_messages(self, queue_name: str, callback_function):
        """
        Consume messages from specified queue
        """
        def wrapper(ch, method, properties, body):
            try {
                message = json.loads(body)
                callback_function(message)
                ch.basic_ack(delivery_tag=method.delivery_tag)
            } catch (Exception e) {
                # Log error and reject message
                print(f"Error processing message: {e}")
                ch.basic_nack(delivery_tag=method.delivery_tag, requeue=False)
            }
        }
        
        self.channel.basic_consume(queue=queue_name, on_message_callback=wrapper)
        self.channel.start_consuming()
    }
}
```

#### 5.4.2 API Gateway

**MainAPIGateway**
```php
<?php

class MainAPIGateway {
    
    private $routes = [];
    private $middleware = [];
    private $rateLimiter;
    private $cache;
    
    public function __construct() {
        $this->rateLimiter = new RateLimiter();
        $this->cache = new CacheManager();
        $this->setupRoutes();
    }
    
    private function setupRoutes() {
        // Internal service routes
        $this->routes = [
            '/api/vendor-validation/*' => 'http://vendor-validation-service:8080',
            '/api/ml/*' => 'http://ml-service:5000',
            '/api/inventory/*' => 'internal',
            '/api/orders/*' => 'internal',
            '/api/suppliers/*' => 'internal',
            '/api/products/*' => 'internal'
        ];
    }
    
    public function handleRequest($request) {
        try {
            // Apply rate limiting
            if (!$this->rateLimiter->allowRequest($request->getClientIp())) {
                return $this->errorResponse('Rate limit exceeded', 429);
            }
            
            // Apply authentication middleware
            $user = $this->authenticateRequest($request);
            if (!$user) {
                return $this->errorResponse('Unauthorized', 401);
            }
            
            // Route the request
            $route = $this->matchRoute($request->getPath());
            if (!$route) {
                return $this->errorResponse('Route not found', 404);
            }
            
            // Check cache for GET requests
            if ($request->getMethod() === 'GET') {
                $cachedResponse = $this->cache->get($this->getCacheKey($request));
                if ($cachedResponse) {
                    return $cachedResponse;
                }
            }
            
            // Forward request to appropriate service
            $response = $this->forwardRequest($request, $route);
            
            // Cache successful GET responses
            if ($request->getMethod() === 'GET' && $response->getStatusCode() === 200) {
                $this->cache->set($this->getCacheKey($request), $response, 300); // 5 minutes
            }
            
            return $response;
            
        } catch (Exception $e) {
            Log::error('API Gateway error: ' . $e->getMessage());
            return $this->errorResponse('Internal server error', 500);
        }
    }
    
    private function forwardRequest($request, $serviceUrl) {
        if ($serviceUrl === 'internal') {
            // Handle internal Laravel routes
            return $this->handleInternalRoute($request);
        } else {
            // Forward to external microservice
            return $this->forwardToMicroservice($request, $serviceUrl);
        }
    }
    
    private function forwardToMicroservice($request, $serviceUrl) {
        $client = new GuzzleHttp\Client();
        
        $options = [
            'headers' => $request->headers->all(),
            'timeout' => 30,
            'connect_timeout' => 10
        ];
        
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $options['json'] = $request->getContent();
        }
        
        $response = $client->request(
            $request->getMethod(),
            $serviceUrl . $request->getRequestUri(),
            $options
        );
        
        return new Response(
            $response->getBody()->getContents(),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
}
```

#### 5.4.3 Database Connection Pool Manager

**DatabasePoolManager**
```php
<?php

class DatabasePoolManager {
    
    private static $instance = null;
    private $pools = [];
    private $config;
    
    private function __construct() {
        $this->config = config('database.pools');
        $this->initializePools();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializePools() {
        foreach ($this->config as $poolName => $poolConfig) {
            $this->pools[$poolName] = [
                'connections' => [],
                'active' => 0,
                'max_connections' => $poolConfig['max_connections'],
                'min_connections' => $poolConfig['min_connections'],
                'config' => $poolConfig
            ];
            
            // Initialize minimum connections
            for ($i = 0; $i < $poolConfig['min_connections']; $i++) {
                $this->createConnection($poolName);
            }
        }
    }
    
    public function getConnection($poolName = 'default') {
        $pool = &$this->pools[$poolName];
        
        // Try to get an available connection
        foreach ($pool['connections'] as $key => $connection) {
            if (!$connection['in_use']) {
                $connection['in_use'] = true;
                $connection['last_used'] = time();
                return $connection['pdo'];
            }
        }
        
        // Create new connection if under max limit
        if ($pool['active'] < $pool['max_connections']) {
            return $this->createConnection($poolName);
        }
        
        // Wait for available connection or timeout
        $timeout = $pool['config']['wait_timeout'] ?? 30;
        $start = time();
        
        while (time() - $start < $timeout) {
            foreach ($pool['connections'] as $connection) {
                if (!$connection['in_use']) {
                    $connection['in_use'] = true;
                    $connection['last_used'] = time();
                    return $connection['pdo'];
                }
            }
            usleep(100000); // 100ms
        }
        
        throw new Exception("Connection pool exhausted for pool: $poolName");
    }
    
    private function createConnection($poolName) {
        $config = $this->pools[$poolName]['config'];
        
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"
        ]);
        
        $connectionData = [
            'pdo' => $pdo,
            'in_use' => true,
            'created_at' => time(),
            'last_used' => time()
        ];
        
        $this->pools[$poolName]['connections'][] = $connectionData;
        $this->pools[$poolName]['active']++;
        
        return $pdo;
    }
    
    public function releaseConnection($pdo, $poolName = 'default') {
        $pool = &$this->pools[$poolName];
        
        foreach ($pool['connections'] as &$connection) {
            if ($connection['pdo'] === $pdo) {
                $connection['in_use'] = false;
                break;
            }
        }
    }
    
    public function cleanup() {
        foreach ($this->pools as $poolName => &$pool) {
            $this->cleanupPool($poolName);
        }
    }
    
    private function cleanupPool($poolName) {
        $pool = &$this->pools[$poolName];
        $maxIdleTime = $pool['config']['max_idle_time'] ?? 3600; // 1 hour
        $currentTime = time();
        
        foreach ($pool['connections'] as $key => $connection) {
            if (!$connection['in_use'] && 
                ($currentTime - $connection['last_used']) > $maxIdleTime &&
                count($pool['connections']) > $pool['config']['min_connections']) {
                
                unset($pool['connections'][$key]);
                $pool['active']--;
            }
        }
    }
}
```

#### 5.5 Performance and Optimization Components

#### 5.5.1 Caching Layer

**CacheManager**
```php
<?php

class CacheManager {
    
    private $redis;
    private $memcached;
    private $defaultTtl = 3600;
    
    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(config('cache.redis.host'), config('cache.redis.port'));
        
        $this->memcached = new Memcached();
        $this->memcached->addServer(config('cache.memcached.host'), config('cache.memcached.port'));
    }
    
    public function get($key, $layer = 'redis') {
        try {
            switch ($layer) {
                case 'redis':
                    $value = $this->redis->get($key);
                    break;
                case 'memcached':
                    $value = $this->memcached->get($key);
                    break;
                default:
                    $value = Cache::get($key);
            }
            
            return $value ? json_decode($value, true) : null;
            
        } catch (Exception $e) {
            Log::warning("Cache get failed for key: $key", ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    public function set($key, $value, $ttl = null, $layer = 'redis') {
        $ttl = $ttl ?? $this->defaultTtl;
        $serializedValue = json_encode($value);
        
        try {
            switch ($layer) {
                case 'redis':
                    return $this->redis->setex($key, $ttl, $serializedValue);
                case 'memcached':
                    return $this->memcached->set($key, $serializedValue, $ttl);
                default:
                    return Cache::put($key, $serializedValue, $ttl);
            }
        } catch (Exception $e) {
            Log::warning("Cache set failed for key: $key", ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    public function invalidate($pattern) {
        try {
            $keys = $this->redis->keys($pattern);
            if (!empty($keys)) {
                $this->redis->del($keys);
            }
            
            // For memcached, we'll use a different approach
            $this->invalidateMemcachedByTag($pattern);
            
        } catch (Exception $e) {
            Log::warning("Cache invalidation failed for pattern: $pattern", ['error' => $e->getMessage()]);
        }
    }
    
    public function warmup() {
        // Warm up frequently accessed data
        $this->warmupProducts();
        $this->warmupSuppliers();
        $this->warmupInventory();
    }
    
    private function warmupProducts() {
        $products = Product::with(['categories', 'suppliers'])
                          ->where('is_active', true)
                          ->get();
        
        foreach ($products as $product) {
            $this->set("product:{$product->id}", $product->toArray(), 7200);
        }
        
        // Cache product categories
        $categories = Category::all();
        $this->set('product:categories', $categories->toArray(), 14400);
    }
    
    private function warmupSuppliers() {
        $suppliers = Supplier::where('is_active', true)->get();
        $this->set('suppliers:active', $suppliers->toArray(), 7200);
    }
    
    private function warmupInventory() {
        $inventory = Inventory::with('product')
                             ->where('quantity', '>', 0)
                             ->get();
        
        foreach ($inventory as $item) {
            $this->set("inventory:product:{$item->product_id}", $item->toArray(), 1800);
        }
    }
}
```

#### 5.5.2 Query Optimization Service

**QueryOptimizer**
```php
<?php

class QueryOptimizer {
    
    private $slowQueryThreshold = 1000; // milliseconds
    private $queryCache = [];
    
    public function optimizeQuery($query, $parameters = []) {
        $queryHash = md5($query . serialize($parameters));
        
        // Check if we have a cached execution plan
        if (isset($this->queryCache[$queryHash])) {
            return $this->executeCachedQuery($queryHash, $parameters);
        }
        
        // Analyze and optimize the query
        $optimizedQuery = $this->analyzeAndOptimize($query);
        
        // Cache the optimization
        $this->queryCache[$queryHash] = [
            'original' => $query,
            'optimized' => $optimizedQuery,
            'execution_count' => 0,
            'total_time' => 0
        ];
        
        return $this->executeOptimizedQuery($optimizedQuery, $parameters, $queryHash);
    }
    
    private function analyzeAndOptimize($query) {
        $optimizations = [];
        
        // Add appropriate indexes suggestions
        $optimizations[] = $this->suggestIndexes($query);
        
        // Optimize JOIN operations
        $optimizations[] = $this->optimizeJoins($query);
        
        // Optimize WHERE clauses
        $optimizations[] = $this->optimizeWhereClause($query);
        
        // Add query hints if beneficial
        $optimizations[] = $this->addQueryHints($query);
        
        return $this->applyOptimizations($query, $optimizations);
    }
    
    private function suggestIndexes($query) {
        $suggestions = [];
        
        // Analyze WHERE conditions
        preg_match_all('/WHERE\s+(.+?)(?:ORDER|GROUP|LIMIT|$)/i', $query, $whereMatches);
        if (!empty($whereMatches[1])) {
            $whereClause = $whereMatches[1][0];
            
            // Extract column names from WHERE clause
            preg_match_all('/(\w+)\s*[=<>!]/', $whereClause, $columnMatches);
            foreach ($columnMatches[1] as $column) {
                $suggestions[] = "Consider adding index on column: $column";
            }
        }
        
        // Analyze JOIN conditions
        preg_match_all('/JOIN\s+\w+\s+ON\s+(\w+\.\w+)\s*=\s*(\w+\.\w+)/i', $query, $joinMatches);
        for ($i = 0; $i < count($joinMatches[1]); $i++) {
            $suggestions[] = "Consider composite index on: {$joinMatches[1][$i]}, {$joinMatches[2][$i]}";
        }
        
        return $suggestions;
    }
    
    private function optimizeJoins($query) {
        // Convert subqueries to JOINs where possible
        $optimized = preg_replace_callback(
            '/WHERE\s+\w+\s+IN\s*\(\s*SELECT\s+\w+\s+FROM\s+\w+\s+WHERE\s+[^)]+\)/i',
            function($matches) {
                return $this->convertToJoin($matches[0]);
            },
            $query
        );
        
        return $optimized;
    }
    
    private function optimizeWhereClause($query) {
        // Move more selective conditions to the front
        $optimized = preg_replace_callback(
            '/WHERE\s+(.+?)(?:ORDER|GROUP|LIMIT|$)/i',
            function($matches) {
                return 'WHERE ' . $this->reorderWhereConditions($matches[1]);
            },
            $query
        );
        
        return $optimized;
    }
    
    private function addQueryHints($query) {
        // Add MySQL-specific hints for better performance
        if (stripos($query, 'SELECT') === 0) {
            // Add SQL_CALC_FOUND_ROWS if needed
            if (stripos($query, 'LIMIT') !== false) {
                $query = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $query);
            }
            
            // Use index hints for specific tables
            $query = $this->addIndexHints($query);
        }
        
        return $query;
    }
    
    private function executeOptimizedQuery($query, $parameters, $queryHash) {
        $startTime = microtime(true);
        
        try {
            $result = DB::select($query, $parameters);
            
            $executionTime = (microtime(true) - $startTime) * 1000;
            
            // Update statistics
            $this->updateQueryStatistics($queryHash, $executionTime);
            
            // Log slow queries
            if ($executionTime > $this->slowQueryThreshold) {
                $this->logSlowQuery($query, $parameters, $executionTime);
            }
            
            return $result;
            
        } catch (Exception $e) {
            Log::error("Query execution failed", [
                'query' => $query,
                'parameters' => $parameters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    private function updateQueryStatistics($queryHash, $executionTime) {
        if (isset($this->queryCache[$queryHash])) {
            $this->queryCache[$queryHash]['execution_count']++;
            $this->queryCache[$queryHash]['total_time'] += $executionTime;
        }
    }
    
    private function logSlowQuery($query, $parameters, $executionTime) {
        Log::warning("Slow query detected", [
            'query' => $query,
            'parameters' => $parameters,
            'execution_time_ms' => $executionTime,
            'threshold_ms' => $this->slowQueryThreshold
        ]);
    }
    
    public function getQueryStatistics() {
        $stats = [];
        foreach ($this->queryCache as $hash => $data) {
            if ($data['execution_count'] > 0) {
                $stats[] = [
                    'query_hash' => $hash,
                    'execution_count' => $data['execution_count'],
                    'average_time_ms' => $data['total_time'] / $data['execution_count'],
                    'total_time_ms' => $data['total_time']
                ];
            }
        }
        
        // Sort by total execution time descending
        usort($stats, function($a, $b) {
            return $b['total_time_ms'] <=> $a['total_time_ms'];
        });
        
        return $stats;
    }
}
```

### 5.6 Security Components

#### 5.6.1 Security Manager

**SecurityManager**
```php
<?php

class SecurityManager {
    
    private $encryptionKey;
    private $hashAlgorithm = 'sha256';
    private $maxLoginAttempts = 5;
    private $lockoutDuration = 900; // 15 minutes
    
    public function __construct() {
        $this->encryptionKey = config('app.encryption_key');
    }
    
    public function authenticateUser($email, $password, $ipAddress) {
        // Check for account lockout
        if ($this->isAccountLocked($email, $ipAddress)) {
            throw new AccountLockedException('Account temporarily locked due to failed login attempts');
        }
        
        // Rate limiting by IP
        if ($this->isRateLimited($ipAddress)) {
            throw new RateLimitExceededException('Too many requests from this IP address');
        }
        
        $user = User::where('email', $email)->first();
        
        if (!$user || !$this->verifyPassword($password, $user->password)) {
            $this->recordFailedAttempt($email, $ipAddress);
            throw new InvalidCredentialsException('Invalid email or password');
        }
        
        // Check if user account is active
        if (!$user->is_active) {
            throw new AccountDisabledException('Account has been disabled');
        }
        
        // Check for suspicious login patterns
        $this->checkSuspiciousActivity($user, $ipAddress);
        
        // Reset failed attempts on successful login
        $this->clearFailedAttempts($email, $ipAddress);
        
        // Generate secure session token
        $token = $this->generateSecureToken($user);
        
        // Log successful authentication
        $this->logAuthenticationEvent($user, $ipAddress, 'success');
        
        return [
            'user' => $user,
            'token' => $token,
            'expires_at' => now()->addHours(8)
        ];
    }
    
    public function authorizeAction($user, $resource, $action) {
        // Check user permissions
        $hasPermission = $user->hasPermissionTo("$action.$resource");
        
        if (!$hasPermission) {
            // Check role-based access
            $hasRoleAccess = $user->roles()
                                 ->whereHas('permissions', function($query) use ($resource, $action) {
                                     $query->where('name', "$action.$resource");
                                 })
                                 ->exists();
            
            if (!$hasRoleAccess) {
                $this->logAuthorizationFailure($user, $resource, $action);
                throw new UnauthorizedException("Access denied for action: $action on resource: $resource");
            }
        }
        
        // Additional context-based authorization
        if (!$this->checkContextualAuthorization($user, $resource, $action)) {
            throw new UnauthorizedException("Contextual authorization failed");
        }
        
        return true;
    }
    
    public function encryptSensitiveData($data) {
        $cipher = 'AES-256-GCM';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt(
            json_encode($data),
            $cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        return base64_encode($iv . $tag . $encrypted);
    }
    
    public function decryptSensitiveData($encryptedData) {
        $data = base64_decode($encryptedData);
        $cipher = 'AES-256-GCM';
        $ivLength = openssl_cipher_iv_length($cipher);
        
        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, 16);
        $encrypted = substr($data, $ivLength + 16);
        
        $decrypted = openssl_decrypt(
            $encrypted,
            $cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        return json_decode($decrypted, true);
    }
    
    public function sanitizeInput($input, $type = 'string') {
        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'string':
            default:
                return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    public function validateInput($input, $rules) {
        $validator = Validator::make($input, $rules);
        
        if ($validator->fails()) {
            throw new ValidationException($validator->errors());
        }
        
        // Additional security validations
        foreach ($input as $key => $value) {
            // Check for SQL injection patterns
            if ($this->containsSqlInjectionPattern($value)) {
                throw new SecurityException("Potential SQL injection detected in field: $key");
            }
            
            // Check for XSS patterns
            if ($this->containsXssPattern($value)) {
                throw new SecurityException("Potential XSS attack detected in field: $key");
            }
        }
        
        return true;
    }
    
    private function containsSqlInjectionPattern($input) {
        $patterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bupdate\b.*\bset\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bdrop\b.*\btable\b)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function containsXssPattern($input) {
        $patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function isAccountLocked($email, $ipAddress) {
        $attempts = DB::table('failed_login_attempts')
                     ->where('email', $email)
                     ->where('ip_address', $ipAddress)
                     ->where('created_at', '>', now()->subSeconds($this->lockoutDuration))
                     ->count();
        
        return $attempts >= $this->maxLoginAttempts;
    }
    
    private function recordFailedAttempt($email, $ipAddress) {
        DB::table('failed_login_attempts')->insert([
            'email' => $email,
            'ip_address' => $ipAddress,
            'created_at' => now()
        ]);
    }
    
    private function generateSecureToken($user) {
        return JWTAuth::fromUser($user);
    }
}
```

This completes Section 5 (Component Design) with comprehensive component specifications including:

1. **Laravel Core Components**: Models, controllers, services, and middleware
2. **Java Vendor Validation Service**: PDF processing, business rules engine, and API controllers
3. **Python ML Services**: Demand forecasting, customer segmentation, anomaly detection, and API gateway
4. **Integration Components**: Message queue handling and API gateway
5. **Performance Components**: Database connection pooling, caching, and query optimization
6. **Security Components**: Authentication, authorization, encryption, and input validation

Each component includes detailed pseudocode/code examples, architectural patterns, and implementation considerations that align with the overall system design.

