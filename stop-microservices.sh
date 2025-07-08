#!/bin/bash

echo "Stopping microservices..."

# Stop ML service by PID file if available
if [ -f /tmp/aktina_ml.pid ]; then
    ML_PID=$(cat /tmp/aktina_ml.pid)
    echo "Stopping ML service (PID: $ML_PID)..."
    kill $ML_PID 2>/dev/null || true
    rm /tmp/aktina_ml.pid
else
    echo "No ML service PID file found, attempting to kill by process name..."
    pkill -f "uvicorn app:app" 2>/dev/null || true
fi

# Stop Java service by PID file if available
if [ -f /tmp/aktina_java.pid ]; then
    JAVA_PID=$(cat /tmp/aktina_java.pid)
    echo "Stopping Java service (PID: $JAVA_PID)..."
    kill $JAVA_PID 2>/dev/null || true
    rm /tmp/aktina_java.pid
else
    echo "No Java service PID file found, attempting to kill by process name..."
    pkill -f "spring-boot:run" 2>/dev/null || true
fi

echo "Microservices stopped successfully!"
