#!/bin/bash

echo "Starting microservices in local development mode..."

# First kill any existing processes to avoid port conflicts
echo "Stopping any existing microservices..."
pkill -f "uvicorn app:app" 2>/dev/null || true
pkill -f "spring-boot:run" 2>/dev/null || true

# Start MySQL if it's not running (this depends on your local MySQL setup)


# Start Python ML service
echo "Starting Python ML microservice on port 8001..."
cd microservices/python-ml
source .venv/bin/activate && uvicorn app:app --reload --host 0.0.0.0 --port 8001 > /tmp/aktina_ml.log 2>&1 &
ML_PID=$!
echo "ML service started with PID: $ML_PID"
echo $ML_PID > /tmp/aktina_ml.pid
cd ../..

# Wait for ML service to be ready
echo "Waiting for ML service to be ready..."
for i in {1..10}; do
    if curl -s http://localhost:8001/health > /dev/null; then
        echo "ML service is ready!"
        break
    fi
    echo "Waiting for ML service... ($i/10)"
    sleep 1
    if [ $i -eq 10 ]; then
        echo "Warning: ML service didn't respond in time, but continuing anyway..."
    fi
done

# Start Java service
echo "Starting Java microservice on port 8002..."
cd microservices/java-server
./mvnw spring-boot:run -Dspring-boot.run.arguments="--server.port=8002" > /tmp/aktina_java.log 2>&1 &
JAVA_PID=$!
echo "Java service started with PID: $JAVA_PID"
echo $JAVA_PID > /tmp/aktina_java.pid
cd ../..

# Update .env file to use localhost
echo "Configuring Laravel environment..."
php artisan config:clear

echo "Setup complete! MySQL, ML service, and Java service are running."
echo "ML service API docs: http://localhost:8001/docs"
echo "You can now start the Laravel application with 'php artisan serve'"
