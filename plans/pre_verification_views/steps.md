# Pre-Verification Views Implementation Plan

## Overview
Implement role-based pre-verification views system where unverified users (except admin) see specific onboarding/verification views before accessing main application features.

## Phase 1: Database & Model Setup

### Step 1.1: Update Applications Table Migration
- Add `score` field (integer, nullable) for Java server scoring
- Add `meeting_notes` field (text, nullable) for admin notes after meeting
- Update status enum to include 'meeting_scheduled', 'meeting_completed'

### Step 1.2: Create Verification Service
- Create `VerificationService` interface and implementation
- Methods: `isUserFullyVerified()`, `getVerificationRequirements()`, `markAsVerified()`
- Handle different verification logic per role

### Step 1.3: Create Application Repository & Service
- `ApplicationRepository` for CRUD operations
- `ApplicationService` for business logic (scoring, status updates, notifications)

## Phase 2: Middleware & Routing

### Step 2.1: Create Role-Based Verification Middleware
- `EnsureRoleVerified` middleware
- Check email verification + role-specific requirements
- Redirect unverified users to appropriate pre-verification views

### Step 2.2: Update Routes Structure
- Create separate route groups for pre-verification views
- Update existing routes with verification middleware
- Add verification status check routes

## Phase 3: Pre-Verification Views Implementation

### Step 3.1: Vendor Pre-Verification Views
- **Application Tab**: PDF upload form, application status tracking, score display
- **Welcome Tab**: Instructions about application process and expectations
- Livewire components for file upload and real-time status updates

### Step 3.2: Retailer Pre-Verification Views  
- **Demographics Form**: All demographic fields with validation
- **Instructions Tab**: Welcome message and form completion guidance
- Convert to editable profile page post-verification

### Step 3.3: Basic Role Pre-Verification Views
- **Supplier Welcome**: Simple welcome tab with verification status
- **Production Manager Welcome**: Instructions and access expectations
- **HR Manager Welcome**: Role-specific welcome content

## Phase 4: Admin Interface Enhancement

### Step 4.1: Vendor Application Management
- Applications list view with scores and status
- Individual application detail view with PDF viewer
- Meeting scheduling interface
- Approval/rejection workflow with audit trail

### Step 4.2: Verification Dashboard
- Overview of all pending verifications by role
- Quick actions for bulk approvals
- Verification statistics and metrics

## Phase 5: Notification System

### Step 5.1: Email Notifications
- Vendor: Application received, scored, meeting scheduled, approved/rejected
- All Roles: Verification completion notification
- Admin: New applications requiring review

### Step 5.2: In-App Notifications
- Real-time status updates
- Verification progress indicators
- Next steps guidance

## Phase 6: UI Components & Styling

### Step 6.1: Modern UI Components
- File upload component with progress
- Status badges and progress indicators
- Notification components
- Form validation styling

### Step 6.2: Responsive Layouts
- Mobile-friendly pre-verification views
- Consistent styling with existing app
- Loading states and error handling

## Phase 7: Integration & Testing

### Step 7.1: Java Server Integration
- PDF processing workflow
- Score calculation and storage
- Error handling for processing failures

### Step 7.2: Validation & Security
- File upload validation (PDF only, size limits)
- Form validation for demographics
- Authorization checks for all views

### Step 7.3: Testing
- Unit tests for services and repositories
- Feature tests for verification workflows
- UI tests for pre-verification views

## Database Schema Changes

### Applications Table Updates:
```sql
ALTER TABLE applications ADD COLUMN score INTEGER NULL;
ALTER TABLE applications ADD COLUMN meeting_notes TEXT NULL;
ALTER TABLE applications MODIFY COLUMN status ENUM('pending', 'scored', 'meeting_scheduled', 'meeting_completed', 'approved', 'rejected');
```

### New Notifications Table:
```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    type VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Key Features by Role

### Vendor (Unverified):
1. PDF Application Upload
2. Application Status Tracking
3. Score Visibility (if processed)
4. Meeting Schedule Display
5. Welcome/Instructions Tab

### Retailer (Unverified):
1. Demographics Form (mandatory)
2. Form Validation & Instructions
3. Progress Indicator
4. Welcome Tab

### Other Roles (Unverified):
1. Simple Welcome Tab
2. Verification Status Display
3. Instructions for Next Steps

### Admin:
1. Application Review Dashboard
2. PDF Viewer & Scoring
3. Meeting Scheduler
4. Approval Workflow
5. Audit Trail View
6. Bulk Verification Actions

## Technical Stack
- Laravel 11 with Eloquent ORM
- Livewire 3 for dynamic components
- HTML + Tailwind CSS for styling
- Service-Repository pattern
- File storage for PDF uploads
- Email queue for notifications
