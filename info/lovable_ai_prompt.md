# Lovable AI Prompt: Aktina Supply Chain Management System UI Design

## Project Overview
Create a comprehensive, modern web-based UI for **Aktina Supply Chain Management System** - a sophisticated supply chain platform for a technology assembly company specializing in consumer electronics. The system manages the complete supply chain lifecycle from raw material procurement to retail distribution.

## Design Philosophy & Visual Identity

### Color Scheme (Primary)
- **Primary Green**: `#32CD32` (Lime Green) - Main brand color for headers, primary buttons
- **Dark Green**: `#006400` (Dark Green) - Navigation bars, secondary elements
- **Forest Green**: `#228B22` - Accent colors, success states
- **White**: `#FFFFFF` - Background, cards, text areas
- **Off-White**: `#F8F9FA` - Secondary backgrounds

### Accent Colors (Secondary)
- **Ocean Blue**: `#1E90FF` - Information elements, links
- **Royal Purple**: `#6A5ACD` - Analytics charts, premium features
- **Crimson Red**: `#DC143C` - Alerts, warnings, critical actions
- **Amber**: `#FFA500` - Pending states, warnings
- **Slate Gray**: `#708090` - Disabled states, borders

### Dark Mode Support
- **Dark Background**: `#1A1A1A`
- **Dark Cards**: `#2D2D2D`
- **Dark Text**: `#E0E0E0`
- **Dark Green Primary**: `#00FF00` (Bright Lime)
- **Dark Accents**: Brighter versions of accent colors

## Typography & Spacing
- **Font Family**: Inter, Roboto, or similar modern sans-serif
- **Heading Scale**: H1(32px), H2(24px), H3(20px), H4(16px)
- **Body Text**: 14px regular, 16px for readability areas
- **Spacing System**: 8px base unit (8px, 16px, 24px, 32px, 48px)
- **Border Radius**: 8px for cards, 4px for inputs, 16px for buttons

## Animation Requirements

### Micro-Interactions
- **Button Hover**: Smooth color transition (200ms), slight scale (1.02x)
- **Card Hover**: Subtle lift effect with shadow (150ms ease-out)
- **Input Focus**: Border color animation with glow effect
- **Toggle States**: Smooth sliding animations for switches/toggles

### Number Animations
- **Counter Animation**: Numbers should count up from 0 to target value over 1.5s
- **Chart Animations**: Progressive reveal of chart data (800ms stagger)
- **Progress Bars**: Fill animation with percentage counter
- **Dashboard Metrics**: Fade-in with slide-up effect (300ms delay between items)

### Page Transitions
- **Tab Switching**: Slide animation between tabs (250ms ease-in-out)
- **Modal Appearance**: Scale-in from center with backdrop fade
- **Notification Toasts**: Slide-in from top-right with auto-dismiss

## User Roles & Dashboard Designs

### 1. SUPPLIER DASHBOARD

#### Navigation Structure
```
Header: [Aktina Logo] [Notifications Bell] [Profile Avatar] [Dark Mode Toggle]
Tabs: [Home] [Orders] [Profile Settings]
```

#### Tab 0: Home Dashboard
**Layout**: 3-column grid with metrics cards
- **Order Statistics Widget** (Top Row)
  - Total Orders This Month: Animated counter
  - Pending Orders: Red badge if > 5
  - Completed Deliveries: Green progress bar
  - On-Time Delivery %: Circular progress indicator
- **Recent Orders Table** (Middle)
  - Columns: Order #, Product, Quantity, Status, Due Date
  - Status badges: Pending (amber), Accepted (blue), Shipped (green)
  - Quick action buttons: View Details, Update Status
- **Performance Metrics Chart** (Bottom)
  - Line chart showing monthly delivery performance
  - Toggle between Revenue, Orders, Performance metrics

#### Tab 1: Orders Management
**Layout**: Split view - sidebar filters + main content
- **Filter Sidebar**:
  - Status filter checkboxes
  - Date range picker
  - Product category filter
  - Search bar with autocomplete
- **Orders List**:
  - Card-based layout for each order
  - **Card Elements**: Order #, Customer info, Product list, Total value, Status timeline
  - **Action Buttons**: Accept Order (green), Decline (red), Request Modification (blue)
  - **Status Timeline**: Visual progress indicator showing: Received → Accepted → In Production → Shipped → Delivered

#### Tab 2: Profile Settings
**Layout**: Tabbed interface within the main tab
- **Company Information Tab**:
  - Company logo upload area (drag & drop)
  - Form fields: Company name, address, phone, email
  - Business details: Industry, years established, certifications
- **Security Tab**:
  - Password change form
  - Two-factor authentication toggle
  - Login history table
- **Notifications Tab**:
  - Toggle switches for email notifications
  - SMS alert preferences
  - Notification frequency settings

### 2. PRODUCTION MANAGER DASHBOARD

#### Navigation Structure
```
Header: [Aktina Logo] [Quick Actions Dropdown] [Notifications] [Profile] [Dark Mode]
Tabs: [Home] [Orders] [Inventory] [Production] [Analytics] [AI Chat] [Sales]
```

#### Tab 0: Home Dashboard
**Layout**: 4-column responsive grid
- **Production Overview** (Top Row - Full Width)
  - Real-time production line status indicators
  - Today's production targets vs actual (progress bars)
  - Active production lines count with status icons
- **Key Metrics Cards** (Second Row)
  - Overall Equipment Effectiveness (OEE) - Gauge chart
  - Inventory Alerts - Red notification badges
  - Quality Score - Star rating display
  - Daily Output - Animated counter
- **Live Production Feed** (Bottom Half)
  - Real-time updates of production line activities
  - Recent completions, quality checks, line changes

#### Tab 1: Orders Management
**Layout**: Dual-pane view
- **Left Pane**: Incoming Orders (from Wholesalers)
  - Order cards with priority indicators
  - Drag-and-drop to production queue
  - Batch processing capabilities
- **Right Pane**: Outgoing Orders (to Suppliers)
  - Material requirement calculations
  - Supplier selection interface
  - Automated reorder suggestions

#### Tab 2: Inventory Control
**Layout**: Dashboard with multiple widgets
- **Stock Level Grid** (Main View)
  - Product cards with current stock levels
  - Color-coded inventory status (green: good, amber: low, red: critical)
  - Quick reorder buttons
- **Inventory Movements Timeline**
  - Visual timeline of recent stock changes
  - Filterable by product, location, type
- **Reorder Management Panel**
  - Automated reorder suggestions
  - Pending orders status
  - Supplier lead time indicators

#### Tab 3: Production Planning
**Layout**: Kanban-style board
- **Production Lines Status Board**
  - Cards for each production line
  - Real-time status indicators
  - Capacity utilization meters
- **Scheduling Interface**
  - Drag-and-drop production scheduler
  - Calendar view with time slots
  - Conflict detection and resolution
- **Bill of Materials Manager**
  - Hierarchical BOM view
  - Version control interface
  - Cost calculation tools

#### Tab 4: Analytics
**Layout**: Dashboard with customizable widgets
- **Production Efficiency Charts**
  - Line charts for OEE trends
  - Heat maps for performance by time/line
  - Comparative analysis tools
- **Quality Metrics Dashboard**
  - Defect rate trends
  - First-pass yield charts
  - Quality control checkpoint results
- **Cost Analysis Tools**
  - Production cost breakdowns
  - Efficiency improvement opportunities
  - ROI calculators

#### Tab 5: AI Chat
**Layout**: Chat interface with analytics sidebar
- **Main Chat Area**
  - Conversational interface with AI assistant
  - Quick action buttons for common queries
  - File upload for data analysis
- **Insights Sidebar**
  - Real-time AI recommendations
  - Anomaly detection alerts
  - Predictive insights cards

#### Tab 6: Sales Tracking
**Layout**: Revenue-focused dashboard
- **Sales Performance Metrics**
  - Revenue trend charts
  - Product performance rankings
  - Customer analysis tools
- **Financial Analytics**
  - Profit margin analysis
  - Cost center performance
  - Budget vs actual tracking

### 3. HR MANAGER DASHBOARD

#### Navigation Structure
```
Header: [Aktina Logo] [Quick Actions] [Notifications] [Profile] [Dark Mode]
Tabs: [Home] [Workforce] [Distribution] [Analytics] [Predictions] [AI Assistant]
```

#### Tab 0: Home Dashboard
**Layout**: Workforce-centric layout
- **Workforce Overview** (Top Banner)
  - Total staff count with department breakdown
  - Available workers indicator
  - Shift coverage status
- **Productivity Metrics** (Cards Grid)
  - Average productivity score per team
  - Attendance rates
  - Training completion rates
- **Staffing Alerts** (Notification Panel)
  - Understaffed locations
  - Skill gap alerts
  - Performance concerns

#### Tab 1: Workforce Management
**Layout**: Master-detail view
- **Staff Directory** (Left Panel)
  - Searchable/filterable employee list
  - Employee cards with photos and key info
  - Quick contact options
- **Employee Details** (Right Panel)
  - Detailed employee profile
  - Skills matrix visualization
  - Performance history charts
  - Task assignment interface

#### Tab 2: Distribution Management
**Layout**: Geographic and organizational view
- **Supply Center Map**
  - Interactive map showing all locations
  - Staffing level indicators on map pins
  - Resource allocation visualizations
- **Distribution Optimization Tools**
  - Drag-and-drop staff allocation
  - Shift planning interface
  - Transportation logistics coordination

#### Tab 3: Analytics
**Layout**: HR-specific analytics dashboard
- **Performance Analytics**
  - Team productivity trends
  - Individual performance rankings
  - Training effectiveness measures
- **Capacity Planning Tools**
  - Workforce demand forecasting
  - Seasonal staffing analysis
  - Cost optimization metrics

#### Tab 4: Predictions
**Layout**: AI-powered insights
- **Workforce Forecasting**
  - Demand prediction charts
  - Skill requirement projections
  - Turnover risk assessments
- **Optimization Recommendations**
  - AI-suggested staff reallocations
  - Training program recommendations
  - Performance improvement strategies

#### Tab 5: AI Assistant
**Layout**: Specialized HR chat interface
- **HR-focused Chat**
  - Workforce optimization queries
  - Policy questions and guidance
  - Employee data analysis requests
- **HR Insights Panel**
  - Real-time workforce analytics
  - Compliance monitoring
  - Employee satisfaction metrics

### 4. SYSTEM ADMINISTRATOR DASHBOARD

#### Navigation Structure
```
Header: [Aktina Logo] [System Status] [Notifications] [Profile] [Dark Mode]
Tabs: [Home] [Economics] [Performance] [Predictions] [AI Assistant] [Communication] [User Management]
```

#### Tab 0: Executive Home Dashboard
**Layout**: Executive summary with KPI focus
- **Company-Wide KPIs** (Top Row)
  - Revenue dashboard with trend indicators
  - Operational efficiency metrics
  - System health indicators
- **Financial Overview** (Cards)
  - Monthly revenue progress
  - Cost center performance
  - Profit margin trends
- **Critical Alerts** (Priority Panel)
  - System issues requiring attention
  - Business process bottlenecks
  - Compliance notifications

#### Tab 1: Economic Analytics
**Layout**: Financial intelligence dashboard
- **Revenue Analytics**
  - Multi-dimensional revenue charts
  - Sales trend analysis
  - Profit margin breakdowns by segment
- **Cost Analysis Tools**
  - Operational expense tracking
  - Supply chain cost optimization
  - ROI calculation tools
- **Budget Management**
  - Budget vs actual analysis
  - Variance reporting
  - Cost control metrics

#### Tab 2: Performance Analytics
**Layout**: Operational metrics dashboard
- **System Performance**
  - Platform uptime monitoring
  - Response time analytics
  - User activity metrics
- **Process Efficiency**
  - End-to-end process cycle times
  - Automation benefits tracking
  - Bottleneck identification
- **Cross-Functional Analysis**
  - Departmental performance comparison
  - Integrated workflow efficiency
  - Resource utilization metrics

#### Tab 3: Predictions & Strategic AI
**Layout**: Strategic intelligence interface
- **Market Intelligence**
  - Industry trend analysis
  - Competitive positioning
  - Market opportunity identification
- **Predictive Analytics**
  - Revenue forecasting
  - Risk assessment tools
  - Growth projection models
- **Strategic Recommendations**
  - AI-generated strategic insights
  - Investment opportunity analysis
  - Expansion planning tools

#### Tab 4: AI Assistant
**Layout**: Executive-level AI interface
- **Strategic Chat**
  - Executive-level queries and analysis
  - Complex data interpretation
  - Strategic planning support
- **Executive Insights**
  - Real-time business intelligence
  - Market analysis
  - Competitive intelligence

#### Tab 5: Communication Hub
**Layout**: System-wide communication center
- **Inter-Role Messaging**
  - Role-based communication channels
  - System announcements
  - Broadcast messaging tools
- **Communication Analytics**
  - Message volume metrics
  - Response time analysis
  - Communication effectiveness

#### Tab 6: User Management
**Layout**: Administrative interface
- **User Administration**
  - User account management
  - Role assignment interface
  - Permission management tools
- **Access Control**
  - Security policy management
  - Access audit trails
  - Compliance monitoring

### 5. WHOLESALER DASHBOARD

#### Navigation Structure
```
Header: [Aktina Logo] [Notifications] [Profile] [Dark Mode]
Tabs: [Home] [Incoming Orders] [Outgoing Orders] [Retailers] [Analytics] [Communication] [AI Assistant]
```

#### Tab 0: Distribution Home
**Layout**: Distribution-focused overview
- **Distribution Overview** (Banner)
  - Order pipeline status
  - Fulfillment metrics
  - Inventory turnover rates
- **Retailer Performance** (Cards)
  - Top performing retailers
  - Territory performance maps
  - Growth opportunity indicators
- **Business Metrics** (Charts)
  - Revenue trends
  - Order volume patterns
  - Seasonal analysis

#### Tab 1: Incoming Orders (from Retailers)
**Layout**: Order processing interface
- **Order Queue**
  - New orders requiring attention
  - Order cards with retailer info and details
  - Batch processing capabilities
- **Order Processing Tools**
  - Inventory availability checker
  - Shipping calculator
  - Delivery scheduling interface

#### Tab 2: Outgoing Orders (to Aktina)
**Layout**: Procurement management
- **Demand Consolidation**
  - Retailer demand aggregation
  - Order optimization tools
  - Supplier coordination interface
- **Order Status Tracking**
  - Active orders with Aktina
  - Delivery timeline tracking
  - Communication threads

#### Tab 3: Retailer Management
**Layout**: Relationship management interface
- **Retailer Directory**
  - Comprehensive retailer database
  - Performance metrics per retailer
  - Territory mapping tools
- **Onboarding Interface**
  - New retailer application processing
  - Verification workflow
  - Contract management

#### Tab 4: Analytics
**Layout**: Business intelligence dashboard
- **Sales Analytics**
  - Revenue trend analysis
  - Product performance metrics
  - Territory analysis tools
- **Retailer Analytics**
  - Performance comparisons
  - Growth potential assessment
  - Market penetration analysis

#### Tab 5: Communication
**Layout**: Business communication center
- **Aktina Liaison Channel**
  - Direct communication with Aktina
  - Order-related discussions
  - Business development conversations
- **Retailer Support**
  - Multi-retailer communication
  - Support ticket management
  - Announcement broadcasting

#### Tab 6: AI Assistant
**Layout**: Business optimization AI
- **Market Insights**
  - Market trend analysis
  - Competitive intelligence
  - Opportunity identification
- **Optimization Recommendations**
  - Inventory optimization
  - Pricing strategies
  - Territory expansion advice

### 6. RETAILER DASHBOARD

#### Navigation Structure
```
Header: [Aktina Logo] [Notifications] [Profile] [Dark Mode]
Tabs: [Home] [Orders] [Customer Feedback] [Market Insights] [AI Assistant]
```

#### Tab 0: Retail Home
**Layout**: Retail-focused metrics
- **Retail Dashboard** (Overview)
  - Daily sales summary
  - Inventory status indicators
  - Customer satisfaction scores
- **Sales Performance** (Charts)
  - Revenue trends
  - Product performance rankings
  - Customer analytics
- **Operational Metrics** (Cards)
  - Transaction volumes
  - Average order values
  - Customer retention rates

#### Tab 1: Orders Management
**Layout**: Simple order interface
- **Order Placement**
  - Product catalog browser
  - Shopping cart interface
  - Order history review
- **Shipment Tracking**
  - Active order status
  - Delivery timeline tracking
  - Receipt confirmation tools

#### Tab 2: Customer Feedback
**Layout**: Customer experience center
- **Feedback Collection**
  - Customer rating interface
  - Review management system
  - Feedback analytics dashboard
- **Product Reviews**
  - Review aggregation tools
  - Sentiment analysis
  - Response management

#### Tab 3: Market Insights
**Layout**: Retail intelligence
- **Consumer Trends**
  - Market trend analysis
  - Seasonal pattern identification
  - Demand forecasting
- **Competitive Analysis**
  - Price comparison tools
  - Market positioning analysis
  - Opportunity identification

#### Tab 4: AI Assistant
**Layout**: Retail optimization AI
- **Sales Optimization**
  - Inventory recommendations
  - Pricing strategies
  - Customer targeting advice
- **Market Analysis**
  - Consumer behavior insights
  - Trend predictions
  - Growth opportunities

## Global UI Components

### Navigation Components
- **Header Bar**: Fixed header with role-appropriate quick actions
- **Tab Navigation**: Consistent numbered tab system across all roles
- **Breadcrumbs**: Context-aware navigation path
- **Search Bar**: Global search with role-based filtering

### Data Visualization Components
- **Chart Library**: Line charts, bar charts, pie charts, heat maps, gauge charts
- **Real-time Metrics**: Live updating numbers with animation
- **Progress Indicators**: Progress bars, circular progress, step indicators
- **Status Badges**: Color-coded status indicators with tooltips

### Form Components
- **Input Fields**: Consistent styling with validation states
- **Dropdown Menus**: Searchable select components
- **Date/Time Pickers**: Range selection capabilities
- **File Upload**: Drag-and-drop with progress indicators

### Interactive Elements
- **Buttons**: Primary, secondary, tertiary with loading states
- **Modals**: Consistent modal design with backdrop
- **Tooltips**: Contextual help and information
- **Notifications**: Toast messages with auto-dismiss

## Responsive Design Requirements

### Breakpoints
- **Desktop**: 1200px+ (Primary design target)
- **Tablet**: 768px - 1199px (Simplified layouts)
- **Mobile**: 320px - 767px (Essential functions only)

### Mobile Adaptations
- **Collapsible Navigation**: Hamburger menu for mobile
- **Touch-Friendly**: Minimum 44px touch targets
- **Simplified Layouts**: Priority content on smaller screens
- **Swipe Gestures**: Tab switching and card navigation

## Accessibility Requirements
- **WCAG 2.1 AA Compliance**: Color contrast, keyboard navigation
- **Screen Reader Support**: Proper ARIA labels and semantic HTML
- **Keyboard Navigation**: Full functionality without mouse
- **Focus Management**: Clear focus indicators and logical tab order

## Performance Specifications
- **Load Time**: Initial page load under 2 seconds
- **Animation Performance**: 60fps for all animations
- **Data Loading**: Progressive loading with skeleton screens
- **Image Optimization**: WebP format with fallbacks

## Technical Implementation Notes

### Frontend Framework
- **Recommended**: React with TypeScript or Vue.js 3
- **State Management**: Redux/Zustand or Pinia
- **UI Library**: Tailwind CSS or Material-UI with custom theming
- **Charts**: Chart.js, D3.js, or Recharts

### Real-time Features
- **WebSocket Integration**: For live updates and notifications
- **Real-time Dashboards**: Auto-refreshing metrics every 30 seconds
- **Live Chat**: Instant messaging capabilities
- **Push Notifications**: Browser notification support

### Data Integration
- **API Integration**: RESTful API with proper error handling
- **Data Caching**: Intelligent caching for performance
- **Offline Support**: Basic offline functionality for critical features
- **Real-time Sync**: Conflict resolution for concurrent edits

## Special Features to Implement

### AI Integration
- **Chatbot Interface**: Conversational AI with natural language processing
- **Predictive Analytics**: Visual representation of AI predictions
- **Anomaly Detection**: Automatic highlighting of unusual patterns
- **Recommendation Engine**: Contextual suggestions throughout the interface

### Advanced Analytics
- **Interactive Charts**: Drill-down capabilities and data exploration
- **Custom Dashboards**: User-configurable widget placement
- **Export Capabilities**: PDF, Excel, CSV export options
- **Scheduled Reports**: Automated report generation and delivery

### Collaboration Features
- **Real-time Communication**: In-context messaging and comments
- **Activity Feeds**: Timeline of recent actions and updates
- **Notification Center**: Comprehensive notification management
- **Document Sharing**: File upload and sharing capabilities

This comprehensive UI design should create a sophisticated, user-friendly, and highly functional supply chain management interface that meets the needs of all six user roles while maintaining consistency and modern design standards throughout the application. Ensure that you use creative and clean design.
