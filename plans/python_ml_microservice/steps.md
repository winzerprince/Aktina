# Python ML Microservice Implementation Plan

## Overview
Implement customer segmentation and sales prediction microservice using demographics-based clustering with sales volume categorization for Aktina SCM.

## Phase 1: Database Analysis & Data Preparation

### Step 1.1: Understand Data Structure
- Analyze retailers table demographics fields:
  - `male_female_ratio` (decimal)
  - `city` (string) 
  - `urban_rural_classification` (enum: urban, suburban, rural)
  - `customer_age_class` (enum: child, teenager, youth, adult, senior)
  - `customer_income_bracket` (enum: low, medium, high)
  - `customer_education_level` (enum: low, mid, high)
- Analyze orders table for sales data (seller_id with company_name 'Aktina')
- Map retailer sales performance to volume categories (high, medium, low)

### Step 1.2: Create Data Repository
- Create `MLDataRepository` for data extraction
- Methods for retailer demographics
- Methods for Aktina sales data 
- Sales volume categorization logic
## Phase 2: Python ML Microservice

### Step 2.1: Setup Python Environment
- Create FastAPI application in `microservices/python-ml/`
- Install dependencies: pandas, scikit-learn, fastapi, uvicorn
- Create basic project structure

### Step 2.2: Customer Segmentation Model
- Demographics preprocessing (encoding categorical variables)
- KMeans clustering implementation based on demographics only
- Sales volume categorization (high, medium, low) as target variable
- Model training and persistence
- API endpoint `/segment-customers`

### Step 2.3: Sales Prediction Model
- Time series analysis for Aktina sales (orders where seller company_name = 'Aktina')
- Prophet model for forecasting
- API endpoint `/predict-sales`
- Model persistence with joblib

## Phase 3: Laravel Integration

### Step 3.1: Service Layer
- Create `MLService` for microservice communication
- Customer segmentation methods
- Sales prediction methods
- Error handling and caching

### Step 3.2: Repository Layer  
- Create `MLRepository` for data preparation
- Retailer demographics extraction
- Sales data aggregation for Aktina
- Sales volume categorization logic (based on total sales per retailer)

### Step 3.3: Livewire Components
- `CustomerSegmentation` component for admin predictions view
- `SalesPredicton` component for admin predictions view
- ApexCharts integration
- Real-time data updates

## Phase 4: Admin View Enhancement

### Step 4.1: Update Admin Predictions View Only
- Add customer segmentation section to existing trends-and-predictions.blade.php
- Add sales prediction section
- Charts and visualizations
- Keep existing layout and add new ML sections

### Step 4.2: Charts and Visualizations
- Customer segment distribution (pie chart)
- Sales prediction timeline (line chart)
- Segment performance comparison (bar chart)
- Demographics analysis charts

## Implementation Priorities
1. Basic functionality first (data extraction → model → simple prediction)
2. Admin predictions view integration only
3. Chart visualizations
4. Caching and error handling

## Technical Requirements
- Follow Laravel Service-Repository pattern
- Use existing admin predictions route: admin.trends-and-predictions
- KISS principle - minimal complexity
- Cache expensive ML operations (24-hour TTL)
- Handle microservice failures gracefully
- Only add to admin predictions view, no new routes needed
- System handles data updates gracefully with minimal performance impact
- All components follow Laravel best practices and KISS principles
