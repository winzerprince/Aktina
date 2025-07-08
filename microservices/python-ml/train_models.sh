#!/bin/bash

# Helper script to manually train ML models with example CSV files

echo "Aktina SCM ML Model Manual Training Helper"
echo "----------------------------------------"

# Check if the microservice is running
if ! curl -s --head http://localhost:8000/health > /dev/null; then
  echo "❌ Error: ML microservice doesn't seem to be running on port 8000."
  echo "   Please start the microservice first:"
  echo "   $ cd microservices/python-ml"
  echo "   $ uvicorn app:app --reload --port 8000"
  exit 1
fi

# Helper function to train a model
train_model() {
  local endpoint=$1
  local file=$2
  local param_name=$3
  local param_value=$4
  local model_name=$5

  echo -e "\n🔄 Training $model_name model using $file..."

  response=$(curl -s -w "\n%{http_code}" -X POST "http://localhost:8000/$endpoint" \
    -F "file=@$file" \
    -F "$param_name=$param_value")

  status_code=$(echo "$response" | tail -n1)
  response_body=$(echo "$response" | sed '$d')

  if [ "$status_code" -eq 200 ]; then
    echo "✅ Success! $model_name model trained successfully."
    echo -e "\nModel summary:"

    if [ "$endpoint" == "upload-train-segmentation" ]; then
      # Extract and display segment distribution
      segments=$(echo "$response_body" | grep -o '"segment_distribution":{[^}]*}' | sed 's/"segment_distribution":/Segment distribution:/g')
      echo "$segments"
    elif [ "$endpoint" == "upload-train-forecast" ]; then
      # Count forecast points
      forecast_count=$(echo "$response_body" | grep -o '"forecast_dates":\[[^\]]*\]' | grep -o ',' | wc -l)
      forecast_count=$((forecast_count + 1))
      echo "Generated $forecast_count forecast points"
    fi
  else
    echo "❌ Error: Training failed with status code $status_code"
    echo "$response_body"
  fi
}

# Main menu
while true; do
  echo -e "\nPlease select an option:"
  echo "1. Train customer segmentation model with example data"
  echo "2. Train sales forecast model with example data"
  echo "3. Train with custom data file"
  echo "4. Exit"

  read -p "Enter choice [1-4]: " choice

  case $choice in
    1)
      train_model "upload-train-segmentation" "examples/segmentation_template.csv" "num_clusters" "3" "customer segmentation"
      ;;
    2)
      train_model "upload-train-forecast" "examples/forecast_template.csv" "horizon_days" "90" "sales forecast"
      ;;
    3)
      read -p "Enter path to your data file: " file_path
      if [ ! -f "$file_path" ]; then
        echo "❌ Error: File not found at '$file_path'"
        continue
      fi

      read -p "Is this for (1) segmentation or (2) forecast? [1/2]: " model_type

      if [ "$model_type" == "1" ]; then
        read -p "Enter number of clusters [3]: " clusters
        clusters=${clusters:-3}
        train_model "upload-train-segmentation" "$file_path" "num_clusters" "$clusters" "customer segmentation"
      elif [ "$model_type" == "2" ]; then
        read -p "Enter forecast horizon in days [90]: " horizon
        horizon=${horizon:-90}
        train_model "upload-train-forecast" "$file_path" "horizon_days" "$horizon" "sales forecast"
      else
        echo "Invalid choice"
      fi
      ;;
    4)
      echo "Exiting..."
      exit 0
      ;;
    *)
      echo "Invalid option, please try again."
      ;;
  esac
done
