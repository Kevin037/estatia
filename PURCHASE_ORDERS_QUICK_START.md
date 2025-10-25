# Purchase Orders - Quick Start Guide

## ✅ Status: READY TO USE

The Purchase Orders feature is complete and ready for use at:
**http://127.0.0.1:8000/purchase-orders**

---

## What's Included

✅ **Full Transaction Management**
- Create purchase orders with multiple materials
- Edit existing purchase orders
- Delete purchase orders
- View all purchase orders in table
- Auto-generate transaction numbers (PO-000001, PO-000002, etc.)

✅ **Advanced Features**
- Dynamic material rows (add/remove)
- Auto-calculate totals
- Auto-update material stock
- Filter by status and date range
- Export to Excel
- Server-side DataTables
- Real-time price/subtotal calculation

✅ **Sample Data**
- 5 suppliers seeded
- 13 materials seeded
- 5 projects seeded
- 8 purchase orders seeded (17 items total)

---

## Quick Test (5 minutes)

### 1. **View Purchase Orders**
- Go to **http://127.0.0.1:8000/purchase-orders**
- You should see 8 purchase orders listed
- Table shows: No | Transaction No | Supplier | Date | Total | Status | Actions

### 2. **Create a Purchase Order**
- Click **"Add Purchase Order"** (green button)
- Fill in:
  - **Project:** Green Valley Residence
  - **Date:** Today's date
  - **Supplier:** PT Semen Indonesia
  - **Status:** Completed
  - **Material 1:** Select "Semen Portland Type I"
    - Price auto-fills: Rp 65,000
    - Qty: 100
    - Subtotal auto-calculates: Rp 6,500,000
  - Click **"Add Row"** button
  - **Material 2:** Select "Semen Portland Type II"
    - Price auto-fills: Rp 68,000
    - Qty: 50
    - Subtotal auto-calculates: Rp 3,400,000
  - **Total** shows: Rp 9,900,000
- Click **"Create Purchase Order"**
- Success message shows transaction number (e.g., "PO-000009")

### 3. **Edit a Purchase Order**
- Click **"Edit"** (cyan button) on any purchase order
- Change quantity or add/remove materials
- Click **"Update Purchase Order"**
- Notice stock updates automatically

### 4. **Delete a Purchase Order**
- Click **"Delete"** (red button) on any purchase order
- Confirm in popup (shows transaction number)
- Purchase order deleted
- Material stock reverted automatically

### 5. **Filter & Export**
- Click **"Filter"** button
- Set **Status:** Pending
- Set date range (optional)
- Click **"Apply"** → Table shows only pending orders
- Click **"Export Excel"** → Excel file downloads

---

## Where to Find Things

### In the Application
- **Menu:** Sidebar > Transaction > Purchase Orders
- **List Page:** `/purchase-orders`
- **Create Form:** `/purchase-orders/create`
- **Edit Form:** `/purchase-orders/{id}/edit`

### Documentation Files
- **Implementation Details:** `PURCHASE_ORDERS_IMPLEMENTATION_SUMMARY.md`
- **Quick Start:** This file

### Code Files
- **Controller:** `app/Http/Controllers/PurchaseOrderController.php`
- **Models:** `app/Models/PurchaseOrder.php`, `PurchaseOrderDetail.php`
- **Export:** `app/Exports/PurchaseOrdersExport.php`
- **Views:** `resources/views/purchase-orders/`
- **Routes:** `routes/web.php` (search for "purchase-orders")
- **Menu:** `resources/views/layouts/partials/sidebar-menu.blade.php`

---

## Key Features

### 1. Auto Transaction Number
- Format: **PO-XXXXXX** (e.g., PO-000001, PO-000002)
- Generated automatically on creation
- Sequential numbering
- 6-digit padding with zeros

### 2. Auto Total Calculation
- Calculates: **Sum of (Material Price × Quantity)**
- Updates in real-time as you add materials
- Displays in Rupiah format
- Saved to database on submit

### 3. Auto Stock Management
**On Create:**
- Increases material stock by ordered quantity
- Example: Material has 100, order 50 → new stock is 150

**On Update:**
- Reverts old quantities first
- Then applies new quantities
- Example: Old order 50, new order 75 → stock increases by 25

**On Delete:**
- Decreases material stock by ordered quantity
- Example: Order was 50 → stock decreases by 50

### 4. Dynamic Material Rows
- Click **"Add Row"** to add more materials
- Click **trash icon** to remove a row
- Minimum 1 row required
- Unlimited rows allowed
- Each row calculates independently

### 5. Status Management
- **Pending** (yellow badge): Order placed, not received
- **Completed** (green badge): Order received and processed
- Can filter by status

---

## Form Fields Reference

### Purchase Order Information

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| Project | Dropdown (Select2) | Yes | Select from existing projects |
| Date | Date Picker | Yes | Defaults to today |
| Supplier | Dropdown (Select2) | Yes | Only materials from this supplier can be ordered |
| Status | Dropdown | Yes | Pending or Completed (default: Completed) |

### Materials (Multiple Rows)

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| Material | Dropdown (Select2) | Yes | Shows stock quantity in parentheses |
| Price | Read-only | Auto | Fills automatically from material master data |
| Qty | Number | Yes | Minimum 0.01, can use decimals |
| Subtotal | Read-only | Auto | Qty × Price, calculated in real-time |

---

## Need Help?

### Check Data Counts
```bash
# Count purchase orders
php artisan tinker --execute="echo App\Models\PurchaseOrder::count();"

# Count details
php artisan tinker --execute="echo App\Models\PurchaseOrderDetail::count();"

# View routes
php artisan route:list --name=purchase-orders

# Re-seed if needed
php artisan db:seed --class=PurchaseOrderSeeder
```

### Common Issues

**Issue:** "Page not found" when accessing /purchase-orders
- **Fix:** Check routes are registered in `routes/web.php`
- **Verify:** Run `php artisan route:list --name=purchase-orders`

**Issue:** No data in table
- **Fix:** Run seeder: `php artisan db:seed --class=PurchaseOrderSeeder`
- **Note:** You must seed suppliers, materials, and projects first

**Issue:** Select2 dropdowns not working
- **Fix:** Check Select2 CDN is loaded in the view
- **Verify:** Look for Select2 CSS/JS in browser console

**Issue:** Total not calculating
- **Fix:** Open browser console for JavaScript errors
- **Check:** Alpine.js is loaded and materialManager() function is defined

**Issue:** Stock not updating
- **Fix:** Check Material model's increment/decrement methods
- **Verify:** Check database transactions are committing

**Issue:** Transaction number not unique
- **Fix:** Check PurchaseOrder::generateNumber() method
- **Verify:** No manual transaction number input

---

## Sample Data

### After Seeding, You Should See:

**8 Purchase Orders:**
1. PO-000001 - PT Semen Indonesia - Rp 9,900,000 (Completed)
2. PO-000002 - CV Baja Perkasa - Rp 39,200,000 (Completed)
3. PO-000003 - UD Kayu Jati Murni - Rp 28,050,000 (Completed)
4. PO-000004 - Toko Bangunan Sentosa - Rp 20,125,000 (Completed)
5. PO-000005 - PT Semen Indonesia - Rp 4,875,000 (Pending)
6. PO-000006 - PT Aluminium Indah - Rp 18,900,000 (Pending)
7. PO-000007 - Toko Bangunan Sentosa - Rp 10,675,000 (Pending)
8. PO-000008 - CV Baja Perkasa - Rp 20,750,000 (Pending)

**Status Distribution:**
- Completed: 4 purchase orders
- Pending: 4 purchase orders

**Date Range:**
- Last 30 days (spread evenly)

---

## Testing Checklist

### Must Test (10 minutes)
- [ ] View purchase orders list
- [ ] Create new purchase order (1 material)
- [ ] Create purchase order (multiple materials)
- [ ] Add rows dynamically
- [ ] Remove rows
- [ ] Edit existing purchase order
- [ ] Delete purchase order
- [ ] Filter by status
- [ ] Filter by date range
- [ ] Export to Excel

### Should Test (20 minutes)
- [ ] Search by transaction number
- [ ] Create with different suppliers
- [ ] Create with different projects
- [ ] Change status (Pending ↔ Completed)
- [ ] Export with filters applied
- [ ] Verify stock increases on create
- [ ] Verify stock decreases on delete
- [ ] Verify stock updates correctly on edit
- [ ] Check transaction number generation
- [ ] Check total calculation accuracy
- [ ] Verify Rupiah formatting
- [ ] Test validation (empty fields)
- [ ] Test with decimal quantities

### Optional Test (30 minutes)
- [ ] Create purchase order with 10+ materials
- [ ] Test Select2 search functionality
- [ ] Test responsive design (mobile)
- [ ] Test with large quantities (10,000+)
- [ ] Test with many purchase orders (50+)
- [ ] Check Excel export formatting
- [ ] Verify date sorting in table
- [ ] Test concurrent edits (two tabs)
- [ ] Check error handling (invalid data)
- [ ] Verify loading spinners

---

## Success Indicators

When everything is working correctly:
- ✅ Can access http://127.0.0.1:8000/purchase-orders
- ✅ See 8 purchase orders in the table
- ✅ Can create, edit, and delete purchase orders
- ✅ Transaction numbers auto-generate sequentially
- ✅ Totals calculate correctly in Rupiah
- ✅ Material stock updates automatically
- ✅ Dynamic rows add/remove smoothly
- ✅ Select2 dropdowns are searchable
- ✅ Excel export works with filters
- ✅ Status badges show correct colors
- ✅ No error messages or console errors
- ✅ "Purchase Orders" menu item highlighted
- ✅ Transaction menu auto-expands

---

## Tips for Testing

### Creating Test Data
```
Project: Green Valley Residence
Date: Today
Supplier: PT Semen Indonesia
Status: Completed

Materials:
Row 1: Semen Portland Type I, Qty: 50
Row 2: Semen Portland Type II, Qty: 30

Expected Total: Rp 5,290,000
```

### Testing Stock Updates
1. Note current stock of a material (e.g., 500)
2. Create PO ordering 100 of that material
3. Check material stock increased to 600
4. Delete the PO
5. Check material stock reverted to 500

### Testing Edit Functionality
1. Create PO with Material A (qty 50)
2. Edit and change to Material B (qty 75)
3. Verify Material A stock decreased by 50
4. Verify Material B stock increased by 75

### Testing Filters
1. Create mix of Pending and Completed orders
2. Filter by "Pending" → see only pending
3. Filter by "Completed" → see only completed
4. Set date range → see only orders in range
5. Reset filter → see all orders again

### Testing Export
1. Apply filter (e.g., Status: Pending)
2. Click "Export Excel"
3. Open downloaded file
4. Verify:
   - Only pending orders exported
   - All columns present
   - Rupiah formatting correct
   - Materials listed properly

---

## Business Logic Flow

### Create Purchase Order Flow
```
1. User fills form with project, date, supplier, materials
2. User clicks "Create Purchase Order"
3. Validation runs (all required fields)
4. Database transaction begins
5. Generate transaction number (PO-XXXXXX)
6. Create purchase_orders record
7. Loop through each material:
   - Create purchase_order_details record
   - Calculate subtotal (qty × price)
   - Increment material stock
   - Add subtotal to running total
8. Update purchase_orders.total
9. Commit transaction
10. Redirect to index with success message
```

### Edit Purchase Order Flow
```
1. Load existing purchase order with details
2. User modifies form (change materials/quantities)
3. User clicks "Update Purchase Order"
4. Validation runs
5. Database transaction begins
6. For each old detail:
   - Decrement material stock (revert)
7. Delete all old details
8. For each new material:
   - Create new detail
   - Calculate subtotal
   - Increment material stock
   - Add to running total
9. Update purchase order info and total
10. Commit transaction
11. Redirect to index with success message
```

### Delete Purchase Order Flow
```
1. User clicks "Delete" button
2. SweetAlert2 confirmation popup
3. User confirms deletion
4. AJAX request sent
5. Database transaction begins
6. For each detail:
   - Decrement material stock
7. Delete all details
8. Delete purchase order
9. Commit transaction
10. Return JSON success response
11. DataTable reloads automatically
```

---

## Keyboard Shortcuts

When on the Purchase Orders pages:
- **Click Filter:** Toggle filter card
- **Enter in search:** Trigger search
- **Tab:** Navigate between fields in forms
- **Ctrl+Click on row:** Open in new tab (if implemented)

---

## Database Fields Reference

### purchase_orders table
| Field | Type | Nullable | Notes |
|-------|------|----------|-------|
| id | int | No | Primary key |
| no | varchar | Yes | Auto-generated transaction number |
| dt | date | No | Purchase order date |
| project_id | BigInt | No | Foreign key to projects |
| supplier_id | BigInt | No | Foreign key to suppliers |
| total | double | No | Auto-calculated total |
| status | enum | No | pending, completed |
| created_at | timestamp | No | Auto |
| updated_at | timestamp | No | Auto |

### purchase_order_details table
| Field | Type | Nullable | Notes |
|-------|------|----------|-------|
| id | int | No | Primary key |
| purchase_order_id | BigInt | No | Foreign key to purchase_orders |
| material_id | BigInt | No | Foreign key to materials |
| qty | double | No | Quantity ordered |
| created_at | timestamp | No | Auto |
| updated_at | timestamp | No | Auto |

---

## Pattern Comparison

**Purchase Orders** vs **Other Features:**

| Feature | Purchase Orders | Sales | Milestones | Users |
|---------|----------------|-------|------------|-------|
| Complexity | High | Low | Medium | Medium |
| Fields | 4 + Materials | 2 | 2 | 4 |
| Dynamic Rows | Yes | No | No | No |
| Auto-calculation | Yes | No | No | No |
| Stock Management | Yes | No | No | No |
| Transaction No | Yes | No | No | No |
| Status | Yes | No | No | No |

**Why Purchase Orders is More Complex:**
- Has parent-child relationship (PO + Details)
- Manages inventory (stock updates)
- Requires transaction safety
- Has dynamic UI (add/remove rows)
- Auto-generates unique numbers
- Auto-calculates totals from details

---

## Troubleshooting Scenarios

### Scenario 1: Total is Zero
**Possible Causes:**
- Materials have zero price
- Quantities are empty
- JavaScript calculation not running

**Solution:**
1. Check material prices in database
2. Ensure qty fields have values
3. Open browser console for JS errors

### Scenario 2: Stock Not Updating
**Possible Causes:**
- Transaction failed
- increment/decrement not called
- Wrong material ID

**Solution:**
1. Check database for error logs
2. Verify Material::increment() in controller
3. Use tinker to check material qty directly

### Scenario 3: Duplicate Transaction Numbers
**Possible Causes:**
- Race condition (concurrent creates)
- generateNumber() method broken
- Manual number entry

**Solution:**
1. Add unique constraint on 'no' column
2. Use database-level sequence
3. Implement locking mechanism

---

## Advanced Usage

### Filtering Combinations
```
Status: Pending + Date Range: Last 7 days
→ Shows recent pending orders

Status: Completed + Date Range: This month
→ Shows completed orders this month

No filters
→ Shows all purchase orders
```

### Export Scenarios
```
Export without filters
→ All purchase orders in Excel

Export with Status: Pending
→ Only pending orders

Export with Date Range: 2025-10-01 to 2025-10-31
→ Only October orders

Export with both filters
→ Pending orders in October
```

---

## Quick Commands Reference

```bash
# View all purchase order routes
php artisan route:list --name=purchase-orders

# Count purchase orders
php artisan tinker --execute="echo App\Models\PurchaseOrder::count();"

# View latest purchase order
php artisan tinker --execute="echo App\Models\PurchaseOrder::latest()->first()->no;"

# Re-seed purchase orders (will fail if dependencies missing)
php artisan db:seed --class=PurchaseOrderSeeder

# Seed dependencies first
php artisan db:seed --class=SupplierSeeder
php artisan db:seed --class=MaterialSeeder
php artisan db:seed --class=ProjectSeeder
php artisan db:seed --class=PurchaseOrderSeeder

# Clear cache
php artisan route:cache
php artisan config:cache
```

---

## Ready to Use!

**Access the feature:** http://127.0.0.1:8000/purchase-orders

**For detailed technical information, see:**
- `PURCHASE_ORDERS_IMPLEMENTATION_SUMMARY.md`

**Have questions?** Check the troubleshooting section above or review the implementation summary.

---

*Quick Start Guide - October 25, 2025*
*Transaction Management for Material Procurement* 📦
