# Training Data for ML Models

This directory contains sample training data for the Aktina ML microservice models.

## Files

### Customer Segmentation Training Data

1. **`customer_segmentation_training.csv`** - Comprehensive dataset with 50 retailers
   - Includes realistic demographic and business data
   - Covers various business types, locations, and sizes
   - Sales data ranges from $4,500 to $65,000
   - Includes urban, suburban, and rural classifications

2. **`test_segmentation.csv`** - Small test dataset with 10 retailers
   - Quick testing and validation
   - Diverse business types and demographics

### Sales Forecasting Training Data

1. **`sales_forecasting_training.csv`** - Comprehensive yearly dataset
   - 372 days of sales data (2024-2025)
   - Realistic seasonal patterns and growth trends
   - Sales range from $37,000 to $256,000
   - Includes weekly and monthly patterns

2. **`test_forecast.csv`** - Small test dataset
   - 30 days of sales data
   - Quick testing and validation

## Data Characteristics

### Customer Segmentation Features

- **Demographics**: male_female_ratio, age_class, income_bracket, education_level
- **Location**: city, urban_rural_classification
- **Business**: business_type, annual_revenue, employee_count, years_in_business
- **Performance**: total_sales (target for volume categorization)

### Sales Forecasting Features

- **Temporal**: Daily sales data with seasonal patterns
- **Trends**: Growth trends, weekly patterns, seasonal variations
- **Realistic**: Based on typical B2B sales patterns

## Usage

### Quick Test
```bash
# Run the test script to validate models
./test_training.sh
```

### Manual Training via API

#### Customer Segmentation
```bash
curl -X POST "http://localhost:8000/upload-train-segmentation" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@training_data/customer_segmentation_training.csv" \
  -F "num_clusters=5"
```

#### Sales Forecasting
```bash
curl -X POST "http://localhost:8000/upload-train-forecast" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@training_data/sales_forecasting_training.csv" \
  -F "horizon_days=30"
```

## Expected Results

### Customer Segmentation
- 5 distinct customer segments based on demographics and sales volume
- Segments typically include:
  - High-value urban tech companies
  - Medium-sized suburban businesses
  - Small rural retailers
  - Large enterprises
  - New/growing businesses

### Sales Forecasting
- 30-day forecast with confidence intervals
- Seasonal patterns recognition
- Growth trend projection
- Realistic forecast bounds

## Model Files

After training, models are saved in the `models/` directory:
- `customer_segments_model.joblib` - Customer segmentation model
- `sales_forecast_model.joblib` - Sales forecasting model

## Notes

- Training data includes realistic business scenarios
- Sales patterns reflect typical B2B seasonal trends
- Demographics cover diverse market segments
- Data is designed to produce meaningful clustering results
- Forecasting data includes both growth and seasonal patterns
