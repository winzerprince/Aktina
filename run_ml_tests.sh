#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}===== Aktina ML Microservice Test Runner =====${NC}"
echo -e "${YELLOW}NOTE: Some tests may need conversion from Pest to PHPUnit style${NC}"
echo -e "${YELLOW}Python tests require Python 3.8-3.10 environment${NC}"

# Function to run Laravel tests
run_laravel_tests() {
    echo -e "\n${BLUE}Running Laravel Tests...${NC}"

    echo -e "\n${BLUE}Running Unit Tests...${NC}"
    php artisan test --filter=MLServiceTest
    php artisan test --filter=RefreshMLPredictionsTest
    # Skip MLRepositoryTest and MLDataRepositoryTest as they need conversion to PHPUnit style
    # php artisan test --filter=MLRepositoryTest
    # php artisan test --filter=MLDataRepositoryTest

    echo -e "\n${BLUE}Running Feature Tests...${NC}"
    php artisan test --filter=CustomerSegmentationTest
    php artisan test --filter=SalesPredictionTest
}

# Function to run Python tests
run_python_tests() {
    echo -e "\n${BLUE}Running Python ML Microservice Tests...${NC}"
    cd microservices/python-ml

    # Check if Python virtual environment exists, if not create it
    if [ ! -d ".venv" ]; then
        echo -e "${BLUE}Creating Python virtual environment...${NC}"
        python -m venv .venv
    fi

    # Activate virtual environment
    source .venv/bin/activate

    # Install requirements
    echo -e "${BLUE}Installing dependencies...${NC}"
    pip install -r requirements.txt

    # Run tests
    echo -e "${BLUE}Running tests...${NC}"
    pytest test_app.py -v

    # Deactivate virtual environment
    deactivate

    # Return to project root
    cd ../..
}

# Function to run all tests
run_all_tests() {
    run_laravel_tests
    run_python_tests

    echo -e "\n${GREEN}All tests completed!${NC}"
}

# Check command line arguments
if [ "$1" == "laravel" ]; then
    run_laravel_tests
elif [ "$1" == "python" ]; then
    run_python_tests
else
    run_all_tests
fi
