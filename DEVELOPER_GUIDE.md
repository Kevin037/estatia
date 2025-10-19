# Estatia - Quick Developer Guide

## Project Overview
**Estatia** is a comprehensive Property Developer ERP system built with Laravel 12, featuring a modern admin interface with full authentication, email verification, and modular architecture.

## Tech Stack
- **Backend**: Laravel 12
- **Database**: MySQL/SQLite
- **Frontend**: Tailwind CSS 3, Alpine.js
- **UI Components**: Custom Blade components
- **Datatables**: Yajra Datatables (server-side)
- **Exports**: Maatwebsite/Excel, DomPDF
- **Enhancements**: Select2, SweetAlert2

## Quick Start

### Running the Application
```bash
# Start development server
php artisan serve

# Compile assets (development)
npm run dev

# Compile assets (production)
npm run build
```

### Database
```bash
# Run migrations
php artisan migrate

# Fresh migration with seed
php artisan migrate:fresh --seed
```

## Architecture

### Models (26 Total)
All models use `$guarded = ['id']` for mass assignment protection.

**Master Data**:
- Customer, Supplier, Contractor, Sales, Material, Type, Account, Milestone

**Production**:
- Land, Project, ProjectContractor, ProjectMilestone, Cluster, Product, ProductPhoto, Unit, UnitPhoto

**Transactions**:
- PurchaseOrder, PurchaseOrderDetail, Order, Invoice, Payment, Ticket, Feedback

**Accounting**:
- JournalEntry

### Relationships
Models include complete Eloquent relationships:
```php
// One-to-Many
$project->clusters()
$customer->orders()

// Many-to-Many
$project->contractors()

// Belongs To
$unit->cluster()
$order->customer()
```

### Scopes (Reusable Query Methods)
```php
// Search
Customer::search('john')->get();

// Filter by status
Order::byStatus('pending')->get();

// Date range
Payment::dateRange('2024-01-01', '2024-12-31')->get();

// With relations
Project::withRelations()->get();
```

## Frontend Components

### Admin Layout
```blade
<x-admin-layout>
    <x-slot name="header">
        <h2>Page Title</h2>
    </x-slot>

    <!-- Content -->
</x-admin-layout>
```

### Toast Notifications
```php
// Controller
return redirect()->back()->with('success', 'Saved!');
return redirect()->back()->with('error', 'Failed!');
```

```javascript
// JavaScript
window.dispatchEvent(new CustomEvent('toast', {
    detail: {
        type: 'success',
        title: 'Success!',
        message: 'Operation completed'
    }
}));
```

### Utility Classes
```html
<!-- Cards -->
<div class="card">Content</div>

<!-- Buttons -->
<button class="btn btn-primary">Save</button>
<button class="btn btn-danger">Delete</button>

<!-- Badges -->
<span class="badge badge-success">Active</span>
<span class="badge badge-warning">Pending</span>

<!-- Tables -->
<div class="table-container">
    <table class="table">
        <!-- ... -->
    </table>
</div>
```

## Sidebar Menu Structure
```
├── Dashboard
├── Master Data (Collapsible)
│   ├── Customers
│   ├── Suppliers
│   ├── Contractors
│   ├── Sales
│   ├── Materials
│   ├── Types
│   ├── Accounts
│   └── Milestones
├── Production (Collapsible)
│   ├── Lands
│   ├── Projects
│   ├── Project Milestones
│   ├── Clusters
│   ├── Products
│   └── Units
├── Purchasing (Collapsible)
│   ├── Purchase Orders
│   └── Material Stock
├── Sales (Collapsible)
│   ├── Orders
│   ├── Invoices
│   └── Payments
├── Customer Service (Collapsible)
│   ├── Tickets
│   └── Feedbacks
├── Accounting (Collapsible)
│   ├── Chart of Accounts
│   ├── Journal Entries
│   └── General Ledger
├── Reports (Collapsible)
│   ├── Sales Report
│   ├── Purchase Report
│   ├── Project Report
│   ├── Financial Report
│   └── Inventory Report
└── Settings
```

## Best Practices

### Controllers (Thin Controllers)
```php
public function store(StoreOrderRequest $request)
{
    $order = Order::createFromRequest($request);
    return redirect()->route('orders.index')
        ->with('success', 'Order created successfully');
}
```

### Form Requests (Validation)
```php
php artisan make:request StoreOrderRequest
```

### Services (Business Logic)
```php
// app/Services/OrderService.php
class OrderService
{
    public function createOrder(array $data)
    {
        // Complex business logic here
    }
}
```

### Eager Loading
```php
// Good - Eager loading
$orders = Order::with(['customer', 'invoices'])->get();

// Bad - N+1 problem
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->customer->name; // N+1!
}
```

## Database Schema Highlights

### Users
- `email_verified_at` for email verification
- `photo` for profile picture
- `phone` for contact

### Auto-Generated Numbers
```php
Order::generateNumber()        // ORD-000001
Invoice::generateNumber()      // INV-000001
PurchaseOrder::generateNumber() // PO-000001
Payment::generateNumber()      // PAY-000001
Ticket::generateNumber()       // TKT-000001
```

### Enums
- Project status: `pending`, `in_progress`, `completed`
- Unit status: `available`, `reserved`, `sold`, `handed_over`
- Invoice status: `unpaid`, `partial`, `paid`, `overdue`
- Payment type: `cash`, `transfer`

## Email Verification
Configured in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

User model implements `MustVerifyEmail` interface.

## Currency Format
All monetary values use **Indonesian Rupiah (Rp)** format.

```php
// Display
Rp {{ number_format($amount, 0, ',', '.') }}
```

## Theme Colors
```php
config('theme.colors.primary')  // #4F46E5
config('theme.colors.success')  // #10B981
config('theme.colors.danger')   // #EF4444
config('theme.colors.warning')  // #F59E0B
```

## Tips & Tricks

### Alpine.js State Management
```html
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Content</div>
</div>
```

### Tailwind Responsive Design
```html
<!-- Mobile first -->
<div class="w-full md:w-1/2 lg:w-1/3">
    <!-- Full width on mobile, half on tablet, third on desktop -->
</div>
```

### File Uploads Preview
```html
<input type="file" 
       @change="preview = URL.createObjectURL($event.target.files[0])"
       accept="image/*">
<img :src="preview" x-show="preview">
```

## Common Commands
```bash
# Create model with migration, factory, seeder
php artisan make:model ModelName -mfs

# Create controller with resource methods
php artisan make:controller ModelController --resource

# Create form request
php artisan make:request StoreModelRequest

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# List routes
php artisan route:list --except-vendor
```

## Support & Resources
- Laravel Documentation: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- Alpine.js: https://alpinejs.dev/
- Yajra Datatables: https://yajrabox.com/docs/laravel-datatables

---
**Last Updated**: October 19, 2025
**Version**: 1.0.0
