# Manual Training Guide for ML Models

This guide explains how to manually train the machine learning models using CSV or Excel files.

## Overview

The Python microservice provides two endpoints that allow you to upload files to train the models:

1. `/upload-train-segmentation` - For customer segmentation based on demographics and sales volume
2. `/upload-train-forecast` - For sales prediction/forecasting

## Manual Training with CSV/Excel Files

### Customer Segmentation Training

#### File Requirements

Create a CSV or Excel file with the following columns:

- **Required columns:**
  - `id`: Unique identifier for each retailer (numeric)
  - `total_sales`: Total sales amount for the retailer (numeric)

- **Optional demographic columns:**
  - `male_female_ratio`: Gender ratio of customers (numeric)
  - `city`: City name (string)
  - `urban_rural_classification`: urban, suburban, or rural (string)
  - `customer_age_class`: customer age category (string)
  - `customer_income_bracket`: low, medium, high (string)
  - `customer_education_level`: low, mid, high (string)
  - `business_type`: Type of business (string)
  - `annual_revenue`: Annual revenue amount (string)
  - `employee_count`: Number of employees (string)
  - `years_in_business`: How long the business has existed (numeric)

#### Example CSV Format

```csv
id,male_female_ratio,city,urban_rural_classification,customer_age_class,customer_income_bracket,customer_education_level,business_type,annual_revenue,employee_count,years_in_business,total_sales
1,0.8,New York,urban,adult,high,high,Electronics,500000,50,10,15000
2,1.2,Chicago,suburban,youth,medium,mid,Clothing,200000,15,5,8000
3,0.9,Rural Town,rural,adult,low,low,Grocery,50000,3,2,3000
```

#### How to Train

1. **Using cURL:**

   ```bash
   curl -X POST "http://localhost:8000/upload-train-segmentation" \
     -F "file=@retailers_data.csv" \
     -F "num_clusters=3"
   ```

2. **Using a REST client (like Postman):**
   - URL: `http://localhost:8000/upload-train-segmentation`
   - Method: POST
   - Form data:
     - file: [your CSV or Excel file]
     - num_clusters: 3 (or your preferred number of clusters)

### Sales Forecast Training

#### File Requirements

Create a CSV or Excel file with the following columns:

- **Required columns:**
  - `date`: Date of sales (YYYY-MM-DD format recommended)
  - `amount`: Sales amount (numeric)

#### Example CSV Format

```csv
date,amount
2025-01-01,5000
2025-01-02,5200
2025-01-03,5400
2025-01-04,5600
2025-01-05,5800
```

#### How to Train

1. **Using cURL:**

   ```bash
   curl -X POST "http://localhost:8000/upload-train-forecast" \
     -F "file=@sales_data.csv" \
     -F "horizon_days=90"
   ```

2. **Using a REST client (like Postman):**
   - URL: `http://localhost:8000/upload-train-forecast`
   - Method: POST
   - Form data:
     - file: [your CSV or Excel file]
     - horizon_days: 90 (or your preferred forecast horizon)

## Best Practices

1. **Data Quality:**
   - Ensure data is clean and consistent
   - Avoid missing values in required fields
   - Check for proper formatting (especially dates)

2. **Segmentation Guidelines:**
   - Include as many demographic fields as possible for better clustering
   - Aim for at least 20+ retailers for meaningful segments
   - Choose an appropriate number of clusters (2-5 usually works well)

3. **Forecasting Guidelines:**
   - Provide at least 2 weeks of daily data for reliable forecasts
   - Ensure dates are continuous and sorted
   - Include seasonal patterns if available (e.g., full year of data)

## Troubleshooting

If you encounter errors:

1. **Check file format:**
   - Ensure it's a valid CSV or Excel file
   - Verify column names match exactly (case-sensitive)

2. **Check data types:**
   - Numeric columns should contain numbers only
   - Date columns should be in a standard format

3. **API Response:**
   - The API will return detailed error messages if there are issues with your file

## Model Storage

Trained models are automatically saved and will be used for future predictions until manually retrained.

## Integration with Laravel

After manually training the models, the Laravel application will automatically use the updated models when accessing the customer segmentation or sales prediction functionality through the MLService.
