#!/bin/bash

# Aktina PDF Processing Service Startup Script

echo "Starting Aktina PDF Processing Service..."

# Set JVM options
export JAVA_OPTS="-Xms512m -Xmx1024m -server"

# Set the JAR file path
JAR_FILE="target/java-server-0.0.1-SNAPSHOT.jar"

# Check if JAR exists
if [ ! -f "$JAR_FILE" ]; then
    echo "JAR file not found. Building the application..."
    mvn clean package -DskipTests

    if [ $? -ne 0 ]; then
        echo "Failed to build the application. Exiting."
        exit 1
    fi
fi

# Start the service
echo "Starting service on port 8081..."
java $JAVA_OPTS -jar $JAR_FILE

# Alternative with specific profile
# java $JAVA_OPTS -jar $JAR_FILE --spring.profiles.active=production
