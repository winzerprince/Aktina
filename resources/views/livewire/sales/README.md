# Sales Table Documentation

## Overview
The Sales Table component is a comprehensive Livewire component built with Mary UI that provides a modern, interactive interface for viewing and managing sales data.

## Features

### 📊 Dashboard View
- **Quick Stats Cards**: Total Revenue, Total Orders, Average Order Value
- **Responsive Design**: Mobile-first approach with responsive grid layouts
- **Dark Mode Support**: Full dark/light theme compatibility

### 📅 Date Filtering
- **Start Date & End Date Pickers**: Using Mary UI datetime components
- **Live Updates**: Real-time filtering with `wire:model.live`
- **Apply Filters Button**: Manual refresh option

### 📋 Data Table
- **Mary UI x-table Component**: Feature-rich table with built-in styling
- **Pagination**: Built-in pagination support
- **Sorting**: Sortable columns (default: created_at desc)
- **Striped Rows**: Enhanced readability

### 🎯 Table Columns
1. **Order ID**: Badge-styled with zero-padded format (#0001)
2. **Buyer**: Avatar + name + email + company info
3. **Total Amount**: Formatted currency with item count
4. **Date**: Formatted date and time
5. **Actions**: View and Export buttons

### 🔍 Order Details Modal
- **Spotlight Modal**: Mary UI modal with backdrop blur
- **Order Header**: Gradient header with order info
- **Customer Information**: Detailed buyer information card
- **Order Summary**: Key order details
- **Order Items**: Itemized list (if items data available)
- **Action Buttons**: Export and Close options

## Technical Implementation

### Livewire Component Structure
```php
class SalesTable extends Component
{
    use WithPagination;

    public string $companyName;
    public string $startDate;
    public string $endDate;
    public bool $showOrderModal = false;
    public $selectedOrder = null;
    public int $perPage = 10;

    // Methods: mount(), viewOrder(), exportOrder(), closeModal(), render()
}
```

### Key Mary UI Components Used
- `x-datetime` - Date/time pickers
- `x-button` - Action buttons with icons
- `x-table` - Main data table
- `x-modal` - Order details modal
- `x-card` - Information containers
- `x-badge` - Status indicators
- `x-avatar` - User avatars
- `x-icon` - Icons throughout the interface

### CSS Enhancements
- Custom hover effects for table rows
- Smooth animations and transitions
- Responsive design adjustments
- Dark mode specific styling
- Custom scrollbar styling for modals

## Usage

### Basic Implementation
```blade
<livewire:sales.sales-table />
```

### In Controller/Route
```php
Route::get('/sales', function () {
    return view('sales.index');
})->name('sales');
```

### View File (sales/index.blade.php)
```blade
<x-layouts.app title="Sales Management">
    <div class="p-6">
        <livewire:sales.sales-table />
    </div>
</x-layouts.app>
```

## Customization Options

### Modifying Date Range Defaults
In the `mount()` method:
```php
$this->startDate = Carbon::now()->subDays(30)->toDateString(); // Change default range
$this->endDate = Carbon::now()->toDateString();
```

### Adjusting Pagination
```php
public int $perPage = 20; // Change default items per page
```

### Adding New Table Columns
In the blade file, modify the `$headers` array:
```php
$headers = [
    ['key' => 'id', 'label' => 'Order ID', 'class' => 'w-24 text-center'],
    ['key' => 'status', 'label' => 'Status', 'class' => 'w-32'], // New column
    // ... existing columns
];
```

Then add the corresponding scope:
```blade
@scope('cell_status', $order)
    <x-badge :value="$order->status" class="badge-success" />
@endscope
```

### Styling Customization
The component uses CSS classes that can be customized in `resources/css/sales-table.css`:
- `.sales-table-container` - Main container styling
- `.stats-card` - Statistics cards
- `.modal-content` - Modal content area
- Media queries for responsive behavior

## Dependencies

### Required Packages
- Laravel Livewire
- Mary UI (`robsontenorio/mary`)
- Tailwind CSS

### Required Services
- `SalesService` - For data retrieval
- `Order` model with proper relationships (buyer, seller)
- User authentication system

## Browser Support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance Considerations
- Uses Livewire pagination for large datasets
- Lazy loading for modal content
- Optimized CSS with minimal JavaScript
- Efficient database queries through SalesService

## Accessibility Features
- Semantic HTML structure
- ARIA attributes from Mary UI components
- Keyboard navigation support
- Screen reader compatible
- High contrast mode support

## Future Enhancements
- Export functionality (CSV, PDF)
- Advanced filtering options
- Bulk actions for multiple orders
- Real-time updates with Laravel Echo
- Print-friendly modal view
