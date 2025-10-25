# Formula Transaction Feature Implementation

## Overview
Successfully implemented a complete Formula CRUD transaction feature that allows users to create formulas with multiple materials, automatically calculates total costs, and provides full management capabilities.

## Implementation Date
October 25, 2025

## Database Structure

### Tables Created
1. **formulas**
   - id (primary key)
   - code (unique string) - Formula identifier
   - name (string) - Formula name
   - total (decimal 15,2, default 0) - Automatically calculated total cost
   - created_at, updated_at
   - Indexes: code, name

2. **formula_details**
   - id (primary key)
   - formula_id (foreign key → formulas, cascade on delete)
   - material_id (foreign key → materials, restrict on delete)
   - qty (decimal 15,2) - Quantity of material
   - created_at, updated_at
   - Index: [formula_id, material_id]

## Models

### Formula Model (`app/Models/Formula.php`)
- **Fillable**: code, name, total
- **Casts**: total as decimal:2
- **Relationship**: hasMany FormulaDetail (details)

### FormulaDetail Model (`app/Models/FormulaDetail.php`)
- **Fillable**: formula_id, material_id, qty
- **Casts**: qty as decimal:2
- **Relationships**:
  - belongsTo Formula
  - belongsTo Material

## Controller Features (`app/Http/Controllers/FormulaController.php`)

### Index (DataTables)
- Server-side processing
- Date range filter (start_date, end_date)
- Search by name and code
- Formatted total display with currency (Rp)
- Custom action column

### Create
- Load all materials for selection
- Multi-row form interface
- Real-time price fetching

### Store
- **Validation**:
  - code: required, unique, max 255
  - name: required, max 255
  - material_ids: required array, min 1 item, exists in materials
  - quantities: required array, min 1 item, numeric, min 0.01
- **Transaction Processing**:
  - Calculate total automatically: SUM(material.price × qty)
  - Create formula record
  - Create multiple formula_detail records
  - Database transaction with rollback on error

### Edit
- Load existing formula with details
- Load all materials
- Pre-populate form with existing data

### Update
- Same validation as Store
- Recalculate total cost
- Delete old details, create new ones
- Maintain data integrity with transactions

### Destroy
- Delete formula (details cascade automatically)
- JSON response for AJAX handling
- SweetAlert2 confirmation

### Export
- Excel export with date filtering
- Custom formatting with emerald header
- Columns: No, Formula Code, Formula Name, Cost (Total), Created At

## Export Class (`app/Exports/FormulaExport.php`)
- Implements: FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
- Date range filtering support
- Emerald-600 (#059669) header with white text
- Formatted currency display (Rp)
- Custom column widths for readability

## Views (All using `<x-admin-layout>`)

### Index (`resources/views/formulas/index.blade.php`)
- **Features**:
  - Filter card with slideToggle animation
  - Date range filter
  - Export to Excel button
  - Add Formula button
  - DataTables with server-side processing
  - Custom loading spinner
  - Delete confirmation with SweetAlert2
  - Responsive table layout

### Create (`resources/views/formulas/create.blade.php`)
- **Features**:
  - Alpine.js powered dynamic multi-row table
  - Add/Remove row functionality
  - Material dropdown selection (shows stock quantity)
  - Automatic price fetching from selected material
  - Real-time quantity input
  - Automatic subtotal calculation (price × qty)
  - Total cost calculation (sum of all subtotals)
  - Formatted currency display (Indonesian format)
  - Spinner button on submit
  - Form validation

**Alpine.js Functions**:
```javascript
- rows: Array of formula details
- totalCost: Computed total from all subtotals
- addRow(): Add new material row
- removeRow(index): Remove specific row (minimum 1 row)
- updatePrice(index): Get price from selected material
- calculateSubtotal(index): Calculate price × qty
- formatNumber(number): Indonesian number format
```

### Edit (`resources/views/formulas/edit.blade.php`)
- Same features as Create
- Pre-populated with existing formula data
- Loads existing formula details into rows array
- Maintains material selections and quantities
- Recalculates totals on material/qty changes

### Actions Partial (`resources/views/formulas/partials/actions.blade.php`)
- Edit button (btn-icon-primary)
- Delete button (btn-icon-danger)
- Delete confirmation via jQuery `.delete-formula` class

## Routing (`routes/web.php`)
```php
// Transaction - Formulas
Route::get('/formulas/export', [FormulaController::class, 'export'])->name('formulas.export');
Route::resource('formulas', FormulaController::class);
```

## Sidebar Navigation
Added new "Transaction" menu section between "Purchasing" and "Sales":
- Icon: Clipboard/document icon
- Submenu: Formulas (active when on formulas/* routes)
- Collapsible with smooth transition
- Proper highlighting on active route

## Seeder (`database/seeders/FormulaSeeder.php`)
Creates 3 test formulas:
1. **F-001**: Basic Concrete Mix (2 materials)
2. **F-002**: Premium Building Material (2 materials)
3. **F-003**: Standard Foundation Mix (3 materials)

Each with realistic quantities and calculated totals.

## Key Features

### 1. Multi-Row Material Selection
- Dynamic add/remove rows with Alpine.js
- Dropdown shows material name and current stock
- Cannot remove last row (minimum 1 material required)

### 2. Automatic Cost Calculation
- Real-time subtotal calculation per row
- Automatic total cost summation
- Backend verification and storage
- Indonesian currency formatting (Rp X.XXX.XXX)

### 3. Data Integrity
- Database transactions for atomic operations
- Foreign key constraints
- Cascade delete on formula deletion
- Restrict delete on material (must update formula first)
- Validation prevents empty formulas

### 4. User Experience
- Consistent with Users/Customers/Materials/Suppliers UI
- Loading spinners on form submission
- SweetAlert2 for confirmations
- DataTables for efficient data display
- Responsive design
- Filter and export capabilities

### 5. Excel Export
- Professional formatting
- Emerald theme consistency
- Date range filtering
- Proper number formatting

## Testing Results ✅

All features tested and working:
- ✅ Create formula with multiple materials
- ✅ Real-time price fetching from materials
- ✅ Automatic subtotal and total calculation
- ✅ Add/remove rows dynamically
- ✅ Edit existing formulas
- ✅ Pre-populate edit form with details
- ✅ Delete formulas with cascade
- ✅ DataTables loading and display
- ✅ Filter by date range
- ✅ Export to Excel
- ✅ Form validation
- ✅ Loading spinner buttons
- ✅ Sidebar navigation
- ✅ Responsive layout

## Formula Calculation Logic

### Frontend (Alpine.js)
```javascript
subtotal = price × qty
total = sum of all subtotals
```

### Backend (Laravel)
```php
foreach (material_ids as index => material_id) {
    material = Material::find(material_id)
    qty = quantities[index]
    total += (material->price × qty)
}
formula->total = total
```

## File Structure
```
app/
├── Http/Controllers/FormulaController.php
├── Models/
│   ├── Formula.php
│   └── FormulaDetail.php
└── Exports/FormulaExport.php

database/
├── migrations/
│   ├── 2025_10_25_031835_create_formulas_table.php
│   └── 2025_10_25_031843_create_formula_details_table.php
└── seeders/FormulaSeeder.php

resources/views/formulas/
├── index.blade.php
├── create.blade.php
├── edit.blade.php
└── partials/
    └── actions.blade.php

routes/web.php (updated)
resources/views/layouts/partials/sidebar-menu.blade.php (updated)
```

## Usage Examples

### Creating a Formula
1. Navigate to Transaction → Formulas
2. Click "Add Formula"
3. Enter Formula Code (e.g., F-004)
4. Enter Formula Name
5. Select Material from dropdown (shows stock)
6. Enter Quantity
7. Click "Add Row" for more materials
8. View automatic total calculation
9. Click "Create Formula"

### Editing a Formula
1. Click Edit icon on formula row
2. Modify code/name if needed
3. Change materials or quantities
4. Add/remove rows as needed
5. Total recalculates automatically
6. Click "Update Formula"

### Deleting a Formula
1. Click Delete icon
2. Confirm in SweetAlert2 dialog
3. Formula and all details deleted
4. Table refreshes automatically

## Security Features
- CSRF protection on all forms
- Validation on both client and server
- Database transactions
- Proper authorization middleware
- SQL injection prevention (Eloquent ORM)

## Performance Optimizations
- Server-side DataTables processing
- Eager loading of relationships
- Indexed database columns
- Efficient queries with constraints

## Consistency with Existing Modules
- ✅ Uses `<x-admin-layout>` template
- ✅ Same CSS classes (btn, card, form-input)
- ✅ Same button structure and SVG icons
- ✅ Same DataTables configuration
- ✅ Same filter card pattern
- ✅ Same export functionality
- ✅ Same spinner button pattern
- ✅ Same delete confirmation pattern
- ✅ Same sidebar menu structure

## Conclusion
The Formula Transaction feature is fully implemented, tested, and ready for production use. It provides a complete solution for managing formulas with multiple materials, automatic cost calculation, and all standard CRUD operations matching the existing system patterns.

## Next Steps (Optional Enhancements)
1. Add formula cloning feature
2. Add formula history/versioning
3. Add material substitution suggestions
4. Add bulk import from Excel
5. Add formula templates
6. Add cost comparison reports
