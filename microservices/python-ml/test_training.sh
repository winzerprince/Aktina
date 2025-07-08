#!/bin/bash

# Training script for ML models using sample data
echo "🚀 Starting ML Model Training with Sample Data"
echo "============================================="

# Check if Python virtual environment is activated
if [[ "$VIRTUAL_ENV" != "" ]]; then
    echo "✅ Virtual environment is active: $VIRTUAL_ENV"
else
    echo "⚠️  Virtual environment not active. Activating..."
    source .venv/bin/activate
fi

# Check if FastAPI server is running
echo "🔍 Checking if FastAPI server is running..."
if curl -s http://localhost:8000/health > /dev/null; then
    echo "✅ FastAPI server is running"
else
    echo "❌ FastAPI server is not running. Starting server..."
    echo "🚀 Starting FastAPI server in background..."
    nohup python app.py > server.log 2>&1 &
    sleep 5

    # Check again
    if curl -s http://localhost:8000/health > /dev/null; then
        echo "✅ FastAPI server started successfully"
    else
        echo "❌ Failed to start FastAPI server. Check server.log for details."
        exit 1
    fi
fi

echo ""
echo "🧪 Testing Customer Segmentation Training"
echo "=========================================="

# Test customer segmentation with test data
echo "📊 Training customer segmentation model with test data..."
curl -X POST "http://localhost:8000/upload-train-segmentation" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@training_data/test_segmentation.csv" \
  -F "num_clusters=3" \
  -w "\n⏱️  Response Time: %{time_total}s\n" \
  -o segmentation_test_result.json

if [ $? -eq 0 ]; then
    echo "✅ Customer segmentation test completed successfully"
    echo "📁 Results saved to: segmentation_test_result.json"
else
    echo "❌ Customer segmentation test failed"
fi

echo ""
echo "🔮 Testing Sales Forecasting Training"
echo "====================================="

# Test sales forecasting with test data
echo "📈 Training sales forecasting model with test data..."
curl -X POST "http://localhost:8000/upload-train-forecast" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@training_data/test_forecast.csv" \
  -F "horizon_days=7" \
  -w "\n⏱️  Response Time: %{time_total}s\n" \
  -o forecast_test_result.json

if [ $? -eq 0 ]; then
    echo "✅ Sales forecasting test completed successfully"
    echo "📁 Results saved to: forecast_test_result.json"
else
    echo "❌ Sales forecasting test failed"
fi

echo ""
echo "🎯 Training with Full Dataset"
echo "============================="

# Train with comprehensive dataset
echo "🏋️  Training customer segmentation with comprehensive dataset..."
curl -X POST "http://localhost:8000/upload-train-segmentation" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@training_data/customer_segmentation_training.csv" \
  -F "num_clusters=5" \
  -w "\n⏱️  Response Time: %{time_total}s\n" \
  -o segmentation_full_result.json

if [ $? -eq 0 ]; then
    echo "✅ Comprehensive customer segmentation training completed"
    echo "📁 Results saved to: segmentation_full_result.json"
else
    echo "❌ Comprehensive customer segmentation training failed"
fi

echo ""
echo "📊 Training sales forecasting with comprehensive dataset..."
curl -X POST "http://localhost:8000/upload-train-forecast" \
  -H "Content-Type: multipart/form-data" \
  -F "file=@training_data/sales_forecasting_training.csv" \
  -F "horizon_days=30" \
  -w "\n⏱️  Response Time: %{time_total}s\n" \
  -o forecast_full_result.json

if [ $? -eq 0 ]; then
    echo "✅ Comprehensive sales forecasting training completed"
    echo "📁 Results saved to: forecast_full_result.json"
else
    echo "❌ Comprehensive sales forecasting training failed"
fi

echo ""
echo "🔍 Checking trained models"
echo "========================="

# Check if models were created
if [ -f "models/customer_segments_model.joblib" ]; then
    echo "✅ Customer segmentation model created successfully"
    ls -la models/customer_segments_model.joblib
else
    echo "❌ Customer segmentation model not found"
fi

if [ -f "models/sales_forecast_model.joblib" ]; then
    echo "✅ Sales forecasting model created successfully"
    ls -la models/sales_forecast_model.joblib
else
    echo "❌ Sales forecasting model not found"
fi

echo ""
echo "📊 Model Training Summary"
echo "======================="

if [ -f "segmentation_test_result.json" ]; then
    echo "🎯 Customer Segmentation Test Results:"
    cat segmentation_test_result.json | python -m json.tool | head -20
    echo "..."
fi

if [ -f "forecast_test_result.json" ]; then
    echo ""
    echo "📈 Sales Forecasting Test Results:"
    cat forecast_test_result.json | python -m json.tool | head -20
    echo "..."
fi

echo ""
echo "🎉 Training completed! Check the result files for detailed outputs."
echo "💡 Models are saved in the 'models/' directory and ready for use."
