import pytest
import json
from fastapi.testclient import TestClient
from app import app
import io
import pandas as pd

# Create test client
client = TestClient(app)


def test_root_endpoint():
    """Test that the root endpoint returns a 200 OK response"""
    response = client.get("/")
    assert response.status_code == 200
    assert "status" in response.json()
    assert response.json()["status"] == "active"


def test_health_endpoint():
    """Test that the health endpoint returns a 200 OK response"""
    response = client.get("/health")
    assert response.status_code == 200
    assert "status" in response.json()
    assert response.json()["status"] == "healthy"


def test_segment_customers_endpoint():
    """Test that the segment-customers endpoint processes data correctly"""
    # Create test data
    test_data = {
        "retailers": [
            {
                "id": 1,
                "male_female_ratio": 0.8,
                "city": "New York",
                "urban_rural_classification": "urban",
                "customer_age_class": "adult",
                "customer_income_bracket": "high",
                "customer_education_level": "high",
                "business_type": "Electronics",
                "annual_revenue": "500000",
                "employee_count": "50",
                "years_in_business": 10,
                "total_sales": 15000
            },
            {
                "id": 2,
                "male_female_ratio": 1.2,
                "city": "Chicago",
                "urban_rural_classification": "suburban",
                "customer_age_class": "youth",
                "customer_income_bracket": "medium",
                "customer_education_level": "mid",
                "business_type": "Clothing",
                "annual_revenue": "200000",
                "employee_count": "15",
                "years_in_business": 5,
                "total_sales": 8000
            },
            {
                "id": 3,
                "male_female_ratio": 0.9,
                "city": "Rural Town",
                "urban_rural_classification": "rural",
                "customer_age_class": "adult",
                "customer_income_bracket": "low",
                "customer_education_level": "low",
                "business_type": "Grocery",
                "annual_revenue": "50000",
                "employee_count": "3",
                "years_in_business": 2,
                "total_sales": 3000
            }
        ]
    }

    response = client.post("/segment-customers", json=test_data)
    assert response.status_code == 200

    # Check response structure
    data = response.json()
    assert "retailer_segments" in data
    assert "segment_descriptions" in data
    assert "segment_distribution" in data

    # Check retailer_segments contains all IDs
    assert set(map(int, data["retailer_segments"].keys())) == {1, 2, 3}

    # Check segment_descriptions
    assert len(data["segment_descriptions"]) > 0

    # Check segment_distribution
    assert sum(data["segment_distribution"].values()) == 3


def test_segment_customers_with_empty_data():
    """Test that the segment-customers endpoint handles empty data"""
    test_data = {"retailers": []}

    response = client.post("/segment-customers", json=test_data)
    assert response.status_code == 400
    assert "detail" in response.json()


def test_predict_sales_endpoint():
    """Test that the predict-sales endpoint processes data correctly"""
    # Create test data
    test_data = {
        "sales": [
            {"date": "2025-01-01", "amount": 5000},
            {"date": "2025-01-02", "amount": 5200},
            {"date": "2025-01-03", "amount": 5400},
            {"date": "2025-01-04", "amount": 5600},
            {"date": "2025-01-05", "amount": 5800},
            {"date": "2025-01-06", "amount": 6000},
            {"date": "2025-01-07", "amount": 6200},
            {"date": "2025-01-08", "amount": 6400},
            {"date": "2025-01-09", "amount": 6600},
            {"date": "2025-01-10", "amount": 6800},
            {"date": "2025-01-11", "amount": 7000},
            {"date": "2025-01-12", "amount": 7200},
            {"date": "2025-01-13", "amount": 7400},
            {"date": "2025-01-14", "amount": 7600}
        ],
        "horizon_days": 7
    }

    response = client.post("/predict-sales", json=test_data)
    assert response.status_code == 200

    # Check response structure
    data = response.json()
    assert "forecast_dates" in data
    assert "forecast_values" in data
    assert "forecast_lower_bound" in data
    assert "forecast_upper_bound" in data

    # Check forecast length matches horizon
    assert len(data["forecast_dates"]) == 7
    assert len(data["forecast_values"]) == 7
    assert len(data["forecast_lower_bound"]) == 7
    assert len(data["forecast_upper_bound"]) == 7


def test_predict_sales_with_empty_data():
    """Test that the predict-sales endpoint handles empty data"""
    test_data = {"sales": [], "horizon_days": 7}

    response = client.post("/predict-sales", json=test_data)
    assert response.status_code == 400
    assert "detail" in response.json()


def test_upload_train_segmentation():
    """Test that the upload-train-segmentation endpoint processes CSV files correctly"""
    # Create test CSV file content
    test_data = pd.DataFrame({
        'id': [1, 2, 3],
        'male_female_ratio': [0.8, 1.2, 0.9],
        'city': ['New York', 'Chicago', 'Rural Town'],
        'urban_rural_classification': ['urban', 'suburban', 'rural'],
        'customer_age_class': ['adult', 'youth', 'adult'],
        'customer_income_bracket': ['high', 'medium', 'low'],
        'customer_education_level': ['high', 'mid', 'low'],
        'business_type': ['Electronics', 'Clothing', 'Grocery'],
        'annual_revenue': ['500000', '200000', '50000'],
        'employee_count': ['50', '15', '3'],
        'years_in_business': [10, 5, 2],
        'total_sales': [15000, 8000, 3000]
    })

    # Convert to CSV
    csv_content = test_data.to_csv(index=False).encode('utf-8')

    # Create file-like object
    csv_file = io.BytesIO(csv_content)

    # Make request
    response = client.post(
        "/upload-train-segmentation",
        files={"file": ("test_retailers.csv", csv_file, "text/csv")},
        data={"num_clusters": 2}
    )

    assert response.status_code == 200

    # Check response structure
    data = response.json()
    assert "retailer_segments" in data
    assert "segment_descriptions" in data
    assert "segment_distribution" in data

    # Check retailer_segments contains all IDs
    assert set(map(int, data["retailer_segments"].keys())) == {1, 2, 3}


def test_upload_train_segmentation_invalid_format():
    """Test that the upload-train-segmentation endpoint handles invalid file formats"""
    # Create invalid file content
    invalid_file = io.BytesIO(b"not a valid CSV or Excel file")

    # Make request
    response = client.post(
        "/upload-train-segmentation",
        files={"file": ("test.txt", invalid_file, "text/plain")},
        data={"num_clusters": 2}
    )

    assert response.status_code == 400
    assert "Unsupported file format" in response.json()["detail"]


def test_upload_train_segmentation_missing_columns():
    """Test that the upload-train-segmentation endpoint handles missing required columns"""
    # Create test CSV with missing columns
    test_data = pd.DataFrame({
        'id': [1, 2, 3],
        # Missing total_sales column
        'male_female_ratio': [0.8, 1.2, 0.9]
    })

    # Convert to CSV
    csv_content = test_data.to_csv(index=False).encode('utf-8')

    # Create file-like object
    csv_file = io.BytesIO(csv_content)

    # Make request
    response = client.post(
        "/upload-train-segmentation",
        files={"file": ("test_retailers.csv", csv_file, "text/csv")},
        data={"num_clusters": 2}
    )

    assert response.status_code == 400
    assert "Missing required column" in response.json()["detail"]


def test_upload_train_forecast():
    """Test that the upload-train-forecast endpoint processes CSV files correctly"""
    # Create test CSV file content
    test_data = pd.DataFrame({
        'date': pd.date_range(start='2025-01-01', periods=14),
        'amount': [5000, 5200, 5400, 5600, 5800, 6000, 6200,
                   6400, 6600, 6800, 7000, 7200, 7400, 7600]
    })

    # Convert to CSV
    csv_content = test_data.to_csv(index=False).encode('utf-8')

    # Create file-like object
    csv_file = io.BytesIO(csv_content)

    # Make request
    response = client.post(
        "/upload-train-forecast",
        files={"file": ("test_sales.csv", csv_file, "text/csv")},
        data={"horizon_days": 7}
    )

    assert response.status_code == 200

    # Check response structure
    data = response.json()
    assert "forecast_dates" in data
    assert "forecast_values" in data
    assert "forecast_lower_bound" in data
    assert "forecast_upper_bound" in data

    # Check forecast length matches horizon
    assert len(data["forecast_dates"]) == 7


def test_upload_train_forecast_invalid_format():
    """Test that the upload-train-forecast endpoint handles invalid file formats"""
    # Create invalid file content
    invalid_file = io.BytesIO(b"not a valid CSV or Excel file")

    # Make request
    response = client.post(
        "/upload-train-forecast",
        files={"file": ("test.txt", invalid_file, "text/plain")},
        data={"horizon_days": 7}
    )

    assert response.status_code == 400
    assert "Unsupported file format" in response.json()["detail"]


def test_upload_train_forecast_missing_columns():
    """Test that the upload-train-forecast endpoint handles missing required columns"""
    # Create test CSV with missing columns
    test_data = pd.DataFrame({
        'date': pd.date_range(start='2025-01-01', periods=14),
        # Missing amount column
    })

    # Convert to CSV
    csv_content = test_data.to_csv(index=False).encode('utf-8')

    # Create file-like object
    csv_file = io.BytesIO(csv_content)

    # Make request
    response = client.post(
        "/upload-train-forecast",
        files={"file": ("test_sales.csv", csv_file, "text/csv")},
        data={"horizon_days": 7}
    )

    assert response.status_code == 400
    assert "Missing required column" in response.json()["detail"]


if __name__ == "__main__":
    pytest.main()
