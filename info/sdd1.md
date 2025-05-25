<img src="https://r2cdn.perplexity.ai/pplx-full-logo-primary-dark%402x.png" class="logo" width="120"/>

# Software Design Document (SDD)

**Aktina Supply Chain \& Product Management Platform**

---

## 1. Introduction

### 1.1 Purpose

This document defines the architecture, components, data design, and requirements for the Aktina Supply Chain and Product Management Platform. The system is designed for a tech hardware company managing complex supply chains and product lifecycles across smartphones, tablets, wireless headphones (Kina Pods), smart speakers, and future IoT devices. It supports efficient operations, analytics, and vendor management, with extensibility for machine learning and multi-product support.

### 1.2 Scope

The platform covers end-to-end supply chain processes: supplier onboarding, inventory tracking, order processing, workforce management, product lifecycle tracking, event-based operations (e.g., shipments, assembly), analytics dashboards, and vendor validation. It supports multiple user roles and integrates with external services (e.g., ML APIs, Java vendor server, Supabase).

### 1.3 Definitions, Acronyms, and Abbreviations

- **SCM**: Supply Chain Management
- **BOM**: Bill of Materials
- **ML**: Machine Learning
- **API**: Application Programming Interface
- **ERP**: Enterprise Resource Planning
- **3PL**: Third-Party Logistics
- **KPI**: Key Performance Indicator
- **IoT**: Internet of Things


### 1.4 References

- summary.md (project overview and requirements)
- ideas.md (product roadmap, feature ideas)
- sdd-template (structure)
- Industry best practices (Nothing, Fairphone, OnePlus, etc.)

---

## 2. System Overview

The Aktina platform is a modular web application (Laravel/PHP) with supporting services (Java, Python ML microservices). It enables:

- Centralized management of products, suppliers, inventory, and orders.
- Real-time event tracking (shipments, assembly, stock movements).
- Secure, role-based access for admins, managers, suppliers, and staff.
- Vendor onboarding and automated validation (Java microservice).
- Analytics dashboards and scheduled reporting.
- Machine learning for demand forecasting and customer segmentation.
- Support for multiple hardware product lines and future extensibility.

---

## 3. System Architecture

### 3.1 High-Level Architecture

```
[ Users ]
   |
[ Web Frontend (Laravel Blade/Vue.js) ]
   |
[ Laravel API Layer ]
   |
[ MySQL Database ] <--> [ Supabase (optional, for real-time features) ]
   |
[ External Services ]
   |--- [ Java Vendor Validation Server ]
   |--- [ Python ML Microservices ]
   |--- [ Email/SMS/Notification Gateways ]
```


### 3.2 Technology Stack

- **Backend**: PHP (Laravel Framework)
- **Frontend**: Blade templates with Vue.js components
- **Database**: MySQL (primary), Supabase (optional for real-time sync)
- **Vendor Validation**: Java microservice (Spring Boot/Javalin + PDFBox)
- **Machine Learning**: Python (Flask/FastAPI microservices)
- **Messaging/Notifications**: Laravel Queues, third-party APIs
- **Version Control**: Git + GitHub

---

## 4. Functional Requirements

### 4.1 Core SCM Features

- Inventory management for raw materials, components, and finished goods
- Order processing (customer, internal, and procurement orders)
- Workforce scheduling and distribution across supply centers
- Product creation and lifecycle tracking (from BOM to market)
- Vendor onboarding, application review, and approval workflow
- Event-based operations: shipment tracking, assembly scheduling, stock movements
- Role-based user authentication and authorization
- Internal chat/messaging for cross-role communication
- Analytics dashboards (inventory, order status, vendor performance, sales)
- Automated scheduled reporting (email/PDF)
- Support for multiple product categories (phones, tablets, headphones, speakers, IoT)


### 4.2 Machine Learning Features

- Demand forecasting (sales prediction per product/region)
- Customer segmentation (clustering by purchasing behavior)
- Supplier risk analysis (integration-ready)
- Data pipelines for ML model training and inference


### 4.3 Vendor Validation (Java Service)

- Secure PDF submission and parsing
- Automated business rule checks (financial, regulatory, reputation)
- Scheduling of on-site audits for approved vendors
- Integration with main platform via REST API

---

## 5. Non-Functional Requirements

- **Performance**: <2s response time for standard operations; scalable to 10k+ SKUs and 1M+ transactions/year
- **Security**: Role-based access, encrypted data at rest and in transit, audit logging
- **Reliability**: 99.9% uptime, automated backups, failover support
- **Usability**: Responsive UI, accessible (WCAG 2.1 AA), multi-language ready
- **Extensibility**: Modular design for new product lines and features
- **Maintainability**: Clear codebase, API documentation, CI/CD pipeline
- **Compliance**: GDPR, data retention policies, vendor compliance checks

---

## 6. Data Design

### 6.1 Entity-Relationship Diagram (ERD)

*(Textual summary; diagrams to be produced in implementation)*

- **Users**: id, name, email, password, role, status
- **Products**: id, name, category, description, status, lifecycle_stage
- **BOMs**: id, product_id, component_id, quantity, supplier_id
- **Suppliers**: id, name, contact, approval_status, performance_score
- **VendorApplications**: id, supplier_id, pdf_url, status, review_notes
- **Inventory**: id, product_id, location_id, quantity, last_updated
- **Orders**: id, order_type, product_id, quantity, status, customer_id, created_at
- **Shipments**: id, order_id, carrier, tracking_number, status, shipped_at, delivered_at
- **WorkforceAssignments**: id, user_id, location_id, shift_start, shift_end
- **Events**: id, event_type, related_id, timestamp, details
- **Messages**: id, sender_id, receiver_id, content, timestamp
- **Reports**: id, report_type, generated_for, period, file_url, created_at


### 6.2 Key Data Flows

- Product creation triggers BOM definition, supplier assignment, and initial inventory setup.
- Orders update inventory, generate shipments, and trigger event logs.
- Vendor applications are routed to the Java service; results update supplier status.
- ML pipelines ingest order/inventory data for forecasting and segmentation.

---

## 7. Component Design

### 7.1 Web Application (Laravel)

- **Authentication/Authorization**: Laravel Breeze/Jetstream; role-based middleware
- **Product \& BOM Management**: CRUD for products, multi-level BOM editor, lifecycle tracker
- **Supplier \& Vendor Management**: Onboarding forms, status dashboards, performance analytics
- **Inventory Management**: Real-time stock tracking, alerts for low/overstock, location mapping
- **Order Processing**: Multi-type order flows, status updates, shipment integration
- **Workforce Management**: Scheduling UI, assignment tracking, shift analytics
- **Chat/Messaging**: Vue.js component, real-time via Supabase or Pusher
- **Analytics Dashboards**: Customizable widgets, exportable reports, KPI visualizations
- **Reporting Engine**: Scheduled jobs (Laravel Scheduler), PDF/email delivery


### 7.2 Java Vendor Validation Server

- **REST API**: Endpoint for PDF submission from Laravel
- **PDF Parsing**: Apache PDFBox for extracting application data
- **Business Rules Engine**: Configurable checks for financials, compliance, reputation
- **Audit Scheduling**: Integration with calendar/notification system
- **Result Callback**: Secure webhook to update main platform


### 7.3 Machine Learning Microservices (Python)

- **Demand Forecasting API**: Receives historical sales, returns predictions
- **Customer Segmentation API**: Clusters customer data, returns segment assignments
- **Supplier Risk Analysis API**: (future) Ingests supplier metrics, returns risk score
- **Data Sync**: Scheduled ETL jobs, secure API endpoints for data exchange

---

## 8. Interface Design

### 8.1 User Interfaces

- **Admin Dashboard**: Global KPIs, alerts, quick links to core modules
- **Product Manager**: Product/BOM editor, lifecycle tracker, assembly status
- **Supplier Portal**: Application submission, status tracking, performance feedback
- **Inventory Manager**: Stock views, transfer requests, reorder triggers
- **Order Fulfillment**: Order lists, shipment tracking, customer comms
- **Workforce Scheduler**: Calendar, assignment matrix, shift analytics
- **Chat/Messaging**: Role-based conversations, notifications
- **Reporting**: Downloadable/exportable reports, scheduling UI


### 8.2 API Interfaces

- **RESTful APIs**: For all core resources (products, suppliers, inventory, orders, events)
- **Webhook Endpoints**: For ML predictions, vendor validation results
- **External Integrations**: Email/SMS gateways, 3PL APIs (future)

---

## 9. Deployment \& Operations

- **Environments**: Dev, staging, production (Dockerized for consistency)
- **CI/CD**: Automated tests, linting, build, and deployment via GitHub Actions
- **Monitoring**: Application logs, error tracking (Sentry), performance metrics
- **Backups**: Automated MySQL and file storage backups, tested recovery

---

## 10. Security

- **Authentication**: OAuth2/JWT for API access, strong password policies
- **Authorization**: Role-based, least-privilege principle
- **Data Protection**: Encryption at rest (MySQL, file storage), HTTPS everywhere
- **Audit Logging**: User actions, data changes, access logs
- **Vendor Data**: Secure storage and processing of sensitive vendor applications

---

## 11. Extensibility \& Future Roadmap

- **Product Lines**: Modular support for new device categories (e.g., smartwatches, IoT)
- **ML Expansion**: Integrate anomaly detection, logistics optimization, supplier risk scoring
- **Marketplace Integration**: Support for external sales channels and 3PLs
- **Mobile App**: For field staff, warehouse scanning, and on-the-go approvals
- **API Marketplace**: Open APIs for partners and ecosystem expansion

---

## 12. Appendix

- **Sample Data Sources**: Kaggle, DataWorld, MIT SCALE, etc. (see prior dataset recommendations)
- **Reference Workflows**: Based on industry best practices (Nothing, Fairphone, OnePlus)
- **Glossary**: Definitions of all key SCM and product management terms

---

**End of Document**

<div style="text-align: center">⁂</div>

[^1]: summary.md

[^2]: sdd_template.txt

[^3]: summary.md

[^4]: https://www.cleveroad.com/blog/supply-chain-management-system/

[^5]: https://paceai.co/sample-software-design-document-guide-template/

[^6]: https://www.cms.gov/files/document/sddpdf

[^7]: https://throughput.world/blog/vendor-managed-inventory-vmi/

[^8]: https://www.remedi.com/blog/implementing-event-driven-integration-in-logistics

[^9]: https://www.gooddata.com/blog/supply-chain-dashboard-examples/

[^10]: https://infomineo.com/blog/best-supply-chain-management-software-10-tools-for-2025/

[^11]: https://supplyx.info/en/scm-system-platforms/

[^12]: https://www.infor.com/en-nl/solutions/scm

[^13]: https://mobisoftinfotech.com/resources/blog/mobile-communication-connecting-supply-chains-on-the-go

[^14]: http://www.aasmr.org/liss/Vol.11/No.5/Vol.11.No.5.03.pdf

[^15]: https://business.adobe.com/blog/basics/vendor-managed-inventory-benefits-risks-best-practices

[^16]: https://supplychaindigital.com/digital-supply-chain/top-10-supply-chain-management-platforms

[^17]: https://techvify-software.com/supply-chain-software-development/

[^18]: https://www.netsuite.com/portal/resource/articles/inventory-management/vendor-managed-inventory.shtml

[^19]: https://www.10xsheets.com/blog/scm-supply-chain-management-software/

[^20]: https://multi-techno.com/supply-chain-management/

[^21]: https://vlinkinfo.com/blog/top-supply-chain-management-platforms/

[^22]: https://www.wur.nl/en/article/software-architecture-design-for-supply-chain-management-systems.htm

[^23]: https://www.coupa.com/products/supply-chain-design/

[^24]: https://inventorsoft.co/blog/supply-chain-management-software-requirements

[^25]: https://www.sap.com/resources/what-is-vendor-managed-inventory-vmi

[^26]: https://www.advantive.com/solutions/vmi-software/

[^27]: https://www.supplychainbrain.com/blogs/1-think-tank/post/37451-how-multi-enterprise-platforms-optimize-supply-chain-management

[^28]: https://www.comarch.com/trade-and-services/data-management/news/what-is-supply-chain-management/

[^29]: https://www.ijert.org/supply-chain-management-analysis-in-online-retailers-and-logistics

[^30]: https://www.youtube.com/watch?v=XHu-nDn0SMI

[^31]: https://www.techtarget.com/searcherp/definition/vendor-managed-inventory-VMI

[^32]: https://www.frontiersin.org/journals/blockchain/articles/10.3389/fbloc.2022.846783/full

[^33]: https://www.counterpointresearch.com/report/global-smart-devices-odm-industry-whitepaper-2022-chinese-simplified

