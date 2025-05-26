# Software Design Document (SDD)

## 1. INTRODUCTION

### 1.1 Purpose
This Software Design Document (SDD) provides a comprehensive and detailed description of the Aktina Supply Chain & Product Management Platform. It is intended for all stakeholders, including developers, architects, project managers, business analysts, and system administrators, to ensure a shared understanding of the system’s design, structure, and operational context. The document aims to facilitate effective communication, guide implementation, and support future maintenance and enhancements.

### 1.2 Scope
The Aktina platform is an integrated, enterprise-grade solution designed to manage the full lifecycle of supply chain operations for a technology hardware company. The scope includes vendor validation, multi-level inventory and order management, product lifecycle management, workforce and operations coordination, analytics, and machine learning-driven intelligence. The platform is extensible to support new product lines and business models as outlined in the evolving product roadmap.

### 1.3 Overview
This document is organized according to the IEEE SDD template, providing:
- A high-level and detailed overview of the system’s architecture and modules
- In-depth data design, including entity relationships and data flows
- Thorough component descriptions, focusing on responsibilities and interactions
- User interface design and user experience considerations
- Requirements traceability and supporting appendices

### 1.4 Reference Material
- sdd1.md, sdd2.md (previous design documents)
- sdd_template.txt (IEEE SDD template)
- ideas.md (product roadmap and concepts)
- components.md (component and materials guide)
- summary.md (project summary)
- Industry standards: IEEE Std 1016-1998, ISO 9001:2015, GDPR, WCAG 2.1 AA
- Technical documentation: Laravel, Spring Boot, MySQL, Vue.js, FastAPI, RabbitMQ

### 1.5 Definitions and Acronyms
A comprehensive glossary is provided in the appendices, covering all business and technical terms, acronyms, and abbreviations relevant to the platform and its context.

## 2. SYSTEM OVERVIEW

The Aktina platform is designed to address the complex, multi-faceted needs of a global technology hardware supply chain. It supports the management of diverse product portfolios, hundreds of suppliers, and dynamic market demands. The system enables:
- End-to-end visibility and control over supply chain processes
- Real-time data integration and analytics for informed decision-making
- Automated compliance, risk management, and quality assurance
- Seamless collaboration between internal teams and external partners
- Scalability to accommodate new product lines, regions, and business models

### 2.1 Business Context and Background
Aktina operates in a highly competitive, innovation-driven market. The platform is tailored to support rapid product development, efficient sourcing, and agile operations. It incorporates best practices from leading tech companies and is informed by ongoing market research and product ideation (see ideas.md).

### 2.2 System Functionality Overview
The platform delivers:
- Supplier onboarding, validation, and performance management
- Multi-level Bill of Materials (BOM) and product lifecycle tracking
- Inventory management across multiple warehouses and locations
- Order management for customer, internal, and procurement workflows
- Workforce scheduling, productivity analytics, and cross-team communication
- Advanced analytics dashboards, automated reporting, and KPI tracking
- Machine learning for demand forecasting, customer segmentation, and anomaly detection
- Role-based access control and audit logging for compliance

### 2.3 Product Portfolio and Roadmap
The system supports current and future product lines, including tablets, smartphones, laptops, wearables, audio devices, and emerging IoT products. The roadmap is continuously updated to reflect new ideas and market trends (see ideas.md).

### 2.4 Technology and Integration
The platform is built on a modular, service-oriented architecture. It integrates with external APIs (logistics, compliance, market data), supports real-time data flows, and is designed for extensibility and interoperability.

### 2.5 User Roles and Access
A robust role-based access control (RBAC) system governs all user interactions. Roles include Vendor, Supplier, Procurement Manager, Inventory Manager, Production Manager, Account Officer, Customer, Sales Manager, and Auditor/Compliance Officer. Each role has clearly defined access rights, page visibility, and permitted actions (see Section 8.4).

### 2.6 Performance, Security, and Compliance
The system is engineered for high availability, low latency, and secure data handling. It complies with industry standards for data protection, accessibility, and quality management.

## 3. SYSTEM ARCHITECTURE

### 3.1 Architectural Design
The Aktina platform employs a layered, modular architecture inspired by microservices principles. Major architectural layers include:
- **Presentation Layer:** Web UI (Vue.js, Blade), mobile access, API gateway
- **Application Layer:** Business modules for supply chain, product lifecycle, analytics, and communication
- **Business Logic Layer:** Core services for inventory, orders, supplier management, and ML intelligence
- **Data Access Layer:** ORM, caching, file storage, and external API connectors
- **Data Layer:** Centralized relational database, real-time data store, ML feature store

Subsystems are loosely coupled and communicate via RESTful APIs and event-driven messaging. The architecture supports horizontal scaling, independent module deployment, and future migration to distributed microservices.

### 3.2 Decomposition Description
Each major module is further decomposed into submodules, each with a distinct responsibility:
- **Supply Chain Management:** Inventory, order, and supplier management, logistics coordination
- **Product Lifecycle Management:** Product design, BOM management, component sourcing, lifecycle tracking
- **Vendor Validation & Management:** Application intake, document analysis, compliance verification, performance evaluation
- **Machine Learning & Intelligence:** Demand forecasting, customer segmentation, anomaly detection, model management
- **Analytics & Reporting:** Dashboard, KPI calculation, report generation, data visualization
- **Communication & Messaging:** Internal chat, notifications, alerts, external communication

Each submodule is described in detail in Section 5, including its purpose, data flows, and interactions with other modules.

### 3.3 Design Rationale
The architecture was selected to maximize scalability, maintainability, and flexibility. Key considerations included:
- Support for rapid product and business model evolution
- Ability to integrate best-in-class technologies for each function
- Fault isolation and independent scaling of critical services
- Clear separation of concerns and well-defined interfaces
- Compliance with security, privacy, and accessibility standards

Alternative architectures (monolithic, event-sourcing, serverless) were evaluated and are discussed in the appendices.

## 4. DATA DESIGN

### 4.1 Data Description
The platform’s data model is highly normalized and designed for both transactional integrity and analytical performance. Major data domains include:
- **User Management:** Users, roles, permissions, authentication, activity logs
- **Product Management:** Products, categories, specifications, BOM, lifecycle stages
- **Supply Chain:** Suppliers, contracts, performance metrics, inventory, orders
- **Components & Materials:** Parts, specifications, sourcing, compliance, lifecycle
- **Analytics & Intelligence:** Historical transactions, KPIs, customer behavior, ML features
- **Communication:** Messages, notifications, audit logs

Data is stored in a relational database (MySQL), with Redis for caching and a file store for documents and media. Data lineage and auditability are maintained for compliance.

### 4.2 Data Dictionary
A detailed data dictionary is provided in the appendices, listing all entities, attributes, relationships, and business rules. Entity-relationship diagrams illustrate the connections between products, components, suppliers, orders, and users. Special attention is given to:
- Multi-level BOM structures and component hierarchies
- Supplier-product relationships and performance scoring
- Order workflows and inventory movements
- ML feature extraction and data pipelines

## 5. COMPONENT DESIGN

Each module and submodule is described in terms of its purpose, responsibilities, and interactions. No implementation details or code are included.

### 5.1 Module Overview and Details

#### 5.1.1 Authentication & Authorization
- **Purpose:** Manage user sessions, role assignments, and access control. Ensures secure authentication and granular permission checks for all actions.
- **Key Pages:**
  - **Login Page:**
    - *Description:* Entry point for all users. Allows login with credentials and provides password reset options.
    - *User Actions:* Login, request password reset.
    - *Roles with Access:* All roles.
    - *Components:* Login form, password reset link, error/feedback messages.
  - **SignUp Page:**
    - *Description:* Entry point from login via signup button. Allows signup with google, email, email verifcation.
    - *User Actions:* Signup.
    - *Roles with Access:* All roles.
    - *Components:*Signup buttion, user form, login link, error/feedback messages.
  - **User Profile Page:**
    - *Description:* Displays and allows editing of user profile information and password.
    - *User Actions:* View/edit profile, change password, view session history.
    - *Roles with Access:* All roles.
    - *Components:* Profile form, password change dialog, session log table.

#### 5.1.2 Product Management
- **Purpose:** Handles product creation, specification management, BOM definition, and lifecycle tracking. Supports product variants and market segmentation.
- **Key Pages:**
  - **Product List Page:**
    - *Description:* Shows all products with filters for category, status, and market.
    - *User Actions:* View product details, filter/search, export list.
    - *Roles with Access:* Production Manager, Procurement Manager, Inventory Manager, Sales Manager, Auditor.
    - *Components:* Product table, filter bar, export button, pagination.
  - **Product Detail Page:**
    - *Description:* Detailed view of a single product, including specifications, BOM, and lifecycle status.
    - *User Actions:* Edit product, view BOM, update lifecycle status, add notes.
    - *Roles with Access:* Production Manager, Procurement Manager, Auditor.
    - *Components:* Product info card, BOM tree, lifecycle timeline, notes section.
  - **BOM Editor Page:**
    - *Description:* Allows creation and editing of multi-level Bill of Materials for a product.
    - *User Actions:* Add/remove components, set quantities, assign suppliers.
    - *Roles with Access:* Production Manager, Procurement Manager.
    - *Components:* BOM tree editor, component search, supplier assignment panel.

#### 5.1.3 Inventory Management
- **Purpose:** Tracks stock levels, manages multi-location inventory, triggers reorder alerts, and supports real-time updates. Integrates with order and supplier modules.
- **Key Pages:**
  - **Inventory Dashboard:**
    - *Description:* Overview of inventory status, stock alerts, and key metrics.
    - *User Actions:* View stock levels, acknowledge alerts, drill down to item details.
    - *Roles with Access:* Inventory Manager, Procurement Manager, Production Manager, Auditor.
    - *Components:* Inventory summary cards, alert list, stock trend chart.
  - **Inventory Detail Page:**
    - *Description:* Detailed view of inventory for a specific product/component at all locations.
    - *User Actions:* Update stock, transfer between locations, view movement history.
    - *Roles with Access:* Inventory Manager, Production Manager.
    - *Components:* Location table, stock adjustment form, movement history log.

#### 5.1.4 Order Processing
- **Purpose:** Manages the full order lifecycle, including creation, fulfillment, status tracking, and exception handling. Supports customer, procurement, and internal orders.
- **Key Pages:**
  - **Order List Page:**
    - *Description:* Displays all orders with filters for type, status, and date.
    - *User Actions:* View order details, filter/search, export orders.
    - *Roles with Access:* Procurement Manager, Inventory Manager, Account Officer, Sales Manager, Customer, Auditor.
    - *Components:* Order table, filter bar, export button, status badges.
  - **Order Detail Page:**
    - *Description:* Shows all details of a specific order, including items, status, and history.
    - *User Actions:* Update status, add notes, approve/reject, view shipment tracking.
    - *Roles with Access:* Procurement Manager, Inventory Manager, Account Officer, Sales Manager, Customer, Auditor.
    - *Components:* Order info card, item list, status timeline, notes section, shipment tracker.

#### 5.1.5 Supplier Management
- **Purpose:** Oversees supplier onboarding, contract management, compliance tracking, and performance evaluation. Integrates with vendor validation and analytics.
- **Key Pages:**
  - **Supplier Directory:**
    - *Description:* List of all suppliers with performance and compliance status.
    - *User Actions:* View supplier details, filter/search, export list.
    - *Roles with Access:* Procurement Manager, Account Officer, Auditor, Compliance Officer.
    - *Components:* Supplier table, performance score indicator, compliance badges, filter bar.
  - **Supplier Detail Page:**
    - *Description:* Detailed supplier profile, including contracts, performance metrics, and compliance documents.
    - *User Actions:* Edit supplier info, upload documents, view audit history.
    - *Roles with Access:* Procurement Manager, Account Officer, Auditor, Compliance Officer.
    - *Components:* Supplier info card, contract list, performance chart, document upload panel, audit log.

#### 5.1.6 Vendor Validation
- **Purpose:** Automates application intake, document analysis, and business rule evaluation for new suppliers. Supports compliance verification and audit scheduling.
- **Key Pages:**
  - **Vendor Application Page:**
    - *Description:* Portal for vendors to submit applications and upload required documents.
    - *User Actions:* Submit application, upload files, track application status.
    - *Roles with Access:* Vendor, Compliance Officer.
    - *Components:* Application form, document upload, status tracker, help section.
  - **Application Review Page:**
    - *Description:* Allows compliance staff to review, approve, or reject vendor applications.
    - *User Actions:* Review documents, approve/reject, add review notes, schedule audits.
    - *Roles with Access:* Compliance Officer, Auditor.
    - *Components:* Application detail view, document viewer, review form, audit scheduler.

#### 5.1.7 Analytics & Reporting
- **Purpose:** Aggregates data for dashboards, generates reports, and calculates KPIs. Supports ad-hoc queries and scheduled reporting.
- **Key Pages:**
  - **Analytics Dashboard:**
    - *Description:* Central hub for business intelligence, showing KPIs, trends, and alerts.
    - *User Actions:* View charts, filter by date/product, export data, schedule reports.
    - *Roles with Access:* All managers, Account Officer, Auditor, Compliance Officer.
    - *Components:* KPI widgets, trend charts, filter bar, export/schedule buttons.
  - **Report Center:**
    - *Description:* Access to all generated and scheduled reports.
    - *User Actions:* Download reports, schedule new reports, set up email delivery.
    - *Roles with Access:* Managers, Account Officer, Auditor.
    - *Components:* Report list, schedule form, delivery settings.

#### 5.1.8 Machine Learning Services
- **Purpose:** Provides demand forecasting, customer segmentation, and anomaly detection. Manages model training, evaluation, and deployment. Integrates with analytics and operational modules.
- **Key Pages:**
  - **ML Insights Page:**
    - *Description:* Presents machine learning-driven insights, such as demand forecasts and customer segments.
    - *User Actions:* View predictions, filter by product/segment, download insights.
    - *Roles with Access:* Managers, Production Manager, Sales Manager, Auditor.
    - *Components:* Forecast charts, segment tables, filter bar, download button.
  - **Anomaly Alerts Page:**
    - *Description:* Lists detected anomalies in inventory, orders, or supplier performance.
    - *User Actions:* Review anomalies, acknowledge, assign for investigation.
    - *Roles with Access:* Inventory Manager, Procurement Manager, Auditor, Compliance Officer.
    - *Components:* Anomaly list, detail view, action buttons.

#### 5.1.9 Messaging & Notification
- **Purpose:** Enables internal communication, real-time notifications, and alerting. Supports both user-to-user and system-generated messages.
- **Key Pages:**
  - **Inbox Page:**
    - *Description:* Centralized location for all user messages and notifications.
    - *User Actions:* Read, reply, archive, mark as read/unread.
    - *Roles with Access:* All roles.
    - *Components:* Message list, message viewer, reply form, notification center.
  - **Notification Center:**
    - *Description:* Displays system alerts, workflow updates, and critical notifications.
    - *User Actions:* Acknowledge, filter, view details.
    - *Roles with Access:* All roles.
    - *Components:* Notification list, filter bar, alert details.

#### 5.1.10 API Gateway, Caching & Security
- **Purpose:**
  - **API Gateway:** Routes requests, enforces authentication, and logs access. Provides a unified entry point for all client and service interactions.
  - **Caching & Performance:** Manages data caching, session storage, and query optimization to ensure responsiveness and scalability.
  - **Security:** Enforces authentication, authorization, encryption, and input validation. Monitors for suspicious activity and ensures compliance with security policies.
- **Key Pages:**
  - These are primarily backend services and do not have direct user-facing pages, but their status and logs may be accessible via the admin dashboard for System Administrators.

## 6. HUMAN INTERFACE DESIGN

### 6.1 Overview of User Interface
The user interface is designed for clarity, efficiency, and accessibility. It is fully responsive, supporting both desktop and mobile devices. Navigation is role-based, with dynamic menus and dashboards tailored to each user’s responsibilities. The UI provides real-time feedback, contextual help, and accessibility features in compliance with WCAG 2.1 AA.

### 6.2 Page and Component Details

For each major page, the following details are provided:
- **Page Description:** What the page is for and what information it presents.
- **User Actions:** What users can do on the page.
- **Roles with Access:** Which user roles can access the page.
- **Components:** Key UI elements and their purpose.

#### Dashboard Page
- *Description:* The main landing page after login, showing a summary of KPIs, alerts, and quick links to key actions.
- *User Actions:* View KPIs, acknowledge alerts, navigate to modules, customize widgets.
- *Roles with Access:* All roles (widgets and data shown are role-specific).
- *Components:* KPI widgets, alert panel, quick action buttons, customizable layout.

#### Product Management Pages
- *Product List:* Shows all products, allows filtering and searching.
- *Product Detail:* Shows product info, BOM, lifecycle, and related documents.
- *BOM Editor:* Allows editing of product BOMs.
- *User Actions:* View, edit, filter, export, manage BOM.
- *Roles with Access:* Production Manager, Procurement Manager, Auditor.
- *Components:* Product table, detail cards, BOM tree, document viewer.

#### Inventory Pages
- *Inventory Dashboard:* Overview of stock, alerts, and trends.
- *Inventory Detail:* Stock by location, movement history.
- *User Actions:* View, update, transfer, acknowledge alerts.
- *Roles with Access:* Inventory Manager, Production Manager, Auditor.
- *Components:* Inventory summary, location table, adjustment form, history log.

#### Order Pages
- *Order List:* All orders with filters.
- *Order Detail:* Order items, status, shipment tracking.
- *User Actions:* View, update, approve, add notes, track shipment.
- *Roles with Access:* Procurement Manager, Inventory Manager, Account Officer, Sales Manager, Customer, Auditor.
- *Components:* Order table, detail view, status timeline, shipment tracker.

#### Supplier & Vendor Pages
- *Supplier Directory:* List of suppliers, performance, compliance.
- *Supplier Detail:* Profile, contracts, performance, documents.
- *Vendor Application:* Application form, document upload, status tracker.
- *Application Review:* Review, approve/reject, schedule audit.
- *User Actions:* View, edit, upload, review, approve, schedule.
- *Roles with Access:* Procurement Manager, Account Officer, Auditor, Compliance Officer, Vendor.
- *Components:* Supplier table, detail cards, performance chart, document upload, review form.

#### Analytics & Reporting Pages
- *Analytics Dashboard:* KPIs, trends, filters, export.
- *Report Center:* List/download/schedule reports.
- *User Actions:* View, filter, export, schedule, download.
- *Roles with Access:* Managers, Account Officer, Auditor, Compliance Officer.
- *Components:* KPI widgets, trend charts, report list, export/schedule buttons.

#### ML Insights & Anomaly Pages
- *ML Insights:* Forecasts, segments, download insights.
- *Anomaly Alerts:* List, review, assign.
- *User Actions:* View, filter, download, acknowledge, assign.
- *Roles with Access:* Managers, Production Manager, Sales Manager, Inventory Manager, Auditor, Compliance Officer.
- *Components:* Forecast charts, segment tables, anomaly list, detail view.

#### Messaging & Notification Pages
- *Inbox:* Read, reply, archive messages.
- *Notification Center:* View, filter, acknowledge alerts.
- *User Actions:* Read, reply, archive, acknowledge, filter.
- *Roles with Access:* All roles.
- *Components:* Message list, viewer, reply form, notification list, alert details.

#### Admin & System Pages
- *Admin Dashboard:* System status, logs, user management.
- *User Actions:* View system health, manage users, review logs.
- *Roles with Access:* System Administrator.
- *Components:* Status cards, user table, log viewer, settings panel.

### 6.3 Screen Objects and Actions
Each page includes structured objects (tables, cards, forms, filters) and actionable elements (buttons, menus, dialogs). User actions are context-sensitive and permissions-aware. Feedback is provided through notifications, modals, and real-time updates. Accessibility features include keyboard navigation, ARIA labels, and high-contrast modes.

## 7. REQUIREMENTS MATRIX

A detailed requirements traceability matrix is provided, mapping all functional and non-functional requirements to the responsible modules, data entities, and user roles. Each requirement is cross-referenced to its source (e.g., SRS, user story) and supporting design elements.

## 8. APPENDICES

### 8.1 System Specifications
- Supported OS: Linux (recommended), Windows, macOS
- Hardware: Quad-core CPU or higher, 16GB+ RAM, 200GB+ SSD
- Software: PHP 8.2+, MySQL 8.0+, Redis, Node.js, Python 3.10+, Java 17+, Docker, RabbitMQ
- Network: Reliable broadband, secure VPN for remote access

### 8.2 Frameworks and Libraries
- **Backend:**
  - **Laravel** (core platform, RESTful APIs, RBAC, ORM, validation), **Spring Boot** (vendor validation, workflow automation, compliance microservices), **FastAPI** (machine learning services, real-time inference, lightweight APIs)
  - *Rationale:* Laravel provides rapid development, robust security, and a large ecosystem for core business logic. Spring Boot is chosen for its enterprise-grade reliability, modularity, and Java ecosystem integration, ideal for compliance and validation workflows. FastAPI is selected for its high performance, async support, and seamless integration with Python ML libraries.
- **Frontend:**
  - **Vue.js** (SPA, dynamic dashboards, component-based UI), **Tailwind CSS** (utility-first styling, rapid prototyping, accessibility), **Chart.js/ApexCharts** (data visualization, interactive analytics)
  - *Rationale:* Vue.js offers a reactive, maintainable, and scalable UI framework, supporting modular development and real-time updates. Tailwind CSS ensures consistent, accessible, and responsive design. Chart.js and ApexCharts provide flexible, visually rich analytics components.
- **Machine Learning:**
  - **scikit-learn**, **pandas**, **numpy** (data processing, model training), **MLflow** (model lifecycle management, experiment tracking)
  - *Rationale:* These libraries are industry standards for data science, enabling robust feature engineering, model development, and reproducibility. MLflow centralizes model tracking and deployment.
- **Messaging & Eventing:**
  - **RabbitMQ** (asynchronous communication, decoupled microservices, event-driven workflows)
  - *Rationale:* RabbitMQ enables reliable, scalable messaging between services, supporting real-time notifications, workflow triggers, and system integration.
- **Caching & Performance:**
  - **Redis** (in-memory caching, session storage, pub/sub)
  - *Rationale:* Redis accelerates data access, reduces database load, and supports real-time features such as notifications and analytics.
- **Database:**
  - **MySQL** (relational data, transactional integrity), **File Storage** (documents, media), **Feature Store** (ML features)
  - *Rationale:* MySQL is chosen for its maturity, reliability, and strong transactional support. File storage and feature stores enable efficient handling of unstructured and ML-specific data.
- **Security:**
  - **OAuth2/OpenID Connect** (authentication), **JWT** (stateless sessions), **HTTPS/TLS** (encryption), **input validation libraries**
  - *Rationale:* These technologies ensure secure authentication, data privacy, and compliance with industry standards (GDPR, ISO 27001).
- **DevOps & Deployment:**
  - **Docker** (containerization), **CI/CD pipelines** (automated testing, deployment), **Kubernetes** (scalability, orchestration), **Prometheus/Grafana** (monitoring, alerting)
  - *Rationale:* Containerization and orchestration enable scalable, portable deployments. CI/CD ensures rapid, reliable releases. Monitoring tools provide operational visibility and reliability.
- **Interoperability:**
  - **RESTful APIs**, **OpenAPI/Swagger** (API documentation), **Webhooks** (event integration), **External API connectors** (logistics, compliance, market data)
  - *Rationale:* Open standards and documented APIs ensure seamless integration with third-party systems, partners, and future modules.

All technologies are selected for their maturity, community support, extensibility, and proven success in enterprise environments. The stack is designed for modularity, scalability, and ease of maintenance, supporting both current needs and future growth.
