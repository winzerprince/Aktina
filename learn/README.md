# Aktina SCM Learning Library 📚

Welcome to the comprehensive learning library for the Aktina Supply Chain Management System. This library provides in-depth explanations of every architectural component, designed to help learners understand the system from multiple perspectives.

## 📋 Table of Contents

### 🏗️ Architecture Components
- [**Controllers**](./controllers/) - HTTP request handling and routing
- [**Services**](./services/) - Business logic and complex operations
- [**Repositories**](./repositories/) - Data access and database operations
- [**Jobs**](./jobs/) - Background tasks and async processing

### 🗄️ Database Components
- [**Migrations**](./migrations/) - Database schema definitions and changes
- [**Seeders**](./seeders/) - Test data generation and database population
- [**Factories**](./factories/) - Model instance creation for testing

### 🧪 Testing
- [**Tests**](./tests/) - Unit tests, feature tests, and testing strategies

### 🔧 Microservices
- [**Java Server**](./java-server/) - Spring Boot microservice for vendor processing
- [**Python ML**](./python-ml/) - Machine learning microservice for predictions

## 🎯 Learning Approach

Each section contains explanations at three different levels:

### 👶 **5-Year-Old Level** 
Simple analogies and basic concepts using everyday language

### 🎓 **CS Student Level**
Technical explanations with code examples and best practices

### 👨‍🏫 **CS Professor Level**
Deep architectural analysis, design patterns, and system engineering concepts

## 🌟 Key Features of This System

### **Architecture Pattern**
- **Service-Repository Pattern**: Clean separation of concerns
- **Dependency Injection**: Modular and testable components
- **Event-Driven Architecture**: Real-time updates and notifications

### **Technology Stack**
- **Backend**: Laravel 11 with PHP 8.3
- **Frontend**: Blade templates + Livewire + Tailwind CSS
- **Database**: MySQL with Redis caching
- **Microservices**: Java Spring Boot + Python FastAPI
- **Charts**: ApexCharts for data visualization

### **Business Domain**
Aktina Technologies supply chain management including:
- **Multi-role System**: Admin, HR Manager, Production Manager, Supplier, Vendor, Retailer
- **Product Management**: Aktina smartphone products (26 Pro, 26 Mini, 26 Pro Max)
- **Order Processing**: Product orders and resource orders
- **Inventory Management**: Resources, BOMs, and warehouse operations
- **ML Analytics**: Customer segmentation and sales forecasting

## 🔄 System Flow

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Controllers   │───▶│    Services     │───▶│  Repositories   │
│ (HTTP Requests) │    │ (Business Logic)│    │ (Data Access)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                       │                       │
        │              ┌─────────────────┐              │
        └─────────────▶│      Jobs       │◀─────────────┘
                       │ (Background)    │
                       └─────────────────┘
```

## 🎯 Learning Path Recommendations

### **For Beginners**
1. Start with [Migrations](./migrations/) to understand database structure
2. Learn [Factories](./factories/) to see how data is created
3. Explore [Controllers](./controllers/) for request handling
4. Study [Services](./services/) for business logic

### **For Intermediate Learners**
1. Deep dive into [Services](./services/) and [Repositories](./repositories/)
2. Understand [Jobs](./jobs/) for background processing
3. Study [Tests](./tests/) for quality assurance
4. Explore microservices architecture

### **For Advanced Learners**
1. Analyze complete system architecture
2. Study microservices integration patterns
3. Understand ML pipeline implementation
4. Explore performance optimization strategies

## 📁 Directory Structure Reference

```
learn/
├── controllers/          # HTTP request handling
├── services/            # Business logic layer
├── repositories/        # Data access layer
├── jobs/               # Background tasks
├── migrations/         # Database schema
├── seeders/           # Test data generation
├── factories/         # Model factories
├── tests/             # Testing strategies
├── java-server/       # Java microservice
└── python-ml/         # ML microservice
```

## 🚀 Getting Started

1. **Choose your learning level** (5-year-old, CS student, or professor)
2. **Pick a component** from the table of contents
3. **Follow the explanations** from basic to advanced
4. **Check the actual code** in the referenced files
5. **Try the examples** and run the tests

## 📞 Support

Each section includes:
- **File references** to actual project files
- **Code examples** with explanations
- **Interconnection details** showing how components work together
- **Best practices** and design patterns used

Happy learning! 🎓
