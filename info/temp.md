Based on the Aktina Supply Chain Management System documentation, here are the suitable analytics for each role's analytics pages:

## 1. Production Manager Analytics

### Core Production Metrics
- **Production Efficiency**: Overall Equipment Effectiveness (OEE), line utilization rates, throughput metrics
- **Capacity Analysis**: Current vs. planned production capacity, bottleneck identification, resource utilization
- **Quality Metrics**: Defect rates by production line, first-pass yield, quality control checkpoint results
- **Lead Time Analysis**: Production cycle times, setup times, changeover efficiency

### Inventory Analytics
- **Stock Level Optimization**: Turnover rates, dead stock identification, optimal reorder points
- **Demand vs. Supply**: Forecast accuracy, stockout incidents, safety stock adequacy
- **Cost Analysis**: Inventory carrying costs, obsolescence costs, storage efficiency

### Supplier Performance
- **Delivery Performance**: On-time delivery rates, lead time variance, quality ratings
- **Cost Trends**: Price variance analysis, total cost of ownership, supplier reliability scores

### Predictive Analytics
- **Production Forecasting**: Expected output based on current orders and capacity
- **Maintenance Predictions**: Equipment failure probability, optimal maintenance scheduling
- **Resource Planning**: Workforce requirements, material needs forecasting

---

## 2. HR Manager Analytics

### Workforce Distribution
- **Staffing Levels**: Current vs. optimal staffing by location and shift
- **Skills Analysis**: Skills gap identification, training needs assessment
- **Allocation Efficiency**: Worker utilization rates, cross-training effectiveness

### Performance Metrics
- **Productivity Tracking**: Output per worker, efficiency trends by team
- **Absenteeism Patterns**: Attendance rates, seasonal variations, impact analysis
- **Performance Ratings**: Individual and team performance trends

### Capacity Planning
- **Demand Forecasting**: Workforce requirements based on production schedules
- **Seasonal Analysis**: Staffing needs during peak and low seasons
- **Cost Optimization**: Labor cost per unit, overtime analysis, temp worker usage

### Predictive Analytics
- **Turnover Prediction**: Employee retention likelihood, recruitment planning
- **Training ROI**: Skill development impact on productivity
- **Workload Balancing**: Optimal task distribution across teams

---

## 3. System Administrator Analytics

### Financial Performance
- **Revenue Analytics**: Sales trends, profit margins, cost center performance
- **Cost Analysis**: Operational expenses, supply chain costs, ROI calculations
- **Budget vs. Actual**: Variance analysis, spending patterns, cost control metrics

### Operational Excellence
- **System Performance**: Platform uptime, response times, user activity metrics
- **Process Efficiency**: End-to-end process cycle times, automation benefits
- **Risk Assessment**: Supply chain disruption risks, financial exposure analysis

### Strategic Intelligence
- **Market Trends**: Industry benchmarking, competitive positioning
- **Customer Analytics**: Satisfaction scores, retention rates, market share
- **Growth Metrics**: Expansion opportunities, scalability indicators

### Cross-Functional Analytics
- **Integrated Dashboards**: Company-wide KPIs, departmental performance comparisons
- **Compliance Monitoring**: Regulatory adherence, audit trail analysis
- **Innovation Metrics**: R&D investment returns, time-to-market improvements

---

## 4. Wholesaler/Vendor Analytics

### Sales Performance
- **Revenue Trends**: Sales volume, seasonal patterns, growth rates
- **Product Performance**: Best/worst selling items, category analysis
- **Market Share**: Territory performance, competitive positioning

### Customer Analytics
- **Retailer Performance**: Sales by retailer, payment patterns, growth potential
- **Geographic Analysis**: Regional sales trends, market penetration
- **Customer Segmentation**: Retailer categorization, tailored strategies

### Inventory Management
- **Turnover Analysis**: Product rotation rates, slow-moving inventory
- **Demand Patterns**: Seasonal trends, promotional impact
- **Optimization Opportunities**: Stock level recommendations, reorder strategies

### Financial Analytics
- **Profitability Analysis**: Margin analysis by product/retailer
- **Cash Flow**: Payment cycles, collection efficiency
- **Cost Management**: Distribution costs, operational efficiency

---

## 5. Retailer Analytics

### Sales Performance
- **Daily/Weekly/Monthly Sales**: Revenue trends, transaction volume
- **Product Performance**: Top sellers, underperforming items, category analysis
- **Customer Insights**: Purchase patterns, loyalty metrics, demographic analysis

### Inventory Analytics
- **Stock Optimization**: Turnover rates, shelf life management
- **Demand Forecasting**: Seasonal trends, promotional planning
- **Replenishment Analytics**: Optimal order quantities, reorder timing

### Market Intelligence
- **Competitive Analysis**: Price comparisons, market positioning
- **Consumer Trends**: Preference shifts, emerging demands
- **Promotional Effectiveness**: Campaign ROI, customer response rates

### Customer Experience
- **Satisfaction Metrics**: Product ratings, review sentiment analysis
- **Service Quality**: Response times, issue resolution rates
- **Loyalty Programs**: Engagement rates, retention metrics

---

## 6. Supplier Analytics

### Order Performance
- **Fulfillment Metrics**: On-time delivery rates, order accuracy
- **Capacity Utilization**: Production efficiency, resource optimization
- **Quality Performance**: Defect rates, customer satisfaction scores

### Financial Analytics
- **Revenue Tracking**: Sales volume, payment cycles
- **Cost Management**: Production costs, efficiency improvements
- **Profitability**: Margin analysis, cost optimization opportunities

### Relationship Management
- **Customer Performance**: Aktina order patterns, growth trends
- **Service Levels**: Response times, issue resolution
- **Compliance Metrics**: Regulatory adherence, certification status

---

## Shared Analytics Components

### AI-Powered Insights (Available to All Roles)
- **Anomaly Detection**: Unusual patterns requiring attention
- **Predictive Recommendations**: AI-suggested actions based on data trends
- **Optimization Opportunities**: Efficiency improvements, cost reductions
- **Risk Alerts**: Early warning systems for potential issues

### Real-Time Dashboards
- **KPI Monitoring**: Role-specific key performance indicators
- **Trend Analysis**: Historical data visualization, pattern recognition
- **Comparative Analysis**: Benchmarking against targets and historical performance
- **Drill-Down Capabilities**: Detailed analysis from summary metrics

### Customizable Reports
- **Scheduled Reports**: Automated delivery of relevant metrics
- **Ad-Hoc Analysis**: Custom report generation capabilities
- **Export Functions**: Data export for external analysis
- **Collaboration Tools**: Report sharing and annotation features

Each role's analytics page should be customizable, allowing users to prioritize the metrics most relevant to their responsibilities while maintaining access to comprehensive data for deeper analysis when needed.

Here are the narrowed down AI/ML predictions for the Aktina Supply Chain Management System:

## Core ML Predictions & Required Parameters

### 1. Demand Forecasting Model

**What We Predict:**
- Future product demand by SKU, region, and time period
- Seasonal demand patterns and trend changes
- Impact of external factors (holidays, promotions, economic indicators)

**Required Parameters to Track:**
```
Historical Data (12-24 months minimum):
- daily_sales_volume
- product_category
- sku_identifier
- geographical_region
- seasonal_indicators (month, quarter, holidays)
- promotional_activities (discounts, campaigns)
- price_changes
- competitor_pricing
- economic_indicators (GDP, consumer_confidence)
- weather_data (for seasonal products)
- marketing_spend
- inventory_levels
- stockout_incidents
- customer_demographics
```

**ML Algorithm:** Time Series Forecasting (ARIMA, LSTM, Prophet)
```pseudocode
FUNCTION predictDemand(product_id, forecast_period):
    historical_data = getHistoricalSales(product_id, 24_months)
    external_factors = getExternalFactors(forecast_period)
    seasonal_patterns = extractSeasonality(historical_data)
    
    model = trainTimeSeriesModel(historical_data, external_factors)
    prediction = model.predict(forecast_period)
    confidence_interval = calculateConfidenceInterval(prediction)
    
    RETURN prediction, confidence_interval
END FUNCTION
```

---

### 2. Customer Segmentation Model

**What We Predict:**
- Customer behavior patterns and preferences
- Purchase probability for specific products
- Customer lifetime value
- Churn risk assessment

**Required Parameters to Track:**
```
Customer Behavior Data:
- purchase_frequency
- average_order_value
- product_categories_purchased
- seasonal_purchase_patterns
- payment_methods_preferred
- delivery_preferences
- customer_tenure
- geographic_location
- demographic_data (age, income_bracket)
- customer_feedback_scores
- return_rates
- support_interaction_frequency
- channel_preferences (online, retail)
- promotional_response_rates
```

**ML Algorithm:** K-Means Clustering, RFM Analysis
```pseudocode
FUNCTION segmentCustomers(customer_data):
    features = extractRFMFeatures(customer_data)
    // Recency, Frequency, Monetary features
    
    normalized_features = standardizeFeatures(features)
    optimal_clusters = determineOptimalClusters(normalized_features)
    
    segmentation_model = KMeansCluster(n_clusters=optimal_clusters)
    customer_segments = segmentation_model.fit_predict(normalized_features)
    
    RETURN customer_segments, segment_characteristics
END FUNCTION
```

---

### 3. Inventory Optimization Model

**What We Predict:**
- Optimal stock levels for each product
- Reorder points and quantities
- Risk of stockouts or overstock
- Inventory carrying cost optimization

**Required Parameters to Track:**
```
Inventory Management Data:
- current_stock_levels
- lead_times (by supplier, product)
- demand_variability
- holding_costs
- ordering_costs
- stockout_costs
- supplier_reliability_scores
- product_shelf_life
- storage_capacity_constraints
- seasonal_demand_patterns
- promotion_schedules
- supplier_minimum_order_quantities
- transportation_costs
- quality_rejection_rates
```

**ML Algorithm:** Reinforcement Learning, Regression Models
```pseudocode
FUNCTION optimizeInventory(product_id):
    demand_forecast = getDemandForecast(product_id)
    lead_time_data = getSupplierLeadTimes(product_id)
    cost_parameters = getCostStructure(product_id)
    
    safety_stock = calculateSafetyStock(demand_variability, lead_time_variability)
    reorder_point = (average_demand * lead_time) + safety_stock
    optimal_order_quantity = calculateEOQ(demand_forecast, cost_parameters)
    
    RETURN reorder_point, optimal_order_quantity, safety_stock
END FUNCTION
```

---

### 4. Supplier Performance Prediction

**What We Predict:**
- Supplier delivery reliability
- Quality performance trends
- Risk of supplier failures
- Optimal supplier selection

**Required Parameters to Track:**
```
Supplier Performance Data:
- delivery_timeliness (percentage on-time)
- quality_scores (defect rates)
- order_fulfillment_accuracy
- response_time_to_queries
- price_competitiveness
- capacity_utilization
- financial_stability_indicators
- compliance_scores
- geographical_risk_factors
- communication_effectiveness
- flexibility_ratings
- innovation_capability
- sustainability_scores
- historical_performance_trends
```

**ML Algorithm:** Classification Models, Neural Networks
```pseudocode
FUNCTION predictSupplierPerformance(supplier_id, evaluation_period):
    historical_performance = getSupplierHistory(supplier_id)
    external_risk_factors = getMarketRiskFactors(supplier_id)
    financial_indicators = getFinancialHealth(supplier_id)
    
    feature_vector = combineFeatures(historical_performance, external_risk_factors, financial_indicators)
    performance_score = performance_model.predict(feature_vector)
    risk_assessment = calculateRiskScore(performance_score)
    
    RETURN performance_score, risk_assessment, recommendations
END FUNCTION
```

---

## ML Model Training Requirements

### Data Quality Standards
```
Minimum Data Requirements:
- 12-24 months historical data for time series models
- 1000+ data points per category for classification
- Regular data validation and cleaning
- Real-time data pipeline integration
- Data versioning and lineage tracking
```

### Model Performance Monitoring
```
Key Metrics to Track:
- prediction_accuracy (MAPE, RMSE)
- model_drift_detection
- feature_importance_changes
- prediction_confidence_intervals
- business_impact_metrics
- model_refresh_frequency
- computational_performance
```

### Continuous Learning Implementation
```pseudocode
FUNCTION continuousModelImprovement():
    WHILE system_is_running:
        new_data = collectRecentData()
        current_performance = evaluateModelPerformance()
        
        IF performance_degradation_detected(current_performance):
            retrain_model(new_data)
            validate_improved_performance()
            deploy_update

Based on the AI and analytics requirements I described earlier, you would need to add the following database entities to support the four core ML models (Demand Forecasting, Customer Segmentation, Inventory Optimization, and Supplier Performance Prediction):

## Additional Database Entities for AI/Analytics

### 1. ML Model Management Entities

**Table: MLModels**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| model_id | VARCHAR | 36 | Primary Key, Not Null | Unique model identifier (UUID) |
| model_name | VARCHAR | 100 | Not Null | Human-readable model name |
| model_type | ENUM | - | Not Null | Values: 'demand_forecasting', 'customer_segmentation', 'inventory_optimization', 'supplier_performance' |
| version | VARCHAR | 20 | Not Null | Model version (e.g., 'v1.2.3') |
| algorithm_type | VARCHAR | 50 | Not Null | Algorithm used (e.g., 'LSTM', 'K-Means', 'Random Forest') |
| status | ENUM | - | Not Null | Values: 'training', 'active', 'deprecated', 'failed' |
| accuracy_score | DECIMAL | 5,3 | Null | Model accuracy percentage |
| created_at | TIMESTAMP | - | Not Null | Model creation timestamp |
| last_trained | TIMESTAMP | - | Null | Last training timestamp |
| parameters | JSON | - | Null | Model hyperparameters and configuration |

**Table: ModelPredictions**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| prediction_id | VARCHAR | 36 | Primary Key, Not Null | Unique prediction identifier |
| model_id | VARCHAR | 36 | Foreign Key, Not Null | References MLModels table |
| entity_type | VARCHAR | 50 | Not Null | Type of entity being predicted (product, customer, supplier) |
| entity_id | VARCHAR | 36 | Not Null | ID of the entity being predicted |
| prediction_value | JSON | - | Not Null | Prediction results in structured format |
| confidence_score | DECIMAL | 5,3 | Null | Prediction confidence (0-1) |
| prediction_date | TIMESTAMP | - | Not Null | When prediction was made |
| actual_value | JSON | - | Null | Actual observed value (for accuracy measurement) |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

### 2. Sales and Transaction Data

**Table: SalesTransactions**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| transaction_id | VARCHAR | 36 | Primary Key, Not Null | Unique transaction identifier |
| order_id | VARCHAR | 36 | Foreign Key, Not Null | References Orders table |
| product_id | VARCHAR | 36 | Foreign Key, Not Null | References Products table |
| customer_id | VARCHAR | 36 | Foreign Key, Not Null | References Users/Companies table |
| quantity_sold | INT | - | Not Null | Quantity sold in transaction |
| unit_price | DECIMAL | 10,2 | Not Null | Price per unit at time of sale |
| total_amount | DECIMAL | 12,2 | Not Null | Total transaction amount |
| discount_applied | DECIMAL | 10,2 | Default 0.00 | Discount amount applied |
| profit_margin | DECIMAL | 5,2 | Null | Profit margin percentage |
| sales_channel | VARCHAR | 50 | Not Null | Channel: 'wholesale', 'retail', 'direct' |
| geography | VARCHAR | 100 | Not Null | Geographic region of sale |
| season | VARCHAR | 20 | Not Null | Season when sale occurred |
| promotion_id | VARCHAR | 36 | Foreign Key, Null | Any promotion applied |
| transaction_date | TIMESTAMP | - | Not Null | Date/time of transaction |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

**Table: DemandHistory**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| demand_record_id | VARCHAR | 36 | Primary Key, Not Null | Unique demand record identifier |
| product_id | VARCHAR | 36 | Foreign Key, Not Null | References Products table |
| location_id | VARCHAR | 36 | Foreign Key, Not Null | References Locations table |
| period_start | DATE | - | Not Null | Start of demand period |
| period_end | DATE | - | Not Null | End of demand period |
| actual_demand | INT | - | Not Null | Actual demand quantity |
| forecasted_demand | INT | - | Null | Previously forecasted demand |
| variance | DECIMAL | 10,2 | Null | Difference between actual and forecast |
| external_factors | JSON | - | Null | Weather, holidays, promotions, etc. |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

### 3. Customer Analytics Data

**Table: CustomerBehaviorMetrics**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| behavior_id | VARCHAR | 36 | Primary Key, Not Null | Unique behavior record identifier |
| customer_id | VARCHAR | 36 | Foreign Key, Not Null | References Users/Companies table |
| analysis_period | VARCHAR | 20 | Not Null | Period analyzed (e.g., 'Q1_2024', 'monthly') |
| total_orders | INT | - | Not Null | Total orders in period |
| total_spend | DECIMAL | 12,2 | Not Null | Total amount spent |
| average_order_value | DECIMAL | 10,2 | Not Null | Average order value |
| purchase_frequency | DECIMAL | 5,2 | Not Null | Average days between purchases |
| preferred_categories | JSON | - | Null | Product categories with purchase counts |
| seasonal_patterns | JSON | - | Null | Seasonal buying behavior |
| payment_behavior | JSON | - | Null | Payment method preferences and timing |
| loyalty_score | DECIMAL | 5,2 | Null | Calculated customer loyalty score |
| churn_risk | DECIMAL | 5,3 | Null | Probability of customer churn |
| lifetime_value | DECIMAL | 12,2 | Null | Calculated customer lifetime value |
| segment_id | VARCHAR | 36 | Foreign Key, Null | References CustomerSegments table |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

**Table: CustomerSegments**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| segment_id | VARCHAR | 36 | Primary Key, Not Null | Unique segment identifier |
| segment_name | VARCHAR | 100 | Not Null | Descriptive segment name |
| segment_description | TEXT | - | Null | Detailed segment characteristics |
| criteria | JSON | - | Not Null | Segmentation criteria and thresholds |
| customer_count | INT | - | Not Null | Number of customers in segment |
| average_clv | DECIMAL | 12,2 | Null | Average customer lifetime value |
| segment_value | DECIMAL | 14,2 | Null | Total value of segment |
| growth_rate | DECIMAL | 5,2 | Null | Segment growth rate percentage |
| created_at | TIMESTAMP | - | Not Null | Segment creation timestamp |
| updated_at | TIMESTAMP | - | Not Null | Last update timestamp |

### 4. Supplier Performance Analytics

**Table: SupplierPerformanceMetrics**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| performance_id | VARCHAR | 36 | Primary Key, Not Null | Unique performance record identifier |
| supplier_id | VARCHAR | 36 | Foreign Key, Not Null | References Suppliers table |
| evaluation_period | VARCHAR | 20 | Not Null | Period evaluated (e.g., 'Q1_2024') |
| total_orders | INT | - | Not Null | Total orders in period |
| on_time_deliveries | INT | - | Not Null | Number of on-time deliveries |
| on_time_percentage | DECIMAL | 5,2 | Not Null | On-time delivery percentage |
| quality_score | DECIMAL | 5,2 | Not Null | Average quality rating |
| defect_rate | DECIMAL | 5,3 | Not Null | Defect rate percentage |
| average_lead_time | DECIMAL | 5,1 | Not Null | Average lead time in days |
| lead_time_variance | DECIMAL | 5,2 | Not Null | Lead time consistency score |
| communication_score | DECIMAL | 5,2 | Null | Communication effectiveness rating |
| cost_competitiveness | DECIMAL | 5,2 | Null | Price competitiveness score |
| capacity_utilization | DECIMAL | 5,2 | Null | Supplier capacity utilization |
| innovation_score | DECIMAL | 5,2 | Null | Innovation and improvement score |
| overall_rating | DECIMAL | 5,2 | Not Null | Composite performance rating |
| risk_score | DECIMAL | 5,3 | Null | Calculated risk score |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

### 5. Feature Store for ML

**Table: FeatureStore**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| feature_id | VARCHAR | 36 | Primary Key, Not Null | Unique feature identifier |
| entity_type | VARCHAR | 50 | Not Null | Type: 'product', 'customer', 'supplier', 'order' |
| entity_id | VARCHAR | 36 | Not Null | ID of the entity |
| feature_name | VARCHAR | 100 | Not Null | Name of the feature |
| feature_value | DECIMAL | 15,5 | Null | Numeric feature value |
| feature_text | TEXT | - | Null | Text feature value |
| feature_json | JSON | - | Null | Complex feature data |
| feature_date | DATE | - | Not Null | Date the feature represents |
| created_at | TIMESTAMP | - | Not Null | Feature creation timestamp |
| model_version | VARCHAR | 20 | Null | Model version that uses this feature |

### 6. External Data Integration

**Table: ExternalDataSources**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| source_id | VARCHAR | 36 | Primary Key, Not Null | Unique source identifier |
| source_name | VARCHAR | 100 | Not Null | Name of external data source |
| source_type | VARCHAR | 50 | Not Null | Type: 'economic', 'weather', 'market', 'industry' |
| api_endpoint | VARCHAR | 255 | Null | API endpoint URL |
| refresh_frequency | VARCHAR | 20 | Not Null | How often data is updated |
| last_updated | TIMESTAMP | - | Null | Last successful data pull |
| status | ENUM | - | Not Null | Values: 'active', 'inactive', 'error' |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

**Table: ExternalDataPoints**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| datapoint_id | VARCHAR | 36 | Primary Key, Not Null | Unique datapoint identifier |
| source_id | VARCHAR | 36 | Foreign Key, Not Null | References ExternalDataSources |
| data_date | DATE | - | Not Null | Date the data represents |
| metric_name | VARCHAR | 100 | Not Null | Name of the metric |
| metric_value | DECIMAL | 15,5 | Not Null | Value of the metric |
| geographic_scope | VARCHAR | 100 | Null | Geographic area (country, region) |
| created_at | TIMESTAMP | - | Not Null | Record creation timestamp |

### 7. Analytics Cache and Aggregations

**Table: AnalyticsCache**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| cache_id | VARCHAR | 36 | Primary Key, Not Null | Unique cache identifier |
| report_type | VARCHAR | 100 | Not Null | Type of cached report |
| entity_id | VARCHAR | 36 | Null | Entity the cache is for (if applicable) |
| user_role | VARCHAR | 20 | Null | User role the cache is for |
| time_period | VARCHAR | 50 | Not Null | Time period of the data |
| cache_data | JSON | - | Not Null | Cached analytics data |
| expires_at | TIMESTAMP | - | Not Null | Cache expiration time |
| created_at | TIMESTAMP | - | Not Null | Cache creation timestamp |

### 8. Model Training Data

**Table: TrainingDatasets**
| Field Name | Data Type | Size | Constraint | Description |
|------------|-----------|------|------------|-------------|
| dataset_id | VARCHAR | 36 | Primary Key, Not Null | Unique dataset identifier |
| model_type | VARCHAR | 50 | Not Null | Type of ML model |
| dataset_name | VARCHAR | 100 | Not Null | Descriptive name |
| data_source_query | TEXT | - | Not Null | SQL query to generate dataset |
| feature_columns | JSON | - | Not Null | List of feature columns |
| target_column | VARCHAR | 100 | Null | Target variable column |
| training_period_start | DATE | - | Not Null | Start date of training data |
| training_period_end | DATE | - | Not Null | End date of training data |
| record_count | INT | - | Not Null | Number of records in dataset |
| created_at | TIMESTAMP | - | Not Null | Dataset creation timestamp |

## Indexes for Performance

```sql
-- Indexes for ML and Analytics tables
CREATE INDEX idx_sales_product_date ON SalesTransactions(product_id, transaction_date);
CREATE INDEX idx_sales_customer_date ON SalesTransactions(customer_id, transaction_date);
CREATE INDEX idx_demand_product_period ON DemandHistory(product_id, period_start, period_end);
CREATE INDEX idx_behavior_customer_period ON CustomerBehaviorMetrics(customer_id, analysis_period);
CREATE INDEX idx_supplier_perf_period ON SupplierPerformanceMetrics(supplier_id, evaluation_period);
CREATE INDEX idx_features_entity_date ON FeatureStore(entity_type, entity_id, feature_date);
CREATE INDEX idx_predictions_model_date ON ModelPredictions(model_id, prediction_date);
CREATE INDEX idx_external_source_date ON ExternalDataPoints(source_id, data_date);
```

These additional entities will provide the necessary data foundation for:

1. **Training ML models** with historical data
2. **Storing and versioning model predictions**
3. **Tracking model performance** over time
4. **Caching analytics results** for performance
5. **Managing feature engineering** pipelines
6. **Integrating external data sources** for enhanced predictions
7. **Supporting real-time analytics** dashboards
8. **Maintaining audit trails** for ML operations

The structure supports both batch processing for model training and real-time processing for live predictions and analytics.