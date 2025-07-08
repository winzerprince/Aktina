# Communication Feature Implementation Plan

## Overview
This plan outlines the steps needed to implement a WhatsApp-like chat interface for the Aktina SCM system. The communication feature will allow different roles to communicate with each other according to the specified rules:

- Admin, Production Manager, and HR Manager can communicate with each other
- Admin and Production Manager can communicate with Suppliers and Vendors (HR Manager cannot)
- Vendors can communicate with Admin, Production Manager, and their associated Retailers
- Retailers can only communicate with their corresponding Vendor

## Phase 1: Update Models and Services

### Step 1.1: Fix the ConversationRepository
- Fix the `findByUsers` method in `ConversationRepository` to use correct column names
- Fix the `getUserConversations` method to use correct column names
- Add missing `updateLastMessage` method

### Step 1.2: Fix the MessageService
- Update the `MessageService` class to properly handle file uploads
- Add methods for marking messages as read

## Phase 2: Role-based Communication Logic

### Step 2.1: Create Communication Permission Service
- Create a new service to handle role-based communication permissions
- Implement logic for determining which users can communicate with each other based on their roles
- Add methods to check if a user can communicate with another user

### Step 2.2: Update ConversationService
- Add methods to get available contacts based on user role
- Implement logic to retrieve contacts for specific roles

## Phase 3: Livewire Components

### Step 3.1: Create ConversationList Component
- Create a component to list all conversations for the current user
- Show unread message count
- Sort conversations by latest message

### Step 3.2: Create ContactList Component
- Create a component to list available contacts based on user's role
- Implement filtering and searching functionality

### Step 3.3: Create MessageThread Component
- Create a component to display messages in a conversation
- Implement real-time message sending
- Add file upload functionality
- Show message status (sent, delivered, read)

### Step 3.4: Create MessageInput Component
- Create a component for the message input area
- Implement file attachment functionality
- Add send button and keyboard shortcuts

## Phase 4: UI Implementation

### Step 4.1: Update Communication View
- Update the main communication.blade.php view
- Implement a WhatsApp-like layout with contacts on the left and messages on the right
- Add responsive design for mobile devices

### Step 4.2: Create Message Bubble Components
- Create components for sent and received message bubbles
- Include timestamps and status indicators
- Add styling for different message types (text, files)

### Step 4.3: Create File Preview Components
- Implement file preview for image attachments
- Add download buttons for all file types

## Phase 5: Testing and Optimization

### Step 5.1: Test Communication Between Different Roles
- Test all the communication rules between different roles
- Ensure files can be uploaded and downloaded
- Verify unread message counts are accurate

### Step 5.2: Performance Optimization
- Add pagination for message loading
- Optimize database queries
- Cache frequently accessed data
