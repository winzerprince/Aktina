# Aktina UI Components Library

This document describes the custom Blade UI components created for the Aktina application, based on the Soft UI Dashboard template with a custom green color scheme.

## Color Scheme

The components use the following primary colors:
- **Primary Dark**: `#044c03`
- **Primary Normal**: `#008800`
- **Primary Light**: `#30cf36`

## Available Components

### 1. Stats Card Component (`x-ui.stats-card`)

A statistics display card with an icon, value, and change indicator.

**Usage:**
```blade
<x-ui.stats-card 
    title="Total Revenue" 
    value="$53,000" 
    change="55%" 
    changeType="positive" 
    icon="money-coins"
    iconBg="success"
/>
```

**Props:**
- `title` (string) - Card title
- `value` (string) - Main value to display
- `change` (string) - Change percentage/amount
- `changeType` (string) - 'positive', 'negative', 'neutral'
- `icon` (string) - Nucleo icon name
- `iconBg` (string) - 'primary', 'secondary', 'success', 'warning', 'danger'

### 2. Button Component (`x-ui.button`)

Customizable button with multiple variants and optional icons.

**Usage:**
```blade
<x-ui.button variant="primary" icon="plus" size="md">
    Add New Item
</x-ui.button>
```

**Props:**
- `variant` (string) - 'primary', 'secondary', 'success', 'warning', 'danger', 'outline'
- `size` (string) - 'sm', 'md', 'lg'
- `type` (string) - Button type (default: 'button')
- `href` (string) - If provided, renders as link
- `icon` (string) - Nucleo icon name
- `iconPosition` (string) - 'left', 'right'
- `loading` (boolean) - Shows spinner
- `disabled` (boolean) - Disables button

### 3. Progress Bar Component (`x-ui.progress-bar`)

Animated progress bar with customizable colors and labels.

**Usage:**
```blade
<x-ui.progress-bar 
    label="Project Progress" 
    :value="75" 
    color="success"
    showPercentage="true"
/>
```

**Props:**
- `value` (int) - Current progress value
- `max` (int) - Maximum value (default: 100)
- `color` (string) - 'primary', 'secondary', 'success', 'warning', 'danger'
- `size` (string) - 'sm', 'md', 'lg'
- `label` (string) - Progress bar label
- `showPercentage` (boolean) - Show percentage text
- `animated` (boolean) - Add pulse animation

### 4. Status Badge Component (`x-ui.status-badge`)

Status indicator badge with predefined status types.

**Usage:**
```blade
<x-ui.status-badge status="online" />
<x-ui.status-badge status="pending" variant="outline" />
```

**Props:**
- `status` (string) - 'online', 'offline', 'pending', 'active', 'inactive', 'success', 'warning', 'danger'
- `variant` (string) - 'gradient', 'solid', 'outline'
- `size` (string) - 'xs', 'sm', 'md', 'lg'

### 5. Data Table Component (`x-ui.data-table`)

Feature-rich data table with search, sorting, and action buttons.

**Usage:**
```blade
<x-ui.data-table
    title="User Management"
    :headers="['Name', 'Email', 'Status', 'Date']"
    :rows="$tableData"
    searchable="true"
    sortable="true"
    pagination="true"
    :rowActions="['edit', 'delete', 'view']"
/>
```

**Props:**
- `headers` (array) - Column headers
- `rows` (array) - Table data rows
- `title` (string) - Table title
- `searchable` (boolean) - Enable search
- `sortable` (boolean) - Enable sorting
- `pagination` (boolean) - Enable pagination
- `actions` (boolean) - Show actions column
- `rowActions` (array) - Available row actions

**Row Data Types:**
- **User**: `['type' => 'user', 'name' => 'John Doe', 'email' => 'john@example.com']`
- **Status**: `['type' => 'status', 'status' => 'online']`
- **Progress**: `['type' => 'progress', 'value' => 75, 'max' => 100]`

### 6. Card Component (`x-ui.card`)

Versatile card container with header, body, and footer sections.

**Usage:**
```blade
<x-ui.card title="Card Title" subtitle="Subtitle text">
    Card content goes here
</x-ui.card>

<!-- Gradient card -->
<x-ui.card variant="gradient" gradientFrom="purple-700" gradientTo="pink-500">
    Gradient card content
</x-ui.card>

<!-- Image background card -->
<x-ui.card variant="image" backgroundImage="/path/to/image.jpg">
    Image overlay content
</x-ui.card>
```

**Props:**
- `title` (string) - Card title
- `subtitle` (string) - Card subtitle
- `padding` (string) - 'none', 'sm', 'normal', 'lg'
- `shadow` (boolean) - Enable shadow
- `border` (boolean) - Enable border
- `variant` (string) - 'default', 'gradient', 'image'
- `backgroundImage` (string) - Background image URL
- `gradientFrom` (string) - Gradient start color
- `gradientTo` (string) - Gradient end color

### 7. Chart Card Component (`x-ui.chart-card`)

Card with integrated Chart.js charts and statistics.

**Usage:**
```blade
<x-ui.chart-card 
    title="Sales Performance" 
    chartId="salesChart"
    chartType="bar"
    description="Monthly sales data"
    :stats="[
        ['label' => 'Revenue', 'value' => '$125K', 'progress' => ['value' => 85, 'max' => 100]],
        ['label' => 'Orders', 'value' => '1.2K', 'progress' => ['value' => 75, 'max' => 100]]
    ]"
/>
```

**Props:**
- `title` (string) - Chart title
- `chartId` (string) - Unique chart element ID
- `chartType` (string) - 'bar', 'line', 'pie', 'doughnut'
- `height` (string) - Chart height in pixels
- `description` (string) - Chart description
- `stats` (array) - Array of statistics with progress bars
- `chartData` (array) - Custom Chart.js data object

### 8. Form Input Component (`x-ui.form-input`)

Form input with label, validation, and icons.

**Usage:**
```blade
<x-ui.form-input 
    label="Email Address" 
    name="email" 
    type="email"
    placeholder="Enter your email"
    icon="email-83"
    required="true"
    error="Invalid email format"
/>

<!-- Textarea -->
<x-ui.form-input 
    label="Message" 
    name="message" 
    type="textarea"
    placeholder="Enter your message"
/>

<!-- Select -->
<x-ui.form-input 
    label="Country" 
    name="country" 
    type="select"
>
    <option value="us">United States</option>
    <option value="ca">Canada</option>
</x-ui.form-input>
```

**Props:**
- `label` (string) - Input label
- `name` (string) - Input name attribute
- `type` (string) - Input type or 'textarea'/'select'
- `placeholder` (string) - Placeholder text
- `required` (boolean) - Required field
- `disabled` (boolean) - Disabled state
- `readonly` (boolean) - Readonly state
- `value` (string) - Input value
- `icon` (string) - Nucleo icon name
- `iconPosition` (string) - 'left', 'right'
- `error` (string) - Error message
- `help` (string) - Help text
- `size` (string) - 'sm', 'md', 'lg'

### 9. Slider Component (`x-ui.slider`)

Range slider input with custom styling.

**Usage:**
```blade
<x-ui.slider 
    label="Priority Level" 
    name="priority"
    :min="1"
    :max="10"
    :value="5"
    color="primary"
    showValue="true"
/>
```

**Props:**
- `label` (string) - Slider label
- `name` (string) - Input name
- `min` (int) - Minimum value
- `max` (int) - Maximum value
- `step` (float) - Step increment
- `value` (int) - Initial value
- `showValue` (boolean) - Display current value
- `color` (string) - 'primary', 'secondary', 'success', 'warning', 'danger'

### 10. Alert Component (`x-ui.alert`)

Notification/alert component with different types.

**Usage:**
```blade
<x-ui.alert type="success" title="Success!" dismissible="true">
    Your data has been successfully saved.
</x-ui.alert>

<x-ui.alert type="warning" title="Warning">
    Please review your settings before proceeding.
</x-ui.alert>
```

**Props:**
- `type` (string) - 'success', 'warning', 'danger', 'info'
- `title` (string) - Alert title
- `dismissible` (boolean) - Show close button
- `icon` (boolean) - Show type icon
- `size` (string) - 'sm', 'md', 'lg'

## Dashboard Examples

### Demo Page
Visit `/components-demo` to see all components in action with examples.

### Role-based Dashboards

The application includes specialized dashboard views for different user roles:

1. **Admin Dashboard** (`/dashboard`) - Financial metrics, system status, comprehensive analytics
2. **Retailer Dashboard** (`/retailer/sales-insights`) - Sales performance, customer insights, order tracking
3. **Default Dashboard** - Basic user interface with task management

### Admin Features
- **Financial Analysis** (`/admin/financial-analysis`) - Revenue trends, cost breakdown, quarterly performance
- **Strategic Insights** - Business intelligence and planning tools

### Retailer Features
- **Sales Insights** (`/retailer/sales-insights`) - Sales analytics, top products, customer behavior
- **Customer Feedback** - Review and rating management
- **Order Placement** - Order management interface

## Dependencies

The components require the following external libraries:

1. **Chart.js** - For chart components
   ```html
   <link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
   ```

2. **Alpine.js** - For interactive components
   ```html
   <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
   ```

3. **Nucleo Icons** - Icon library (included in template)

## Usage Guidelines

1. **Consistent Colors**: Use the defined color scheme (#044c03, #008800, #30cf36) throughout the application
2. **Responsive Design**: All components are built with responsive classes and work on mobile devices
3. **Dark Mode**: Components support dark mode via Tailwind's dark mode classes
4. **Accessibility**: Components include proper ARIA attributes and semantic HTML
5. **Performance**: Components are optimized for performance with minimal JavaScript

## Customization

Components can be customized by:
1. Modifying the Blade templates in `resources/views/components/ui/`
2. Updating color schemes in the component files
3. Extending component props and functionality
4. Creating new variants for existing components

## Browser Support

Components are tested and supported on:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Contributing

When adding new components:
1. Follow the existing naming convention (`x-ui.component-name`)
2. Include proper documentation in this README
3. Add examples to the demo page
4. Ensure responsive design and dark mode support
5. Test across different browsers and devices
