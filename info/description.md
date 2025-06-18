# Aktina Supply Chain Management System
## Project Description

### Overview
The Aktina Supply Chain Management (SCM) System is a comprehensive web-based platform designed to manage the complete supply chain lifecycle for Aktina, a technology assembly company specializing in consumer electronics (smartphones, tablets, and related devices). The system orchestrates operations from raw material procurement through final product delivery to retail outlets, incorporating machine learning capabilities for predictive analytics and optimization.

### Business Context
Aktina operates as a technology assembly company that:
- Sources electronic components and raw materials from various suppliers
- Assembles finished consumer technology products (primarily smartphones)
- Distributes products through a network of wholesalers to retail outlets
- Manages multiple supply centers for efficient distribution logistics

### Technical Architecture
The system employs a hybrid architecture combining:
- **Layered Architecture**: Structured separation of concerns across presentation, business logic, and data layers
- **Microservices Architecture**: Modular services for specific business domains
- **Event-Driven Architecture**: Asynchronous communication between system components
- **Object-Oriented Programming**: Applied throughout the codebase for maintainability and extensibility

### Technology Stack
- **Primary Platform**: Laravel (PHP) web application
- **AI/ML Services**: Python microservice for analytics and predictions
- **Vendor Validation**: Java server for automated vendor verification
- **Database**: MySQL for transactional data, Redis for caching
- **Communication**: RESTful APIs, WebSocket for real-time features

---

## Core Functional Requirements

### 1. Machine Learning and Analytics
**Demand Prediction**
- Analyze historical sales data to forecast future product demand
- Consider seasonal trends, market conditions, and external factors
- Provide demand forecasts at product, category, and regional levels

**Customer Segmentation**
- Analyze purchasing patterns to categorize customers into distinct segments
- Generate personalized recommendations for improved customer satisfaction
- Support targeted marketing and inventory allocation strategies

**Predictive Analytics**
- Stock-out risk assessment and early warning systems
- Production bottleneck identification and resolution recommendations
- Lead time optimization for supplier relationships

### 2. Supply Chain Operations
**Inventory Management**
- Real-time tracking of raw materials, work-in-progress, and finished goods
- Automated reorder point calculations and purchase order generation
- Multi-location inventory visibility across supply centers

**Order Processing**
- End-to-end order lifecycle management from placement to fulfillment
- Integration between suppliers, production, wholesalers, and retailers
- Order status tracking and exception handling

**Production Management**
- Production line scheduling and capacity planning
- Bill of Materials (BOM) management and versioning
- Quality control checkpoints and reporting

**Workforce Distribution**
- Strategic allocation of human resources across supply centers
- Skills-based task assignment and workload balancing
- Performance tracking and productivity analytics

### 3. Communication and Collaboration
**Multi-Channel Chat System**
- Role-based communication channels (Supplier-Company, Company-Wholesaler)
- AI-powered chatbot for common queries and support
- Secure messaging with conversation history and file sharing

**Automated Reporting**
- Stakeholder-specific reports delivered on scheduled intervals
- Customizable dashboards with role-appropriate metrics
- Export capabilities for external analysis and compliance

### 4. Vendor Validation System
**Automated Verification Process**
- Java-based microservice for processing vendor applications
- PDF document analysis and data extraction capabilities
- Multi-criteria evaluation including:
  - Financial stability assessment (bank statements, credit reports)
  - Operational reputation verification (years in business, certifications)
  - Regulatory compliance validation (licenses, permits, industry standards)

**Physical Verification Workflow**
- Automated scheduling of facility inspections for qualified applicants
- Integration with external verification services
- Approval workflow with multi-level authorization

---

## User Roles and Access Control

### 1. Supplier
**Primary Responsibilities**: Raw material and component provision
**System Access**:
- Dashboard with order statistics and delivery performance metrics
- Active orders management (view, accept, update status)
- Delivery scheduling and tracking
- Basic profile management and communication tools

**Key Features**:
- Order fulfillment workflow
- Inventory availability reporting
- Communication channel with Aktina procurement team

### 2. Production Manager
**Primary Responsibilities**: Manufacturing operations and inventory oversight
**System Access**:
- Comprehensive production dashboard with real-time metrics
- Incoming order management (from wholesalers)
- Outgoing order coordination (to suppliers)
- Inventory monitoring with low-stock alerts and reorder management
- Production line configuration and scheduling
- Bill of Materials creation, editing, and version control
- Analytics dashboard with production efficiency metrics
- AI-powered insights and recommendations
- Sales tracking and performance analysis

**Key Features**:
- Production capacity planning
- Quality control integration
- Supplier performance evaluation
- Inventory optimization recommendations

### 3. HR Manager
**Primary Responsibilities**: Workforce management and distribution optimization
**System Access**:
- Workforce analytics dashboard (total workers, availability, productivity)
- Supply center staffing management and optimization
- Worker assignment and task distribution
- Performance analytics and predictive modeling
- AI-powered workforce planning recommendations

**Key Features**:
- Skills-based worker allocation
- Training needs identification
- Performance tracking and evaluation
- Capacity planning for seasonal demands

### 4. System Administrator
**Primary Responsibilities**: Overall system oversight and strategic analytics
**System Access**:
- Executive dashboard with company-wide KPIs
- Financial performance tracking and revenue analytics
- Economic analysis and market intelligence
- Performance analytics across all business units
- AI-generated strategic insights and recommendations
- System-wide communication management
- User access control and security administration

**Key Features**:
- Cross-functional analytics and reporting
- Strategic planning support tools
- Risk assessment and mitigation recommendations
- Compliance monitoring and reporting

### 5. Wholesaler/Vendor
**Primary Responsibilities**: Bulk product distribution to retail network
**System Access**:
- Order management (incoming from retailers, outgoing to Aktina)
- Retailer relationship management (add, remove, track performance)
- Economic analysis and sales performance tracking
- Communication channels (Aktina liaison, AI support)
- Inventory planning and demand forecasting

**Key Features**:
- Multi-retailer order consolidation
- Territory management and expansion planning
- Performance-based retailer recommendations
- Market analysis and opportunity identification

### 6. Retailer
**Primary Responsibilities**: Final product sales to end consumers
**System Access**:
- Retail performance dashboard
- Customer feedback collection and product rating system
- Order placement and tracking (from wholesalers)
- AI-powered sales insights and recommendations
- Market trend analysis and consumer behavior insights

**Key Features**:
- Consumer feedback integration
- Sales optimization recommendations
- Inventory turnover analysis
- Market demand sensing

---

## System Integration and Workflow

### Inter-Role Communication Matrix
| From/To | Supplier | Production | HR | Admin | Wholesaler | Retailer |
|---------|----------|------------|----|----|------------|----------|
| Supplier | - | Orders/Delivery | - | Reports | - | - |
| Production | Purchase Orders | - | Capacity Needs | Operations Data | Order Fulfillment | - |
| HR | - | Workforce Allocation | - | Performance Reports | - | - |
| Admin | Strategy | Directives | Policy | - | Business Intelligence | - |
| Wholesaler | - | Product Orders | - | Sales Data | - | Distribution |
| Retailer | - | - | - | Market Feedback | Orders/Feedback | - |

### Event-Driven Workflows
**Order Processing Flow**:
1. Retailer places order → Wholesaler notification
2. Wholesaler consolidates orders → Production order request
3. Production assesses capacity → HR workforce allocation
4. Production identifies material needs → Supplier purchase orders
5. Supplier confirms delivery → Production scheduling update
6. Production completion → Wholesaler shipment notification
7. Delivery confirmation → All stakeholders updated

**Vendor Validation Flow**:
1. Vendor application submission (PDF documents)
2. Java service automated validation (financial, reputation, compliance)
3. Scoring and preliminary approval/rejection
4. Facility inspection scheduling for approved candidates
5. Final verification and system onboarding
6. Integration into supplier/wholesaler network

---

## User Interface Structure and Navigation

### General UI Layout
All users access the system through a unified dashboard interface with the following structure:

**Core UI Components (All Roles):**
- **Header Bar**: Company logo, user name, notifications icon, profile settings dropdown
- **Navigation Tabs**: Role-specific numbered tabs (detailed below)
- **Main Content Area**: Dynamic content based on selected tab
- **Footer**: System status, help links, version information

**Universal Elements:**
- **Tab 0: Home/Dashboard** - Always the first tab, showing role-specific overview and KPIs
- **Notifications Icon** (🔔) - Real-time alerts, system messages, workflow updates
- **Profile Settings Icon** (⚙️) - Account settings, password reset, preferences, logout

---

## Detailed Role-Based Tab Structure

### 1. Supplier
**Tab Navigation:**
- **Tab 0: Home** - Order statistics, delivery performance, recent activity
- **Tab 1: Active Orders** - Incoming orders from Aktina, order acceptance/confirmation workflow
- **Tab 2: Order History** - Completed and cancelled orders, delivery records
- **Tab 3: Communication** - Direct chat with Aktina procurement team
- **Profile Settings** - Company details, contact information, payment details

**Order Workflow Clarification:**
- Suppliers do NOT manage inventory through this system
- Orders placed by Aktina appear in "Active Orders" tab with status progression:
  1. **Pending** - New order received, awaiting supplier acceptance
  2. **Accepted** - Supplier has confirmed order and delivery timeline
  3. **In Production** - Supplier is preparing/manufacturing items
  4. **Shipped** - Items dispatched to Aktina
  5. **Delivered** - Confirmed received by Aktina (receiver confirms on their end)
- Time tracking: System records duration between order placement and delivery confirmation
- No transportation progress tracking - only status milestones (Kanban-style)

### 2. Production Manager
**Tab Navigation:**
- **Tab 0: Home** - Production KPIs, inventory alerts, urgent notifications
- **Tab 1: Orders** - Incoming orders (wholesalers), outgoing orders (suppliers), order management
- **Tab 2: Inventory** - Stock levels, reorder management, location tracking
- **Tab 3: Production Lines** - Current production status, scheduling, capacity planning
- **Tab 4: Bill of Materials** - BOM creation, editing, version control
- **Tab 5: Analytics** - Production efficiency metrics, performance dashboards
- **Tab 6: Sales Tracking** - Sales data, revenue analysis, product performance
- **Tab 7: AI Assistant** - Machine learning insights, production optimization recommendations

**Order Confirmation Process:**
- All orders require confirmation at the receiving end
- Orders remain "In Transit" status until receiver confirms delivery
- System tracks order lifecycle timing for performance analytics

### 3. HR Manager
**Tab Navigation:**
- **Tab 0: Home** - Workforce overview, availability statistics, alerts
- **Tab 1: Workforce Distribution** - Staff allocation across supply centers, shift management
- **Tab 2: Worker Management** - Individual worker profiles, skills tracking, assignments
- **Tab 3: Analytics** - Productivity metrics, performance trends
- **Tab 4: Predictions** - AI-powered workforce planning, demand forecasting
- **Tab 5: AI Assistant** - Workforce optimization recommendations, scheduling suggestions

**Workforce Synchronization:**
- Workforce planning integrates with production schedules
- Real-time visibility of worker availability and assignments
- Automatic alerts for staffing shortages or overallocation

### 4. System Administrator
**Tab Navigation:**
- **Tab 0: Home** - System overview, financial summary, critical alerts
- **Tab 1: Economic Analytics** - Revenue analysis, cost tracking, profitability metrics
- **Tab 2: Performance Analytics** - System performance, user activity, operational efficiency
- **Tab 3: Predictions & AI** - Strategic insights, market forecasting, system recommendations
- **Tab 4: AI Assistant** - Executive-level analytics and strategic planning support
- **Tab 5: Communication Hub** - Inter-role messaging, system announcements
- **Tab 6: User Management** - Account creation, role assignment, access control

### 5. Wholesaler/Vendor
**Tab Navigation:**
- **Tab 0: Home** - Business overview, order summary, performance metrics
- **Tab 1: Incoming Orders** - Orders from retailers, order acceptance and processing
- **Tab 2: Outgoing Orders** - Orders to Aktina, order status tracking
- **Tab 3: Retailer Management** - Add/remove retailers, performance tracking, territory management
- **Tab 4: Economic Analysis** - Sales performance, profit margins, market trends
- **Tab 5: Communication** - Chat with Aktina (restricted to business liaison only)
- **Tab 6: AI Assistant** - Market insights, inventory recommendations, sales optimization

**Order Linking and Restrictions:**
- All orders are linked to specific vendors/wholesalers
- Only verified vendors can initiate orders to Aktina
- Order confirmation required at both ends of the transaction

### 6. Retailer
**Tab Navigation:**
- **Tab 0: Home** - Retail dashboard, sales summary, inventory status
- **Tab 1: Orders** - Place orders to wholesalers, track incoming shipments
- **Tab 2: Customer Feedback** - Product ratings, customer reviews, feedback forms
- **Tab 3: Market Insights** - Consumer trends, sales analytics
- **Tab 4: AI Assistant** - Sales optimization, inventory recommendations, market analysis

---

## Access-Based Communication System

### Communication Restrictions:
- **Suppliers ↔ Aktina Company**: Direct communication channel for order-related queries
- **Wholesalers ↔ Aktina Company**: Business liaison communication for orders and logistics
- **Retailers ↔ Wholesalers**: Order placement and customer service communication
- **Internal Aktina Teams**: Full internal communication across all departments
- **Cross-Vendor Communication**: NOT PERMITTED (competitors cannot communicate directly)

### AI Assistant Access:
- All roles have access to AI assistant with role-appropriate insights
- AI responses are tailored to user role and permissions
- Common queries handled automatically to reduce support burden

---

## Order Confirmation and Status Tracking

### Order Status Progression:
1. **Placed** - Order initiated by requesting party
2. **Pending** - Awaiting acceptance by fulfilling party
3. **Accepted** - Order confirmed with delivery timeline
4. **In Production/Processing** - Items being prepared
5. **Shipped** - Items in transit
6. **Delivered** - Confirmed received by requesting party (receiver must confirm)

### Key Workflow Rules:
- Orders are only marked as "Delivered" when the receiver confirms receipt
- Time tracking measures total cycle time from placement to delivery confirmation
- Exception handling for delayed or disputed deliveries
- Automatic escalation for orders exceeding promised delivery dates

### Performance Metrics:
- Order fulfillment time (placement to delivery confirmation)
- Supplier reliability scores based on on-time delivery
- Quality metrics based on receiver feedback
- Cost efficiency tracking across all order types

---

## Quality Attributes and Non-Functional Requirements

### Performance
- Response time: < 2 seconds for standard operations
- Concurrent user support: 1000+ simultaneous users
- Data processing: Real-time analytics with < 5-minute latency

### Security
- Role-based access control with granular permissions
- Multi-factor authentication for sensitive operations
- End-to-end encryption for communications
- Audit logging for all system transactions

### Scalability
- Horizontal scaling capability for microservices
- Database partitioning for large data volumes
- Load balancing for high-availability operations

### Reliability
- 99.9% uptime requirement
- Automated backup and disaster recovery
- Graceful degradation during component failures

---

## Success Metrics

### Operational Efficiency
- 20% reduction in inventory carrying costs
- 15% improvement in order fulfillment time
- 25% increase in demand forecast accuracy

### User Adoption
- 95% user adoption rate across all roles
- < 2 hours average training time per user
- 90% user satisfaction rating

### Business Impact
- 10% reduction in supply chain operational costs
- 15% improvement in customer satisfaction scores
- 30% faster vendor onboarding process

---

*This project represents a comprehensive digital transformation initiative for Aktina's supply chain operations, leveraging modern technologies and architectural patterns to create a competitive advantage in the technology assembly industry.*