# 🏭 Model Factories in Aktina SCM

Model factories are special classes that generate fake but realistic data for your database models. They're like automated data generators that create test data that looks and feels like real business information.

## 📚 Understanding Factories at Three Levels

### 🧒 **For a 5-Year-Old: The Toy Makers**

Imagine you have a toy factory that makes different kinds of toys. Each factory machine knows exactly how to make one type of toy:

- **UserFactory**: Makes toy people with names, emails, and jobs
- **ProductFactory**: Makes toy phones that look like real phones
- **OrderFactory**: Makes toy shopping receipts with prices and dates

The best part? You can tell the factory "make me 10 toy people" and it makes 10 different toy people, each with their own name and job! No two toys are exactly the same, but they all look realistic.

### 🎓 **For a 21-Year-Old CS Student: Data Generation Automation**

Model factories implement the Factory pattern to generate test data for Laravel models. They're essential for automated testing and development databases.

**Key Concepts:**
- **Factory Pattern**: Encapsulates object creation logic
- **Faker Integration**: Uses Faker library for realistic fake data
- **State Management**: Factories can have different states/configurations
- **Relationship Handling**: Factories can create related models automatically

**How They Work:**
```php
// Example from UserFactory.php
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['admin', 'supplier', 'vendor']),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ];
    }
}

// Usage in tests or seeders
User::factory()->create();           // Creates 1 user
User::factory(10)->create();         // Creates 10 users
User::factory()->admin()->create();  // Creates admin user
```

### 🎯 **For a CS Professor: Advanced Data Generation Patterns**

Model factories in Aktina SCM implement sophisticated data generation strategies that ensure statistical accuracy and business logic compliance:

**Architectural Patterns:**
- **Abstract Factory**: `Factory` base class provides common interface
- **Builder Pattern**: Chainable methods for complex object construction
- **Strategy Pattern**: Different generation strategies for different environments
- **Template Method**: Base factory defines workflow, subclasses implement specifics

**Advanced Features:**
- **Dependent Data Generation**: Factories create related models with referential integrity
- **Business Rule Enforcement**: Generated data respects business constraints
- **Statistical Distributions**: Data follows realistic distribution patterns
- **Trait-Based Composition**: Reusable factory behaviors
- **Conditional Generation**: Data generation based on business logic

## 🏗️ Current Implementation in Aktina

### **File Structure Overview**
```
/database/factories/
├── UserFactory.php              # Base user generation
├── AdminFactory.php             # Admin-specific data
├── SupplierFactory.php          # Supplier companies
├── VendorFactory.php            # Vendor partners
├── ProductFactory.php           # Aktina products
├── OrderFactory.php             # Product/resource orders
├── ApplicationFactory.php       # Vendor applications
├── RetailerFactory.php          # Retail demographics
├── ProductionFactory.php        # Manufacturing data
├── BomFactory.php               # Bill of Materials
├── ResourceFactory.php          # Manufacturing resources
├── RatingFactory.php            # Product ratings
├── ReportFactory.php            # System reports
└── EmployeeFactory.php          # HR employee data
```

### **Key Factories Deep Dive**

#### **UserFactory.php** - Foundation Factory
```php
// Real implementation from UserFactory.php
public function definition(): array
{
    return [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'role' => fake()->randomElement([
            'admin', 'hr_manager', 'production_manager', 
            'supplier', 'vendor', 'retailer'
        ]),
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
        'remember_token' => Str::random(10),
    ];
}
```

#### **ProductFactory.php** - Business Logic Integration
```php
// Creates Aktina smartphone products with realistic specs
public function definition(): array
{
    $products = [
        ['name' => 'Aktina 26 Pro', 'price' => 899.99],
        ['name' => 'Aktina 26 Mini', 'price' => 699.99],
        ['name' => 'Aktina 26 Pro Max', 'price' => 1099.99],
    ];
    
    $product = fake()->randomElement($products);
    
    return [
        'name' => $product['name'],
        'price' => $product['price'],
        'description' => fake()->paragraph(),
        'category' => 'smartphone',
        'owner_id' => User::factory(), // Creates related user
        'stock' => fake()->numberBetween(0, 1000),
        'image_url' => fake()->imageUrl(640, 480, 'technology'),
    ];
}
```

#### **RetailerFactory.php** - ML-Ready Demographics
```php
// Creates demographically diverse data for ML training
public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'city' => fake()->city(),
        'urban_rural_classification' => fake()->randomElement(['urban', 'rural']),
        'customer_age_class' => fake()->randomElement(['youth', 'adult', 'senior']),
        'customer_income_bracket' => fake()->randomElement(['low', 'medium', 'high']),
        'customer_education_level' => fake()->randomElement(['low', 'medium', 'high']),
        'male_female_ratio' => fake()->randomFloat(2, 0.5, 2.0),
        'population' => fake()->numberBetween(1000, 500000),
        'market_penetration' => fake()->randomFloat(2, 0.1, 0.9),
    ];
}
```

#### **ApplicationFactory.php** - Vendor Application Processing
```php
// Creates vendor applications with PDF processing data
public function definition(): array
{
    return [
        'status' => fake()->randomElement([
            'pending', 'scored', 'meeting_scheduled', 
            'meeting_completed', 'approved', 'rejected'
        ]),
        'meeting_schedule' => fake()->optional(0.7)->dateTimeBetween('now', '+1 month'),
        'vendor_id' => Vendor::factory(),
        'pdf_path' => fake()->optional(0.8)->randomElement([
            'storage/applications/app_' . fake()->uuid . '.pdf',
            'storage/applications/vendor_' . fake()->uuid . '.pdf',
        ]),
        'java_processed' => fake()->boolean(70), // 70% processed
        'java_score' => fake()->optional(0.7)->numberBetween(0, 100),
        'java_analysis' => fake()->optional(0.7)->paragraph(),
    ];
}
```

### **Factory States and Traits**

#### **Factory States** (Conditional Generation)
```php
// In UserFactory.php
public function admin(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
}

public function supplier(): static
{
    return $this->state(fn (array $attributes) => [
        'role' => 'supplier',
        'email_verified_at' => now(),
    ]);
}

// Usage
User::factory()->admin()->create();    // Creates admin user
User::factory()->supplier()->create(); // Creates supplier user
```

#### **Relationship Handling**
```php
// In OrderFactory.php
public function definition(): array
{
    return [
        'buyer_id' => User::factory(),     // Creates related user
        'seller_id' => User::factory(),    // Creates another user
        'product_id' => Product::factory(), // Creates related product
        'quantity' => fake()->numberBetween(1, 100),
        'total_price' => fake()->randomFloat(2, 10, 10000),
        'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered']),
        'order_date' => fake()->dateTimeBetween('-1 year', 'now'),
    ];
}
```

## 🔧 Usage Patterns

### **Basic Usage**
```php
// Create single model
$user = User::factory()->create();

// Create multiple models
$users = User::factory(10)->create();

// Create without saving to database
$user = User::factory()->make();
```

### **With Relationships**
```php
// Create user with related supplier
$user = User::factory()
    ->has(Supplier::factory())
    ->create();

// Create supplier with specific user
$supplier = Supplier::factory()
    ->for(User::factory()->supplier())
    ->create();
```

### **With States**
```php
// Create pending application
$application = Application::factory()
    ->pending()
    ->create();

// Create approved application with meeting
$application = Application::factory()
    ->approved()
    ->withMeeting()
    ->create();
```

## 🎯 Business Context

### **Supply Chain Specific Generation**
- **Role-Based Users**: Different factory states for each SCM role
- **Product Specifications**: Realistic Aktina smartphone data
- **Demographic Accuracy**: Retailer demographics for ML training
- **Order Patterns**: Realistic order distributions and statuses
- **Resource Management**: Manufacturing resources with proper constraints

### **ML Training Data Quality**
- **Customer Segmentation**: Statistically accurate demographic distributions
- **Sales Forecasting**: Historical patterns with seasonal variations
- **Inventory Optimization**: Resource usage patterns
- **Quality Scoring**: Vendor application scoring distributions

## 🔄 Interconnections

**Factories → Models → Database**
1. **Factory Classes** define data generation rules
2. **Faker Library** provides realistic fake data
3. **Model Relationships** are automatically handled
4. **Database Constraints** are respected during generation
5. **Business Rules** are enforced in generated data

**Factory Dependencies:**
```
UserFactory → (AdminFactory, SupplierFactory, VendorFactory, etc.)
ProductFactory → BomFactory → ResourceFactory
OrderFactory → (UserFactory, ProductFactory)
ApplicationFactory → VendorFactory → UserFactory
```

## 🎨 Best Practices Implemented

1. **Realistic Data**: Uses business-appropriate fake data, not random strings
2. **Relationship Management**: Automatically creates related models
3. **State Management**: Different factory states for different scenarios
4. **Performance**: Efficient bulk creation patterns
5. **Consistency**: Standardized data patterns across all factories
6. **Testing Support**: Factories designed for comprehensive testing

## 🚀 Advanced Features

### **Custom Factory Methods**
```php
// In RetailerFactory.php
public function urban(): static
{
    return $this->state(fn (array $attributes) => [
        'urban_rural_classification' => 'urban',
        'population' => fake()->numberBetween(50000, 500000),
        'market_penetration' => fake()->randomFloat(2, 0.3, 0.9),
    ]);
}

public function rural(): static
{
    return $this->state(fn (array $attributes) => [
        'urban_rural_classification' => 'rural',
        'population' => fake()->numberBetween(1000, 49999),
        'market_penetration' => fake()->randomFloat(2, 0.1, 0.5),
    ]);
}
```

### **Callback Integration**
```php
// In ApplicationFactory.php
public function configure(): static
{
    return $this->afterCreating(function (Application $application) {
        // Automatically create related rating if approved
        if ($application->status === 'approved') {
            Rating::factory()->create([
                'vendor_id' => $application->vendor_id,
                'score' => fake()->numberBetween(70, 100),
            ]);
        }
    });
}
```

## 🛠️ Integration with Testing

Factories are extensively used in Aktina's testing infrastructure:

```php
// Feature test example
class OrderManagementTest extends TestCase
{
    public function test_user_can_create_order(): void
    {
        $user = User::factory()->retailer()->create();
        $product = Product::factory()->create();
        
        $response = $this->actingAs($user)->post('/orders', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'buyer_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
```

## 🎯 Future Enhancements

- **Temporal Data**: Time-series data generation for forecasting
- **Geospatial Data**: Location-based data for logistics optimization
- **Constraint Validation**: Enhanced business rule enforcement
- **Data Versioning**: Historical data generation for trend analysis
- **Performance Optimization**: Bulk generation optimization

Model factories in Aktina SCM provide the foundation for reliable testing, consistent development environments, and high-quality ML training data by generating realistic, business-compliant data that reflects real-world supply chain operations.
