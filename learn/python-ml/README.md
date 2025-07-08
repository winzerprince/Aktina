# Python ML Microservice in Aktina SCM 🐍🤖

The Python FastAPI microservice handles machine learning operations for customer segmentation and sales forecasting.

## 📍 Location in Project
```
microservices/python-ml/
├── app.py                           # Main FastAPI application
├── requirements.txt                 # Python dependencies
├── examples/                        # Sample data and examples
├── models/                         # Trained ML models storage
├── training_data/                  # Training datasets
│   ├── customer_segmentation_training.csv
│   ├── sales_forecasting_training.csv
│   └── README.md
├── test_app.py                     # Unit tests
├── test_training.sh                # Training validation script
├── MANUAL_TRAINING.md              # Training documentation
└── TRAINING_SUMMARY.md             # Training results summary
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: The Crystal Ball Robot**

Imagine a magical robot with a crystal ball that can predict the future:

- **The robot looks at old information** (past sales data) to learn patterns
- **It groups similar customers** (segmentation) like putting similar toys in the same box
- **It predicts what will happen** (sales forecasting) like guessing how many ice creams will sell on a hot day
- **It gets smarter over time** (machine learning) by learning from new information
- **It tells the main system** what it learned so people can make better decisions

The robot is really good at finding hidden patterns that people might miss!

### 🎓 **CS Student Level: ML Microservice Architecture**

The Python microservice implements **Machine Learning Pipeline** with **RESTful APIs**:

```python
# FastAPI application with ML endpoints
from fastapi import FastAPI, File, UploadFile
from sklearn.cluster import KMeans
from prophet import Prophet
import pandas as pd
import numpy as np

app = FastAPI(title="Aktina ML Service", version="1.0.0")

@app.post("/segment")
async def customer_segmentation(data: List[CustomerData]):
    """
    Perform customer segmentation using K-Means clustering
    """
    # Convert to DataFrame
    df = pd.DataFrame([customer.dict() for customer in data])
    
    # Feature engineering
    features = prepare_features(df)
    
    # Apply K-Means clustering
    kmeans = KMeans(n_clusters=5, random_state=42)
    clusters = kmeans.fit_predict(features)
    
    # Generate segment descriptions
    segments = generate_segment_descriptions(features, clusters)
    
    return {
        "segments": segments,
        "cluster_centers": kmeans.cluster_centers_.tolist(),
        "inertia": kmeans.inertia_
    }

@app.post("/forecast")
async def sales_forecasting(data: List[SalesData]):
    """
    Generate sales forecasts using Prophet time series model
    """
    # Prepare data for Prophet
    df = pd.DataFrame([sale.dict() for sale in data])
    prophet_df = df.rename(columns={'date': 'ds', 'amount': 'y'})
    
    # Train Prophet model
    model = Prophet(
        yearly_seasonality=True,
        weekly_seasonality=True,
        daily_seasonality=False
    )
    model.fit(prophet_df)
    
    # Generate 30-day forecast
    future = model.make_future_dataframe(periods=30)
    forecast = model.predict(future)
    
    return {
        "forecast": forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].tail(30).to_dict('records'),
        "model_performance": calculate_model_metrics(model, prophet_df)
    }
```

**Key Features:**
- **Customer Segmentation**: K-Means clustering based on demographics and behavior
- **Sales Forecasting**: Prophet time series forecasting for Aktina sales
- **Model Training**: Manual training capabilities with CSV/Excel uploads
- **API Integration**: RESTful endpoints for Laravel integration

### 👨‍🏫 **CS Professor Level: ML Engineering & Domain-Driven Design**

The Python microservice implements **MLOps Pipeline** with **Domain-Driven ML Architecture**:

```python
from abc import ABC, abstractmethod
from dataclasses import dataclass
from typing import Protocol, List, Dict, Any
import joblib
from pathlib import Path

# Domain models
@dataclass
class CustomerSegment:
    segment_id: int
    description: str
    characteristics: Dict[str, Any]
    customer_count: int
    avg_value: float

@dataclass
class SalesForecast:
    date: str
    predicted_value: float
    confidence_interval: tuple[float, float]
    trend: str

# Repository pattern for model persistence
class ModelRepository(ABC):
    @abstractmethod
    def save_model(self, model: Any, model_name: str) -> None:
        pass
    
    @abstractmethod
    def load_model(self, model_name: str) -> Any:
        pass

class FileSystemModelRepository(ModelRepository):
    def __init__(self, models_dir: Path = Path("models")):
        self.models_dir = models_dir
        self.models_dir.mkdir(exist_ok=True)
    
    def save_model(self, model: Any, model_name: str) -> None:
        model_path = self.models_dir / f"{model_name}.joblib"
        joblib.dump(model, model_path)
    
    def load_model(self, model_name: str) -> Any:
        model_path = self.models_dir / f"{model_name}.joblib"
        if model_path.exists():
            return joblib.load(model_path)
        return None

# Domain services
class CustomerSegmentationService:
    def __init__(self, model_repository: ModelRepository):
        self.model_repository = model_repository
    
    def segment_customers(self, customer_data: List[Dict]) -> List[CustomerSegment]:
        # Load or train model
        model = self.model_repository.load_model("customer_segmentation")
        if model is None:
            model = self._train_segmentation_model(customer_data)
            self.model_repository.save_model(model, "customer_segmentation")
        
        # Apply segmentation
        segments = self._apply_segmentation(model, customer_data)
        return segments
    
    def _train_segmentation_model(self, data: List[Dict]) -> KMeans:
        # Feature engineering and model training
        features = self._extract_features(data)
        model = KMeans(n_clusters=5, random_state=42, n_init=10)
        model.fit(features)
        return model

class SalesForecastingService:
    def __init__(self, model_repository: ModelRepository):
        self.model_repository = model_repository
    
    def forecast_sales(self, sales_data: List[Dict], days: int = 30) -> List[SalesForecast]:
        # Domain-specific forecasting logic
        model = self._get_or_train_model(sales_data)
        forecasts = self._generate_forecasts(model, days)
        return forecasts
    
    def _get_or_train_model(self, data: List[Dict]) -> Prophet:
        model = self.model_repository.load_model("sales_forecasting")
        if model is None or self._should_retrain(model, data):
            model = self._train_forecasting_model(data)
            self.model_repository.save_model(model, "sales_forecasting")
        return model

# Application service (orchestrates domain services)
class MLApplicationService:
    def __init__(
        self,
        segmentation_service: CustomerSegmentationService,
        forecasting_service: SalesForecastingService
    ):
        self.segmentation_service = segmentation_service
        self.forecasting_service = forecasting_service
    
    async def process_customer_segmentation(self, data: List[Dict]) -> Dict:
        segments = self.segmentation_service.segment_customers(data)
        return {
            "segments": [self._segment_to_dict(segment) for segment in segments],
            "total_segments": len(segments),
            "processing_timestamp": datetime.utcnow().isoformat()
        }
```

## 🏗️ Architecture Patterns Used

### **1. Microservice Pattern**
Independent ML service with FastAPI:

```python
# Main FastAPI application
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional

app = FastAPI(
    title="Aktina ML Service",
    description="Machine Learning microservice for customer segmentation and sales forecasting",
    version="1.0.0"
)

# Enable CORS for Laravel integration
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000"],  # Laravel URL
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Health check endpoint
@app.get("/health")
async def health_check():
    return {
        "status": "healthy",
        "service": "aktina-ml",
        "version": "1.0.0",
        "timestamp": datetime.utcnow().isoformat()
    }
```

### **2. Repository Pattern for Model Persistence**
```python
import joblib
from pathlib import Path
from datetime import datetime

class ModelRepository:
    def __init__(self, models_dir: str = "models"):
        self.models_dir = Path(models_dir)
        self.models_dir.mkdir(exist_ok=True)
    
    def save_model(self, model, model_name: str, metadata: dict = None):
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        model_path = self.models_dir / f"{model_name}_{timestamp}.joblib"
        
        # Save model with metadata
        model_data = {
            'model': model,
            'metadata': metadata or {},
            'created_at': datetime.utcnow().isoformat(),
            'version': timestamp
        }
        
        joblib.dump(model_data, model_path)
        
        # Create symlink to latest version
        latest_path = self.models_dir / f"{model_name}_latest.joblib"
        if latest_path.exists():
            latest_path.unlink()
        latest_path.symlink_to(model_path.name)
    
    def load_model(self, model_name: str):
        latest_path = self.models_dir / f"{model_name}_latest.joblib"
        if latest_path.exists():
            return joblib.load(latest_path)
        return None
```

### **3. Service Layer Pattern**
```python
class CustomerSegmentationService:
    def __init__(self):
        self.model_repository = ModelRepository()
    
    def segment_customers(self, retailer_data: List[dict]) -> dict:
        try:
            # Prepare features
            df = pd.DataFrame(retailer_data)
            features = self._prepare_features(df)
            
            # Load or train model
            model_data = self.model_repository.load_model("customer_segmentation")
            if model_data is None:
                model = self._train_model(features)
                self.model_repository.save_model(
                    model, 
                    "customer_segmentation",
                    {"features": features.columns.tolist(), "n_clusters": 5}
                )
            else:
                model = model_data['model']
            
            # Apply clustering
            clusters = model.predict(features)
            
            return self._generate_segment_analysis(df, clusters, model)
            
        except Exception as e:
            raise MLProcessingException(f"Segmentation failed: {str(e)}")
```

## 📋 Actual Implementation Examples

### **Main FastAPI Application**
```python
# File: app.py
from fastapi import FastAPI, HTTPException, UploadFile, File
from pydantic import BaseModel
from typing import List, Optional
import pandas as pd
import numpy as np
from sklearn.cluster import KMeans
from prophet import Prophet
import joblib
from pathlib import Path
import json
from datetime import datetime

app = FastAPI(title="Aktina ML Service")

# Pydantic models for request/response
class CustomerData(BaseModel):
    id: int
    male_female_ratio: float
    city: str
    urban_rural_classification: str
    customer_age_class: str
    customer_income_bracket: str
    customer_education_level: str
    company_name: str

class SalesData(BaseModel):
    date: str
    amount: float
    company_name: str

@app.post("/segment")
async def customer_segmentation(data: List[CustomerData]):
    """Customer segmentation using K-Means clustering"""
    try:
        # Convert to DataFrame
        df = pd.DataFrame([customer.dict() for customer in data])
        
        # Feature engineering
        features = prepare_segmentation_features(df)
        
        # Apply K-Means
        kmeans = KMeans(n_clusters=5, random_state=42, n_init=10)
        clusters = kmeans.fit_predict(features)
        
        # Save model
        save_model(kmeans, "customer_segmentation")
        
        # Generate segments
        segments = analyze_segments(df, clusters)
        
        return {
            "segments": segments,
            "total_customers": len(df),
            "model_info": {
                "inertia": float(kmeans.inertia_),
                "n_clusters": 5
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Segmentation failed: {str(e)}")

@app.post("/forecast")
async def sales_forecasting(data: List[SalesData]):
    """Sales forecasting using Prophet"""
    try:
        # Filter Aktina sales data
        df = pd.DataFrame([sale.dict() for sale in data])
        aktina_sales = df[df['company_name'] == 'Aktina'].copy()
        
        if len(aktina_sales) < 10:
            raise HTTPException(status_code=400, detail="Insufficient Aktina sales data")
        
        # Prepare for Prophet
        prophet_df = aktina_sales.rename(columns={'date': 'ds', 'amount': 'y'})
        prophet_df['ds'] = pd.to_datetime(prophet_df['ds'])
        
        # Train Prophet model
        model = Prophet(
            yearly_seasonality=True,
            weekly_seasonality=True,
            daily_seasonality=False
        )
        model.fit(prophet_df)
        
        # Generate forecast
        future = model.make_future_dataframe(periods=30)
        forecast = model.predict(future)
        
        # Save model
        save_model(model, "sales_forecasting")
        
        # Return forecast
        forecast_data = forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].tail(30)
        
        return {
            "forecast": forecast_data.to_dict('records'),
            "model_performance": calculate_forecast_metrics(model, prophet_df),
            "forecast_period": "30_days"
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Forecasting failed: {str(e)}")

def prepare_segmentation_features(df):
    """Prepare features for customer segmentation"""
    # Encode categorical variables
    df_encoded = df.copy()
    
    # Urban/rural encoding
    urban_rural_map = {'urban': 2, 'suburban': 1, 'rural': 0}
    df_encoded['urban_rural_encoded'] = df['urban_rural_classification'].map(urban_rural_map)
    
    # Age class encoding
    age_map = {'child': 0, 'teenager': 1, 'youth': 2, 'adult': 3, 'senior': 4}
    df_encoded['age_encoded'] = df['customer_age_class'].map(age_map)
    
    # Income bracket encoding
    income_map = {'low': 0, 'medium': 1, 'high': 2}
    df_encoded['income_encoded'] = df['customer_income_bracket'].map(income_map)
    
    # Education level encoding
    education_map = {'low': 0, 'mid': 1, 'high': 2}
    df_encoded['education_encoded'] = df['customer_education_level'].map(education_map)
    
    # Select features for clustering
    features = df_encoded[[
        'male_female_ratio',
        'urban_rural_encoded',
        'age_encoded',
        'income_encoded',
        'education_encoded'
    ]]
    
    return features.fillna(features.median())
```

### **Manual Training Endpoints**
```python
@app.post("/upload-train-segmentation")
async def upload_train_segmentation(file: UploadFile = File(...)):
    """Upload CSV/Excel file to train customer segmentation model"""
    try:
        # Read uploaded file
        if file.filename.endswith('.csv'):
            df = pd.read_csv(file.file)
        elif file.filename.endswith(('.xlsx', '.xls')):
            df = pd.read_excel(file.file)
        else:
            raise HTTPException(status_code=400, detail="File must be CSV or Excel")
        
        # Validate required columns
        required_columns = [
            'male_female_ratio', 'city', 'urban_rural_classification',
            'customer_age_class', 'customer_income_bracket', 'customer_education_level'
        ]
        
        missing_columns = [col for col in required_columns if col not in df.columns]
        if missing_columns:
            raise HTTPException(
                status_code=400, 
                detail=f"Missing required columns: {missing_columns}"
            )
        
        # Prepare features and train model
        features = prepare_segmentation_features(df)
        
        kmeans = KMeans(n_clusters=5, random_state=42, n_init=10)
        clusters = kmeans.fit_predict(features)
        
        # Save trained model
        save_model(kmeans, "customer_segmentation")
        
        # Analyze segments
        segments = analyze_segments(df, clusters)
        
        return {
            "message": "Customer segmentation model trained successfully",
            "training_data_size": len(df),
            "segments": segments,
            "model_performance": {
                "inertia": float(kmeans.inertia_),
                "n_clusters": 5
            }
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Training failed: {str(e)}")

@app.post("/upload-train-forecast")
async def upload_train_forecast(file: UploadFile = File(...)):
    """Upload CSV/Excel file to train sales forecasting model"""
    try:
        # Read uploaded file
        if file.filename.endswith('.csv'):
            df = pd.read_csv(file.file)
        elif file.filename.endswith(('.xlsx', '.xls')):
            df = pd.read_excel(file.file)
        else:
            raise HTTPException(status_code=400, detail="File must be CSV or Excel")
        
        # Validate required columns
        required_columns = ['date', 'amount']
        missing_columns = [col for col in required_columns if col not in df.columns]
        if missing_columns:
            raise HTTPException(
                status_code=400, 
                detail=f"Missing required columns: {missing_columns}"
            )
        
        # Prepare data for Prophet
        prophet_df = df.rename(columns={'date': 'ds', 'amount': 'y'})
        prophet_df['ds'] = pd.to_datetime(prophet_df['ds'])
        
        if len(prophet_df) < 10:
            raise HTTPException(status_code=400, detail="Need at least 10 data points")
        
        # Train Prophet model
        model = Prophet(
            yearly_seasonality=True,
            weekly_seasonality=True,
            daily_seasonality=False
        )
        model.fit(prophet_df)
        
        # Generate test forecast
        future = model.make_future_dataframe(periods=30)
        forecast = model.predict(future)
        
        # Save trained model
        save_model(model, "sales_forecasting")
        
        return {
            "message": "Sales forecasting model trained successfully",
            "training_data_size": len(prophet_df),
            "date_range": {
                "start": prophet_df['ds'].min().isoformat(),
                "end": prophet_df['ds'].max().isoformat()
            },
            "sample_forecast": forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].tail(5).to_dict('records')
        }
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Training failed: {str(e)}")
```

## 🔗 Interconnections

### **With Laravel Main Application**
```python
# HTTP endpoints called by Laravel MLService
@app.get("/segment-customers")
async def get_customer_segmentation():
    """Endpoint called by Laravel to get customer segments"""
    try:
        # This would typically receive data from Laravel
        # For now, return cached/stored segmentation results
        model = load_model("customer_segmentation")
        if model is None:
            raise HTTPException(status_code=404, detail="No trained segmentation model found")
        
        # Return segmentation results
        return {
            "segments": get_stored_segments(),
            "last_updated": get_model_timestamp("customer_segmentation")
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/sales-forecast")
async def get_sales_forecast():
    """Endpoint called by Laravel to get sales forecasts"""
    try:
        model = load_model("sales_forecasting")
        if model is None:
            raise HTTPException(status_code=404, detail="No trained forecasting model found")
        
        # Generate current forecast
        future = model.make_future_dataframe(periods=30)
        forecast = model.predict(future)
        
        return {
            "forecast": forecast[['ds', 'yhat', 'yhat_lower', 'yhat_upper']].tail(30).to_dict('records'),
            "generated_at": datetime.utcnow().isoformat()
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
```

### **Model Persistence**
```python
import joblib
from pathlib import Path
from datetime import datetime

def save_model(model, model_name: str):
    """Save trained model to disk"""
    models_dir = Path("models")
    models_dir.mkdir(exist_ok=True)
    
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    model_path = models_dir / f"{model_name}_{timestamp}.joblib"
    
    # Save model with metadata
    model_data = {
        'model': model,
        'created_at': datetime.utcnow().isoformat(),
        'version': timestamp
    }
    
    joblib.dump(model_data, model_path)
    
    # Create latest symlink
    latest_path = models_dir / f"{model_name}_latest.joblib"
    if latest_path.exists():
        latest_path.unlink()
    latest_path.symlink_to(model_path.name)

def load_model(model_name: str):
    """Load latest trained model"""
    latest_path = Path("models") / f"{model_name}_latest.joblib"
    if latest_path.exists():
        model_data = joblib.load(latest_path)
        return model_data['model']
    return None
```

## 🎯 Best Practices Used

### **1. Pydantic Data Validation**
```python
from pydantic import BaseModel, validator
from typing import List, Optional

class CustomerData(BaseModel):
    id: int
    male_female_ratio: float
    city: str
    urban_rural_classification: str
    customer_age_class: str
    customer_income_bracket: str
    customer_education_level: str
    company_name: str
    
    @validator('male_female_ratio')
    def validate_ratio(cls, v):
        if v < 0:
            raise ValueError('Male/female ratio cannot be negative')
        return v
    
    @validator('urban_rural_classification')
    def validate_classification(cls, v):
        if v not in ['urban', 'suburban', 'rural']:
            raise ValueError('Invalid urban/rural classification')
        return v
```

### **2. Error Handling**
```python
class MLProcessingException(Exception):
    """Custom exception for ML processing errors"""
    pass

@app.exception_handler(MLProcessingException)
async def ml_exception_handler(request, exc):
    return JSONResponse(
        status_code=500,
        content={"detail": f"ML Processing Error: {str(exc)}"}
    )
```

### **3. Model Versioning**
```python
def save_model_with_version(model, model_name: str, metadata: dict = None):
    """Save model with proper versioning"""
    models_dir = Path("models")
    models_dir.mkdir(exist_ok=True)
    
    # Version using timestamp
    version = datetime.now().strftime("%Y%m%d_%H%M%S")
    
    model_data = {
        'model': model,
        'metadata': metadata or {},
        'version': version,
        'created_at': datetime.utcnow().isoformat(),
        'model_type': type(model).__name__
    }
    
    # Save versioned model
    versioned_path = models_dir / f"{model_name}_v{version}.joblib"
    joblib.dump(model_data, versioned_path)
    
    # Update latest pointer
    latest_path = models_dir / f"{model_name}_latest.joblib"
    if latest_path.is_symlink():
        latest_path.unlink()
    latest_path.symlink_to(versioned_path.name)
    
    return version
```

## 📊 Performance Considerations

### **1. Async Processing**
```python
import asyncio
from concurrent.futures import ThreadPoolExecutor

# Thread pool for CPU-intensive ML operations
ml_executor = ThreadPoolExecutor(max_workers=4)

@app.post("/segment-async")
async def async_customer_segmentation(data: List[CustomerData]):
    """Asynchronous customer segmentation"""
    loop = asyncio.get_event_loop()
    
    # Run ML processing in thread pool
    result = await loop.run_in_executor(
        ml_executor,
        process_segmentation,
        [customer.dict() for customer in data]
    )
    
    return result
```

### **2. Caching**
```python
from functools import lru_cache
import hashlib

@lru_cache(maxsize=32)
def get_cached_segmentation(data_hash: str):
    """Cache segmentation results"""
    # Return cached results if available
    pass

def hash_data(data: List[dict]) -> str:
    """Generate hash for caching"""
    data_str = json.dumps(data, sort_keys=True)
    return hashlib.md5(data_str.encode()).hexdigest()
```

### **3. Model Loading Optimization**
```python
# Global model cache
_model_cache = {}

def get_model(model_name: str):
    """Get model with caching"""
    if model_name not in _model_cache:
        model = load_model(model_name)
        if model:
            _model_cache[model_name] = model
    
    return _model_cache.get(model_name)
```

The Python ML microservice provides sophisticated machine learning capabilities while maintaining clean architecture and efficient integration with the Laravel main application through well-designed APIs and proper model management.
