from fastapi import FastAPI, HTTPException, UploadFile, File, Form
from pydantic import BaseModel
from typing import List, Dict, Any, Optional
import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
import prophet
from prophet import Prophet
import joblib
import os
import logging
from datetime import datetime, timedelta
import io

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    handlers=[logging.StreamHandler()]
)

logger = logging.getLogger("aktina-ml")

# Create model directory if it doesn't exist
os.makedirs("models", exist_ok=True)

app = FastAPI(
    title="Aktina ML Microservice",
    description="Machine learning microservice for customer segmentation and sales prediction",
    version="1.0.0"
)

# Define data models
class RetailerData(BaseModel):
    id: int
    male_female_ratio: Optional[float]
    city: Optional[str]
    urban_rural_classification: Optional[str]
    customer_age_class: Optional[str]
    customer_income_bracket: Optional[str]
    customer_education_level: Optional[str]
    business_type: Optional[str]
    annual_revenue: Optional[str]
    employee_count: Optional[str]
    years_in_business: Optional[int]
    total_sales: float  # This will be added by the Laravel service

class SalesData(BaseModel):
    date: str
    amount: float

class RetailerList(BaseModel):
    retailers: List[RetailerData]

class SalesTimeSeriesData(BaseModel):
    sales: List[SalesData]
    horizon_days: int = 90

class SegmentationResponse(BaseModel):
    retailer_segments: Dict[int, int]
    segment_descriptions: Dict[str, str]
    segment_distribution: Dict[str, int]

class SalesForecastResponse(BaseModel):
    forecast_dates: List[str]
    forecast_values: List[float]
    forecast_lower_bound: List[float]
    forecast_upper_bound: List[float]

# Helper functions
def preprocess_retailer_data(retailers_df):
    """Preprocess retailer data for clustering"""
    # Handle missing values
    retailers_df['male_female_ratio'].fillna(1.0, inplace=True)
    retailers_df['urban_rural_classification'].fillna('unknown', inplace=True)
    retailers_df['customer_age_class'].fillna('unknown', inplace=True)
    retailers_df['customer_income_bracket'].fillna('unknown', inplace=True)
    retailers_df['customer_education_level'].fillna('unknown', inplace=True)
    retailers_df['business_type'].fillna('unknown', inplace=True)
    retailers_df['annual_revenue'].fillna('0', inplace=True)
    retailers_df['employee_count'].fillna('0', inplace=True)
    retailers_df['years_in_business'].fillna(0, inplace=True)

    # Process annual revenue to numeric values
    def parse_revenue(rev):
        if pd.isna(rev) or rev == 'unknown':
            return 0
        try:
            # Remove non-numeric characters and convert to float
            return float(rev.replace('$', '').replace(',', '').strip())
        except:
            return 0

    retailers_df['annual_revenue_numeric'] = retailers_df['annual_revenue'].apply(parse_revenue)

    # Process employee count to numeric values
    def parse_employees(emp):
        if pd.isna(emp) or emp == 'unknown':
            return 0
        try:
            # Get the first number in ranges like "10-20" or just the number
            return float(emp.split('-')[0].strip())
        except:
            return 0

    retailers_df['employee_count_numeric'] = retailers_df['employee_count'].apply(parse_employees)

    return retailers_df

def categorize_sales_volume(sales):
    """Categorize sales volume into high, medium, low"""
    if sales > 10000:
        return 'high'
    elif sales > 5000:
        return 'medium'
    else:
        return 'low'

def get_segment_description(segment_id, feature_importances=None):
    """Generate descriptions for each segment based on analysis"""
    descriptions = {
        0: "High-value urban retailers with educated customers",
        1: "Medium-sized suburban businesses with mixed customer base",
        2: "Small rural retailers with lower customer education",
        3: "Large enterprises with high sales volumes",
        4: "New businesses with growth potential"
    }
    return descriptions.get(segment_id, f"Segment {segment_id}")

@app.get("/")
def read_root():
    return {"status": "active", "service": "Aktina ML Microservice"}

@app.get("/health")
def health_check():
    return {
        "status": "healthy",
        "timestamp": datetime.now().isoformat(),
        "version": "1.0.0"
    }

def segment_customers_logic(retailers_df, original_df=None, num_clusters=5):
    """
    Core logic for customer segmentation that can be reused by different endpoints
    """
    # Use original_df if provided, otherwise use the processed df
    if original_df is None:
        original_df = retailers_df

    # Add sales volume category
    retailers_df['sales_volume'] = retailers_df['total_sales'].apply(categorize_sales_volume)

    # Feature columns for clustering
    categorical_features = [
        'urban_rural_classification',
        'customer_age_class',
        'customer_income_bracket',
        'customer_education_level',
        'business_type',
        'sales_volume'
    ]

    numeric_features = [
        'male_female_ratio',
        'annual_revenue_numeric',
        'employee_count_numeric',
        'years_in_business'
    ]

    # Create preprocessing pipeline
    preprocessor = ColumnTransformer(
        transformers=[
            ('num', StandardScaler(), numeric_features),
            ('cat', OneHotEncoder(handle_unknown='ignore'), categorical_features)
        ]
    )

    # Create clustering pipeline
    pipeline = Pipeline([
        ('preprocessor', preprocessor),
        ('kmeans', KMeans(n_clusters=num_clusters, random_state=42))
    ])

    # Fit the model
    features_df = retailers_df[numeric_features + categorical_features]
    pipeline.fit(features_df)

    # Save the model
    joblib.dump(pipeline, 'models/customer_segments_model.joblib')

    # Predict clusters
    retailers_df['cluster'] = pipeline.predict(features_df)

    # Prepare response
    retailer_segments = dict(zip(original_df['id'].astype(int).tolist(), retailers_df['cluster'].tolist()))

    # Get segment descriptions and distribution
    segment_descriptions = {str(i): get_segment_description(i) for i in range(num_clusters)}
    segment_distribution = retailers_df['cluster'].value_counts().to_dict()
    segment_distribution = {str(k): v for k, v in segment_distribution.items()}

    return {
        "retailer_segments": retailer_segments,
        "segment_descriptions": segment_descriptions,
        "segment_distribution": segment_distribution
    }

@app.post("/segment-customers", response_model=SegmentationResponse)
def segment_customers(data: RetailerList):
    try:
        logger.info(f"Received segmentation request for {len(data.retailers)} retailers")

        # Convert to DataFrame
        retailers_df = pd.DataFrame([r.dict() for r in data.retailers])

        if retailers_df.empty:
            raise HTTPException(status_code=400, detail="No retailer data provided")

        # Preprocess data
        retailers_df = preprocess_retailer_data(retailers_df)

        # Use the extracted segmentation logic
        return segment_customers_logic(retailers_df)

    except Exception as e:
        logger.error(f"Error in segment_customers: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error processing retailer data: {str(e)}")

@app.post("/predict-sales", response_model=SalesForecastResponse)
def predict_sales(data: SalesTimeSeriesData):
    try:
        logger.info(f"Received sales prediction request with {len(data.sales)} data points")

        # Convert to DataFrame
        sales_df = pd.DataFrame([{"ds": s.date, "y": s.amount} for s in data.sales])

        if sales_df.empty:
            raise HTTPException(status_code=400, detail="No sales data provided")

        # Convert date strings to datetime
        sales_df['ds'] = pd.to_datetime(sales_df['ds'])

        # Sort by date
        sales_df = sales_df.sort_values('ds')

        # Create and fit Prophet model
        model = Prophet(
            yearly_seasonality=True,
            weekly_seasonality=True,
            daily_seasonality=False,
            seasonality_mode='multiplicative',
            uncertainty_samples=1000
        )

        model.fit(sales_df)

        # Create future dataframe for predictions
        future = model.make_future_dataframe(periods=data.horizon_days)

        # Make forecast
        forecast = model.predict(future)

        # Save the model
        joblib.dump(model, 'models/sales_forecast_model.joblib')

        # Get future dates only (beyond the provided data)
        last_date = sales_df['ds'].max()
        future_forecast = forecast[forecast['ds'] > last_date]

        # Prepare response
        return {
            "forecast_dates": future_forecast['ds'].dt.strftime('%Y-%m-%d').tolist(),
            "forecast_values": future_forecast['yhat'].tolist(),
            "forecast_lower_bound": future_forecast['yhat_lower'].tolist(),
            "forecast_upper_bound": future_forecast['yhat_upper'].tolist()
        }

    except Exception as e:
        logger.error(f"Error in predict_sales: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error processing sales data: {str(e)}")

# Add new endpoints for manual training

@app.post("/upload-train-segmentation", response_model=SegmentationResponse)
async def upload_train_segmentation(file: UploadFile = File(...), num_clusters: int = Form(3)):
    """
    Upload a CSV or Excel file with retailer data and train the segmentation model.
    File should contain columns matching the RetailerData model.

    Required columns:
    - id: Unique identifier for each retailer
    - total_sales: Total sales amount for the retailer

    Optional demographic columns:
    - male_female_ratio: Gender ratio of customers
    - city: City name
    - urban_rural_classification: urban, suburban, or rural
    - customer_age_class: customer age category
    - customer_income_bracket: low, medium, high
    - customer_education_level: low, mid, high
    - business_type: Type of business
    - annual_revenue: Annual revenue amount
    - employee_count: Number of employees
    - years_in_business: How long the business has existed
    """
    try:
        # Read the file content
        content = await file.read()

        if not content:
            raise HTTPException(status_code=400, detail="Empty file provided")

        file_extension = file.filename.split('.')[-1].lower()

        # Parse file content based on extension
        try:
            if file_extension == 'csv':
                retailers_df = pd.read_csv(io.StringIO(content.decode('utf-8')))
            elif file_extension in ['xlsx', 'xls']:
                retailers_df = pd.read_excel(io.BytesIO(content))
            else:
                raise HTTPException(status_code=400, detail=f"Unsupported file format: {file_extension}. Please use CSV or Excel.")
        except Exception as parse_error:
            logger.error(f"Error parsing file: {str(parse_error)}")
            raise HTTPException(status_code=400, detail=f"Error parsing file: {str(parse_error)}")

        # Check if dataframe is empty
        if retailers_df.empty:
            raise HTTPException(status_code=400, detail="File contains no data")

        # Check required columns
        required_columns = ['id', 'total_sales']
        missing_columns = [col for col in required_columns if col not in retailers_df.columns]
        if missing_columns:
            raise HTTPException(
                status_code=400,
                detail=f"Missing required column(s): {', '.join(missing_columns)}. Required columns: {', '.join(required_columns)}"
            )

        # Validate data types
        if not pd.api.types.is_numeric_dtype(retailers_df['id']):
            try:
                retailers_df['id'] = pd.to_numeric(retailers_df['id'])
            except:
                raise HTTPException(status_code=400, detail="Column 'id' must contain numeric values")

        if not pd.api.types.is_numeric_dtype(retailers_df['total_sales']):
            try:
                retailers_df['total_sales'] = pd.to_numeric(retailers_df['total_sales'])
            except:
                raise HTTPException(status_code=400, detail="Column 'total_sales' must contain numeric values")

        # Check for duplicate IDs
        if retailers_df['id'].duplicated().any():
            raise HTTPException(status_code=400, detail="Duplicate retailer IDs found. Each retailer must have a unique ID.")

        logger.info(f"Processing segmentation training data with {len(retailers_df)} retailers and {num_clusters} clusters")

        # Process and train
        return train_customer_segmentation(retailers_df, num_clusters)

    except HTTPException as he:
        # Re-raise HTTP exceptions
        raise he
    except Exception as e:
        logger.error(f"Error in upload_train_segmentation: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error processing uploaded file: {str(e)}")

@app.post("/upload-train-forecast", response_model=SalesForecastResponse)
async def upload_train_forecast(file: UploadFile = File(...), horizon_days: int = Form(90)):
    """
    Upload a CSV or Excel file with sales data and train the forecast model.
    File should contain 'date' and 'amount' columns.

    Required columns:
    - date: Date of sales in any standard format (YYYY-MM-DD recommended)
    - amount: Numeric sales amount

    Parameters:
    - horizon_days: Number of days to forecast ahead
    """
    try:
        # Read the file content
        content = await file.read()

        if not content:
            raise HTTPException(status_code=400, detail="Empty file provided")

        file_extension = file.filename.split('.')[-1].lower()

        # Parse file content based on extension
        try:
            if file_extension == 'csv':
                sales_df = pd.read_csv(io.StringIO(content.decode('utf-8')))
            elif file_extension in ['xlsx', 'xls']:
                sales_df = pd.read_excel(io.BytesIO(content))
            else:
                raise HTTPException(status_code=400, detail=f"Unsupported file format: {file_extension}. Please use CSV or Excel.")
        except Exception as parse_error:
            logger.error(f"Error parsing file: {str(parse_error)}")
            raise HTTPException(status_code=400, detail=f"Error parsing file: {str(parse_error)}")

        # Check if dataframe is empty
        if sales_df.empty:
            raise HTTPException(status_code=400, detail="File contains no data")

        # Check required columns
        required_columns = ['date', 'amount']
        missing_columns = [col for col in required_columns if col not in sales_df.columns]
        if missing_columns:
            raise HTTPException(
                status_code=400,
                detail=f"Missing required column(s): {', '.join(missing_columns)}. Required columns: {', '.join(required_columns)}"
            )

        # Convert date column to datetime
        try:
            sales_df['date'] = pd.to_datetime(sales_df['date'])
        except Exception as date_error:
            raise HTTPException(status_code=400, detail=f"Error parsing dates: {str(date_error)}. Please ensure dates are in a standard format (YYYY-MM-DD recommended).")

        # Validate amount column
        if not pd.api.types.is_numeric_dtype(sales_df['amount']):
            try:
                sales_df['amount'] = pd.to_numeric(sales_df['amount'])
            except:
                raise HTTPException(status_code=400, detail="Column 'amount' must contain numeric values")

        # Check for negative amounts
        if (sales_df['amount'] < 0).any():
            logger.warning("Negative sales amounts found in the uploaded data")

        # Check if there's enough data for forecasting (at least 2 weeks recommended)
        if len(sales_df) < 14:
            logger.warning(f"Limited data points ({len(sales_df)}) provided for sales forecasting. Results may be unreliable.")

        # Check for duplicate dates
        if sales_df['date'].duplicated().any():
            logger.warning("Duplicate dates found in sales data. Values will be aggregated.")
            # Aggregate values for duplicate dates
            sales_df = sales_df.groupby('date', as_index=False)['amount'].sum()

        # Sort by date
        sales_df = sales_df.sort_values('date')

        logger.info(f"Processing forecast training data with {len(sales_df)} data points and {horizon_days} days horizon")

        # Convert to the format expected by predict_sales
        sales_data = SalesTimeSeriesData(
            sales=[SalesData(date=str(row['date'].date()), amount=float(row['amount'])) for _, row in sales_df.iterrows()],
            horizon_days=horizon_days
        )

        # Process and train
        return predict_sales(sales_data)

    except HTTPException as he:
        # Re-raise HTTP exceptions
        raise he
    except Exception as e:
        logger.error(f"Error in upload_train_forecast: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error processing uploaded file: {str(e)}")

# Helper function to perform segmentation training
def train_customer_segmentation(retailers_df, num_clusters=3):
    """
    Train customer segmentation model using provided dataframe
    """
    # Preprocess data
    processed_data = preprocess_retailer_data(retailers_df)

    # Use existing segmentation logic
    return segment_customers_logic(processed_data, retailers_df, num_clusters)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
