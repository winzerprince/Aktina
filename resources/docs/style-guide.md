# UI Component Style Guide

## Overview
This document defines the standardized styling rules for all Aktina SCM UI components. All new and updated components should follow these guidelines to ensure consistency across the application.

## Color Palette

### Primary Colors
- Primary: Blue (#0ea5e9)
  - Used for primary actions, buttons, links, and focus states
  - Use appropriate shade (500-700) for different states

### Secondary Colors
- Secondary: Green (#10b981) 
  - Used for secondary actions, success states, and positive indicators
  - Use appropriate shade (500-700) for different states

### State Colors
- Success: Green (#22c55e)
- Warning: Yellow (#f59e0b)
- Danger: Red (#ef4444)
- Neutral: Gray (#737373)

### Dark Mode Colors
- Background: Dark Gray (#262626)
- Text: Light Gray (#f5f5f5)
- Borders: Medium Gray (#525252)

## Typography

### Font Family
- Primary: 'Instrument Sans', sans-serif
- System fallbacks for better performance

### Font Sizes
- xs: 0.75rem (12px)
- sm: 0.875rem (14px)
- base: 1rem (16px)
- lg: 1.125rem (18px)
- xl: 1.25rem (20px)
- 2xl: 1.5rem (24px)
- 3xl: 1.875rem (30px)
- 4xl: 2.25rem (36px)

### Font Weights
- Regular: 400
- Medium: 500
- Bold: 700

## Spacing System
- Use Tailwind's built-in spacing scale
- For custom spacing, define in the tailwind.config.js

## Component Guidelines

### Buttons
- Primary Action: Use primary color, prominent position
- Secondary Action: Use secondary/outline variants
- Destructive Action: Use danger variant
- Text Button: For low-emphasis actions
- Button Sizes: xs, sm, md (default), lg, xl
- Always use proper focus states for accessibility

### Forms
- Consistent input sizes and padding
- Clear label positioning (above inputs)
- Proper error state styling
- Required field indicators consistent
- Help text with proper styling

### Cards
- Consistent padding (p-6)
- Consistent rounded corners (rounded-lg)
- Proper header/footer separation
- Consistent shadow (shadow-sm to shadow-md)

### Tables
- Consistent header styling (bg-neutral-50)
- Alternating row colors for better readability
- Proper cell padding (px-6 py-4)
- Responsive behavior on small screens

### Alerts & Notifications
- Consistent color coding by type
- Proper icons for different alert types
- Dismissible when appropriate
- Accessible contrast ratios

### Dashboard Components
- Consistent card layout and spacing
- Chart consistency (colors, legends, tooltips)
- Metric display formatting

## Accessibility
- Minimum contrast ratio of 4.5:1 for text
- Proper focus states for all interactive elements
- Properly labeled forms and controls
- Keyboard navigable interface

## Responsive Guidelines
- Mobile-first approach
- Consistent breakpoints:
  - sm: 576px
  - md: 768px 
  - lg: 992px
  - xl: 1200px
  - 2xl: 1320px
