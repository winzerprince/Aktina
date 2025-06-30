# Modern UI Components - Aktina SCM

This document describes the modern HTML + Tailwind CSS components that replace Mary UI components in the Aktina Supply Chain Management system.

## Design Philosophy

The new components follow modern SaaS dashboard design principles:
- Clean, minimal aesthetics
- Consistent spacing and typography
- Smooth animations and transitions
- Accessible color contrasts
- Responsive design patterns
- Modern gradient effects and shadows

## Component Library

### Layout Components

#### `modern-dashboard.blade.php`
A complete dashboard layout with header, main content area, and footer.

**Usage:**
```blade
<x-layouts.modern-dashboard>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:subtitle>Welcome to your dashboard</x-slot:subtitle>
    <x-slot:actions>
        <x-ui.modern-button variant="primary">Add New</x-ui.modern-button>
    </x-slot:actions>
    
    <!-- Your content here -->
</x-layouts.modern-dashboard>
```

### UI Components

#### `modern-button.blade.php`
Versatile button component with multiple variants and states.

**Props:**
- `variant`: primary, secondary, success, warning, danger, ghost
- `size`: sm, md, lg
- `disabled`: boolean
- `loading`: boolean
- `icon`: SVG path string
- `iconPosition`: left, right

**Usage:**
```blade
<x-ui.modern-button variant="primary" size="lg" loading="true">
    Save Changes
</x-ui.modern-button>
```

#### `modern-card.blade.php`
Flexible card component with optional header and actions.

**Props:**
- `title`: string
- `subtitle`: string
- `actions`: slot
- `padding`: none, sm, default, lg
- `shadow`: none, sm, default, md, lg

**Usage:**
```blade
<x-ui.modern-card title="Order Details" subtitle="Review order information">
    <x-slot:actions>
        <x-ui.modern-button variant="ghost" size="sm">Edit</x-ui.modern-button>
    </x-slot:actions>
    
    <!-- Card content -->
</x-ui.modern-card>
```

#### `modern-badge.blade.php`
Status badges with color variants.

**Props:**
- `variant`: default, primary, secondary, success, warning, danger, info
- `size`: sm, md, lg
- `dot`: boolean (adds status dot)

**Usage:**
```blade
<x-ui.modern-badge variant="success" dot="true">Active</x-ui.modern-badge>
```

#### `modern-table.blade.php`
Clean, responsive table component.

**Props:**
- `headers`: array of column headers
- `striped`: boolean
- `hoverable`: boolean

**Usage:**
```blade
<x-ui.modern-table :headers="['Name', 'Status', 'Date', 'Actions']" striped hoverable>
    <tr>
        <td class="px-6 py-4 whitespace-nowrap">...</td>
    </tr>
</x-ui.modern-table>
```

#### `modern-input.blade.php`
Form input with label, validation, and icon support.

**Props:**
- `label`: string
- `error`: string
- `helpText`: string
- `required`: boolean
- `icon`: SVG path string
- `iconPosition`: left, right

**Usage:**
```blade
<x-ui.modern-input 
    label="Email Address" 
    required="true"
    icon="<path d='M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207'/>"
    placeholder="Enter your email"
/>
```

#### `modern-modal.blade.php`
Accessible modal dialog with Alpine.js integration.

**Props:**
- `name`: string (unique identifier)
- `show`: boolean
- `maxWidth`: sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl

**Usage:**
```blade
<x-ui.modern-modal name="confirm-delete" maxWidth="md">
    <div class="p-6">
        <h3 class="text-lg font-medium">Confirm Delete</h3>
        <p class="mt-2 text-sm text-gray-500">Are you sure?</p>
        <div class="mt-4 flex space-x-2">
            <x-ui.modern-button variant="danger">Delete</x-ui.modern-button>
            <x-ui.modern-button variant="ghost" x-on:click="$dispatch('close-modal', 'confirm-delete')">Cancel</x-ui.modern-button>
        </div>
    </div>
</x-ui.modern-modal>
```

## Color Scheme

The components use a consistent color palette:

- **Primary**: Blue (#2563EB) - Main actions, links
- **Success**: Emerald (#059669) - Success states, completed items
- **Warning**: Amber (#D97706) - Pending states, warnings
- **Danger**: Red (#DC2626) - Errors, destructive actions
- **Info**: Indigo (#4F46E5) - Information, in-progress states
- **Gray**: Various shades for neutral content

## Icons

Components use Heroicons (outline style) for consistency. Common icons include:
- Magnifying glass for search
- Clock for pending states
- Check circle for completed states
- Exclamation triangle for warnings
- Plus for add actions

## Responsive Design

All components are mobile-first and responsive:
- Grid layouts adapt to screen size
- Tables scroll horizontally on mobile
- Buttons stack vertically on small screens
- Text sizes adjust appropriately

## Migration from Mary UI

Mary UI components have been replaced as follows:

| Mary UI Component | New Component | Notes |
|-------------------|---------------|-------|
| `x-mary-button` | `x-ui.modern-button` | Similar API, improved styling |
| `x-mary-card` | `x-ui.modern-card` | Enhanced with slots for actions |
| `x-mary-badge` | `x-ui.modern-badge` | More color variants |
| `x-mary-table` | `x-ui.modern-table` | Better responsive behavior |
| `x-mary-select` | Native HTML select | Styled with Tailwind |
| `x-mary-icon` | SVG icons | Direct SVG usage for better performance |

## Performance Benefits

- **Smaller bundle size**: No Mary UI JavaScript/CSS dependencies
- **Better performance**: Direct HTML/CSS instead of component overhead
- **More customizable**: Full control over styling and behavior
- **Modern design**: Updated to current design trends
