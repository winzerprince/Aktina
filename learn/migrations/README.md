# Migrations in Aktina SCM 📊

Migrations are version control for your database, defining the schema structure and evolution over time.

## 📍 Location in Project
```
database/migrations/
├── 0001_01_01_000000_create_users_table.php
├── 0001_01_01_000001_create_cache_table.php
├── 0001_01_01_000002_create_jobs_table.php
├── 2025_06_20_123237_create_resources_table.php
├── 2025_06_20_124448_create_products_table.php
├── 2025_06_20_125355_create_productions_table.php
├── 2025_06_20_130641_create_boms_table.php
├── 2025_06_20_141437_create_suppliers_table.php
├── 2025_06_20_141857_create_production_managers_table.php
├── 2025_06_20_141927_create_admins_table.php
├── 2025_06_20_142006_create_hr_managers_table.php
├── 2025_06_20_142022_create_vendors_table.php
├── 2025_06_20_143020_create_retailers_table.php
├── 2025_06_21_040530_create_ratings_table.php
├── 2025_06_21_040727_create_applications_table.php
├── 2025_06_21_041122_create_orders_table.php
├── 2025_06_21_050000_create_retailer_listings_table.php
├── 2025_06_23_082315_create_foreign_keys_production.php
├── 2025_06_26_071351_rename_tables_to_plural.php
├── 2025_06_26_071403_create_bom_resource_pivot_table.php
├── 2025_06_26_071445_update_relationships_structure.php
├── 2025_06_30_090000_create_reports_table.php
├── 2025_06_30_113912_create_employees_table.php
├── 2025_06_30_114217_create_resource_orders_table.php
├── 2025_06_30_152547_update_applications_table_add_score_and_meeting_fields.php
├── 2025_06_30_152645_create_notifications_table.php
├── 2025_06_30_155221_add_demographics_fields_to_retailers_table.php
├── 2025_06_30_181404_add_is_verified_column_to_users_table.php
├── 2025_06_30_181919_create_laravel_notifications_table.php
├── 2025_07_02_122053_create_conversations_table.php
├── 2025_07_02_122106_create_messages_table.php
├── 2025_07_02_122120_create_message_files_table.php
├── 2025_07_02_122829_create_warehouses_table.php
├── 2025_07_02_122838_create_inventory_alerts_table.php
├── 2025_07_02_122846_create_inventory_movements_table.php
├── 2025_07_02_122855_add_warehouse_fields_to_resources_table.php
├── 2025_07_02_123331_create_daily_metrics_table.php
├── 2025_07_02_123441_create_sales_analytics_table.php
├── 2025_07_02_123454_create_production_metrics_table.php
├── 2025_07_02_123505_create_system_metrics_table.php
├── 2025_07_02_125000_enhance_orders_table.php
└── 2025_07_02_183202_create_system_performances_table.php
```

## 🎯 Three-Level Explanations

### 👶 **5-Year-Old Level: Building Instructions**

Think of migrations like building instructions for a toy castle:

- **Each instruction sheet** (migration) tells you exactly what to build next
- **You follow them in order** (timestamps) so the castle is built correctly
- **If you make a mistake**, you can take apart pieces and rebuild them (rollback)
- **Everyone building the same castle** (developers) follows the same instructions
- **New rooms can be added** (new migrations) without breaking the existing castle

When you and your friends all follow the same instructions, everyone ends up with the same beautiful castle!

### 🎓 **CS Student Level: Database Schema Version Control**

Migrations provide version control for database schema:

```php
// Example: create_orders_table.php
class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled']);
            $table->json('items');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index('seller_id');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}
```

**Key Features:**
- **Timestamped Execution**: Ensures consistent order across environments
- **Reversible Operations**: `up()` and `down()` methods for rollbacks
- **Schema Blueprint**: Fluent API for defining table structure
- **Foreign Key Constraints**: Maintain data integrity

### 👨‍🏫 **CS Professor Level: Database Evolution Management**

Migrations implement **Database Refactoring** and **Evolutionary Database Design**:

```php
class EnhanceOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add new columns with proper constraints
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium')
                  ->after('status');
                  
            $table->foreignId('warehouse_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null')
                  ->after('seller_id');
                  
            $table->timestamp('fulfillment_started_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            // Composite indexes for complex queries
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['warehouse_id', 'status']);
        });
    }
    
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'priority', 'created_at']);
            $table->dropIndex(['warehouse_id', 'status']);
            $table->dropConstrainedForeignId('warehouse_id');
            $table->dropColumn([
                'priority', 
                'fulfillment_started_at', 
                'shipped_at', 
                'delivered_at'
            ]);
        });
    }
}
```

## 🏗️ Architecture Patterns Used

### **1. Builder Pattern**
Schema builder uses fluent interface for table definition:

```php
Schema::create('retailers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
    // Demographics fields for ML
    $table->decimal('male_female_ratio', 4, 2)->default(1.0);
    $table->string('city');
    $table->enum('urban_rural_classification', ['urban', 'suburban', 'rural']);
    $table->enum('customer_age_class', ['child', 'teenager', 'youth', 'adult', 'senior']);
    $table->enum('customer_income_bracket', ['low', 'medium', 'high']);
    $table->enum('customer_education_level', ['low', 'mid', 'high']);
    
    $table->timestamps();
    
    // Indexes for analytics queries
    $table->index(['city', 'urban_rural_classification']);
    $table->index('customer_income_bracket');
});
```

### **2. Command Pattern**
Each migration encapsulates a database schema change command:

```php
// Each migration is a command that can be executed or undone
class CreateBomResourcePivotTable extends Migration
{
    public function up(): void
    {
        Schema::create('bom_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_required');
            $table->timestamps();
            
            // Ensure uniqueness
            $table->unique(['bom_id', 'resource_id']);
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('bom_resource');
    }
}
```

## 📋 Actual Implementation Examples

### **Users Table (Foundation)**
```php
// File: 0001_01_01_000000_create_users_table.php
class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role'); // admin, vendor, retailer, etc.
            $table->string('company_name')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->rememberToken();
            $table->timestamps();
            
            // Performance indexes
            $table->index('role');
            $table->index(['role', 'is_verified']);
        });
    }
}
```

### **Complex Relationships (Orders)**
```php
// File: 2025_06_21_041122_create_orders_table.php
class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade')
                  ->comment('Order buyer');
                  
            $table->foreignId('seller_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Order seller');
            
            // Order details
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', [
                'pending', 'approved', 'processing', 
                'completed', 'cancelled', 'refunded'
            ])->default('pending');
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium');
                  
            $table->json('items')->comment('Order items with quantities and prices');
            $table->text('notes')->nullable();
            
            // Fulfillment tracking
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();
            
            // Performance indexes
            $table->index(['status', 'created_at']);
            $table->index(['seller_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('priority');
        });
    }
}
```

### **Analytics Tables**
```php
// File: 2025_07_02_123441_create_sales_analytics_table.php
class CreateSalesAnalyticsTable extends Migration
{
    public function up(): void
    {
        Schema::create('sales_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('period_type'); // daily, weekly, monthly
            
            // Metrics
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->integer('new_customers')->default(0);
            $table->integer('returning_customers')->default(0);
            
            // Segmentation
            $table->string('segment')->nullable(); // by region, product, etc.
            $table->json('metadata')->nullable(); // additional analytics data
            
            $table->timestamps();
            
            // Unique constraint and indexes
            $table->unique(['date', 'period_type', 'segment']);
            $table->index(['period_type', 'date']);
            $table->index('segment');
        });
    }
}
```

## 🔗 Interconnections

### **Foreign Key Relationships**
```php
// Products table references multiple entities
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('model')->unique();
    
    // Relationships with other tables
    $table->foreignId('created_by')
          ->constrained('users')
          ->onDelete('restrict'); // Don't delete if referenced
          
    $table->foreignId('category_id')
          ->nullable()
          ->constrained()
          ->onDelete('set null'); // Set null if category deleted
});
```

### **Pivot Tables for Many-to-Many**
```php
// BOM (Bill of Materials) to Resources relationship
Schema::create('bom_resource', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bom_id')->constrained()->onDelete('cascade');
    $table->foreignId('resource_id')->constrained()->onDelete('cascade');
    $table->integer('quantity_required');
    $table->decimal('unit_cost', 8, 2)->nullable();
    $table->timestamps();
    
    // Composite unique constraint
    $table->unique(['bom_id', 'resource_id']);
});
```

## 🎯 Best Practices Used

### **1. Descriptive Naming**
```php
// Clear, descriptive migration names
2025_06_30_155221_add_demographics_fields_to_retailers_table.php
2025_07_02_125000_enhance_orders_table.php
2025_07_02_122855_add_warehouse_fields_to_resources_table.php
```

### **2. Proper Indexing**
```php
Schema::table('orders', function (Blueprint $table) {
    // Composite indexes for complex queries
    $table->index(['status', 'created_at']); // Order listing
    $table->index(['seller_id', 'status']);   // Vendor orders
    $table->index(['user_id', 'created_at']); // Customer history
});
```

### **3. Data Integrity**
```php
Schema::create('inventory_movements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('resource_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained(); // Who made the movement
    $table->enum('type', ['purchase', 'sale', 'adjustment', 'transfer']);
    $table->integer('quantity'); // Can be negative for outbound
    $table->decimal('unit_cost', 10, 2)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    
    // Ensure data integrity
    $table->index(['resource_id', 'created_at']);
    $table->index('type');
});
```

## 🔧 Common Patterns

### **1. Additive Changes**
```php
// Adding new columns (safe operation)
class AddDemographicsFieldsToRetailersTable extends Migration
{
    public function up(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->decimal('male_female_ratio', 4, 2)->default(1.0);
            $table->string('city')->nullable();
            $table->enum('urban_rural_classification', ['urban', 'suburban', 'rural'])->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropColumn(['male_female_ratio', 'city', 'urban_rural_classification']);
        });
    }
}
```

### **2. Data Migration**
```php
// Migration with data transformation
class UpdateApplicationsTableAddScoreAndMeetingFields extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->integer('score')->nullable()->after('status');
            $table->timestamp('meeting_schedule')->nullable()->after('score');
        });
        
        // Data migration
        DB::table('applications')
          ->where('status', 'approved')
          ->update(['score' => 85]); // Default score for approved applications
    }
}
```

### **3. Table Renaming/Restructuring**
```php
// Rename tables to follow Laravel conventions
class RenameTableToPlural extends Migration
{
    public function up(): void
    {
        Schema::rename('admin', 'admins');
        Schema::rename('vendor', 'vendors');
        Schema::rename('retailer', 'retailers');
        Schema::rename('supplier', 'suppliers');
    }
    
    public function down(): void
    {
        Schema::rename('admins', 'admin');
        Schema::rename('vendors', 'vendor');
        Schema::rename('retailers', 'retailer');
        Schema::rename('suppliers', 'supplier');
    }
}
```

## 📊 Performance Considerations

### **1. Strategic Indexing**
```php
// Indexes based on query patterns
Schema::create('orders', function (Blueprint $table) {
    // For ORDER BY created_at WHERE status = 'pending'
    $table->index(['status', 'created_at']);
    
    // For filtering by seller
    $table->index('seller_id');
    
    // For customer order history
    $table->index(['user_id', 'created_at']);
});
```

### **2. JSON Column Usage**
```php
// Using JSON for flexible data storage
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->json('specifications'); // Flexible product specs
    $table->json('features')->nullable();
    
    // Can add JSON indexes in newer MySQL versions
    $table->index('specifications->processor'); // MySQL 8.0+
});
```

### **3. Partitioning Considerations**
```php
// Large tables designed for partitioning
Schema::create('system_metrics', function (Blueprint $table) {
    $table->id();
    $table->timestamp('recorded_at'); // Partition key
    $table->string('metric_type');
    $table->decimal('value', 15, 4);
    $table->json('metadata')->nullable();
    
    // Compound index with partition key first
    $table->index(['recorded_at', 'metric_type']);
});
```

Migrations in Aktina SCM provide a robust foundation for database evolution, ensuring consistent schema across environments while maintaining data integrity and performance optimization.
