# Aktina Supply Chain Management System Context

Aktina SCM is a Laravel-based supply chain management system for managing the supply chain operations of Aktina Technologies. The system includes the following key features:

1. User management with different roles (admin, hr_manager, production_manager, supplier, vendor, retailer)
2. Product management
3. Order tracking and management
4. Resource and BOM (Bill of Materials) management
5. Production planning and tracking
6. Vendor and supplier management
7. Retailer management and listings

The system follows Laravel best practices, including:
- Service-Repository pattern for code organization
- Eloquent ORM for database interactions
- Blade templates for views
- Livewire for dynamic UI components
- Mary UI for UI elements
- Tailwind CSS for styling

The database schema currently includes separate tables for each role with foreign keys to the users table, as well as tables for products, orders, resources, BOMs, productions, applications, and ratings.

Recent database refactoring improvements include:
1. Added status field to Orders (pending, accepted, complete)
2. Added owner_id to Products to track ownership changes
3. Added demographic fields to Retailers (male-female ratio, city, urban-rural classification, etc.)
4. Created a new Reports table for system-generated reports
5. Enhanced the Application table for PDF processing with Java server integration
6. Standardized all role names using snake_case (admin, hr_manager, production_manager, supplier, vendor, retailer)
7. Moved foreign keys from separate migrations into their respective table migrations
8. Updated controllers, routes, views, factories, and seeders to reflect the new schema and naming conventions
9. Restructured controller namespaces to follow snake_case for consistency
