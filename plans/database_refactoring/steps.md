# Database Refactoring Plan

## Phase 1: Analyze and Prepare

1. Analyze existing migrations, models, factories, and seeders
2. Identify foreign key relationships that need to be moved to their respective table migrations
3. Prepare the structure for updated migrations with proper best practices

## Phase 2: Refactor Migrations

1. Update Orders migration to include a status field with 'pending', 'accepted', 'complete' options
2. Update Products migration to include owner_id field as foreign key to users table
3. Update Retailers migration to include demographic fields
4. Create a new Reports migration with necessary fields for system generated reports
5. Update Application migration to include necessary fields for PDF processing
6. Ensure all migrations use snake_case for role names and maintain consistency
7. Remove separate foreign keys migration file and include foreign keys in their respective tables

## Phase 3: Update Factories and Models

1. Update Order factory to accommodate the new status field
2. Update Product factory to include owner_id field
3. Update Retailer factory to include new demographic fields
4. Create a new Report factory
5. Update Application factory to include new fields
6. Update models to reflect the changes in the database schema

## Phase 4: Update Seeders

1. Update OrderSeeder to include different statuses
2. Update ProductSeeder to set owner_id
3. Update RetailerSeeder to include demographic data
4. Create ReportSeeder for generating sample reports
5. Update ApplicationSeeder to include enhanced data
6. Update the DatabaseSeeder to include the new seeders

## Phase 5: Fix Related Views and Resources

1. Identify views and resources that use role names in incorrect format
2. Update views and resources to use snake_case role names consistently
3. Test the changes to ensure they work as expected
