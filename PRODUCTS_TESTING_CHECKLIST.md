# Products CRUD Testing Checklist

## Overview
This document contains a comprehensive testing checklist for the Products Transaction feature. All tests must pass before considering the feature complete.

## Feature Details
- **Form Fields**: Name, SKU, Photo, Price, Formula_id
- **List Columns**: No. | SKU | Photo | Name | Stock (qty) | Price
- **Pattern**: Replicates Users CRUD pattern with x-admin-layout

---

## Backend Verification ✅

### Database & Model
- ✅ Migration `2025_10_25_041012_add_transaction_fields_to_products_table` executed successfully
- ✅ Added fields: `name`, `sku` (unique), `photo` (nullable), `qty` (decimal, default 0)
- ✅ Product model updated with `photo_url` accessor
- ✅ Product model has `formula()` relationship
- ✅ Sample data seeded (10 products via ProductSeeder)

### Routes
- ✅ All 8 Product routes registered:
  - GET /products (index)
  - POST /products (store)
  - GET /products/create (create)
  - GET /products/export (export)
  - GET /products/{product} (show)
  - PUT /products/{product} (update)
  - DELETE /products/{product} (destroy)
  - GET /products/{product}/edit (edit)

### Controller
- ✅ ProductController created with all CRUD methods
- ✅ Photo upload handling (store/update/delete)
- ✅ Validation rules for all fields
- ✅ DataTables integration
- ✅ Excel export functionality

### Views
- ✅ products/index.blade.php (list with DataTables)
- ✅ products/create.blade.php (form with photo upload)
- ✅ products/edit.blade.php (form with photo preview)
- ✅ products/partials/actions.blade.php (edit/delete buttons)

### Navigation
- ✅ Products link added to Transaction sidebar menu
- ✅ Active state highlighting on `products*` routes

---

## Frontend Testing (REQUIRED MANUAL TESTS)

### 1. Navigation & Access
- [ ] Login to application
- [ ] Open sidebar and expand "Transaction" menu
- [ ] Verify "Products" link appears under "Formulas"
- [ ] Click "Products" link
- [ ] Verify URL is `/products`
- [ ] Verify page title is "Products"

### 2. Index Page (List View)
- [ ] Verify DataTables loads with all 10 seeded products
- [ ] Check table columns: No. | SKU | Photo | Name | Stock (qty) | Price
- [ ] Verify photos show placeholder (gray box with icon) since no photos uploaded yet
- [ ] Verify prices display in Rupiah format (Rp X.XXX.XXX)
- [ ] Verify qty displays with 2 decimals (0.00)
- [ ] Test DataTables search - type product name in search box
- [ ] Test DataTables pagination - change "Show X entries"
- [ ] Test DataTables sorting - click column headers
- [ ] Click "Filter" button - verify date range inputs appear with slide animation
- [ ] Click "Export to Excel" button - verify file downloads with correct data

### 3. Create Product (Without Photo)
- [ ] Click "Add Product" button on index page
- [ ] Verify URL is `/products/create`
- [ ] Verify page title is "Add Product"
- [ ] Verify form has all fields: Name, SKU, Photo, Price, Formula
- [ ] Fill in Name: "Test Product A"
- [ ] Fill in SKU: "TEST-001"
- [ ] Leave Photo empty
- [ ] Fill in Price: "500000"
- [ ] Select Formula from dropdown (or leave as "No Formula")
- [ ] Click "Create Product" button
- [ ] Verify spinner appears on button ("Creating…")
- [ ] Verify redirect to `/products` index page
- [ ] Verify success message appears (if implemented)
- [ ] Verify new product appears in table with placeholder photo
- [ ] Verify price displays as "Rp 500.000"

### 4. Create Product (With Photo)
- [ ] Click "Add Product" button
- [ ] Fill in Name: "Test Product B"
- [ ] Fill in SKU: "TEST-002"
- [ ] Click "Choose File" for Photo
- [ ] Select an image file (JPG, PNG, GIF) < 2MB
- [ ] Verify photo preview appears immediately (24x24 thumbnail)
- [ ] Fill in Price: "750000"
- [ ] Select a Formula
- [ ] Click "Create Product" button
- [ ] Verify spinner appears
- [ ] Verify redirect to index
- [ ] Verify new product appears with uploaded photo (12x12 rounded image)
- [ ] Verify photo is clickable/visible

### 5. Photo Upload Validation
- [ ] Click "Add Product" button
- [ ] Fill in Name: "Test Product C"
- [ ] Fill in SKU: "TEST-003"
- [ ] Try uploading a file > 2MB
- [ ] Verify error message: "The photo must not be greater than 2048 kilobytes."
- [ ] Try uploading a non-image file (PDF, TXT, etc.)
- [ ] Verify error message: "The photo must be a file of type: jpeg, png, jpg, gif."

### 6. SKU Uniqueness Validation
- [ ] Click "Add Product" button
- [ ] Fill in Name: "Test Product D"
- [ ] Fill in SKU: "TEST-001" (duplicate from test #3)
- [ ] Fill in Price: "100000"
- [ ] Click "Create Product" button
- [ ] Verify error message: "The sku has already been taken."
- [ ] Verify form retains all entered data (except photo)

### 7. Required Field Validation
- [ ] Click "Add Product" button
- [ ] Leave Name empty
- [ ] Fill in SKU: "TEST-004"
- [ ] Click "Create Product" button
- [ ] Verify error message under Name field
- [ ] Try submitting with empty SKU
- [ ] Verify error message under SKU field
- [ ] Try submitting with empty Price
- [ ] Verify error message under Price field
- [ ] Note: Photo and Formula are optional fields

### 8. Edit Product (No Photo Change)
- [ ] From products index, click Edit button (pencil icon) on first product
- [ ] Verify URL is `/products/{id}/edit`
- [ ] Verify page title is "Edit Product"
- [ ] Verify form pre-populates with existing data:
  - [ ] Name field shows current name
  - [ ] SKU field shows current sku
  - [ ] Price field shows current price
  - [ ] Formula dropdown shows selected formula (if any)
  - [ ] Current photo displays (or placeholder if no photo)
- [ ] Change Name to "Updated Product Name"
- [ ] Do NOT upload new photo
- [ ] Click "Update Product" button
- [ ] Verify spinner appears ("Updating…")
- [ ] Verify redirect to index
- [ ] Verify product name updated in table
- [ ] Verify photo remains unchanged

### 9. Edit Product (Replace Photo)
- [ ] Edit a product that has a photo (from test #4)
- [ ] Verify current photo displays as 24x24 thumbnail
- [ ] Click "Choose File" for Photo
- [ ] Select a different image
- [ ] Verify new photo preview replaces old preview
- [ ] Click "Update Product" button
- [ ] Verify redirect to index
- [ ] Verify new photo appears in table (old photo deleted)
- [ ] Check `storage/app/public/products` folder - verify old photo deleted

### 10. Edit Product (Add Photo to Product Without One)
- [ ] Edit a product without photo (from test #3)
- [ ] Verify placeholder displays (gray box with icon)
- [ ] Upload a photo
- [ ] Verify preview appears
- [ ] Click "Update Product" button
- [ ] Verify photo now displays in table

### 11. Delete Product (With Photo)
- [ ] From products index, click Delete button (trash icon) on product with photo
- [ ] Verify SweetAlert2 confirmation dialog appears
- [ ] Verify dialog shows product details
- [ ] Click "Cancel" button
- [ ] Verify product NOT deleted
- [ ] Click Delete button again
- [ ] Click "Yes, delete it!" button
- [ ] Verify product removed from table
- [ ] Check `storage/app/public/products` folder - verify photo deleted

### 12. Delete Product (Without Photo)
- [ ] Delete a product without photo
- [ ] Verify SweetAlert2 confirmation
- [ ] Confirm deletion
- [ ] Verify product removed

### 13. Formula Dropdown
- [ ] Click "Add Product" button
- [ ] Check Formula dropdown
- [ ] Verify "No Formula" option at top
- [ ] Verify all formulas listed with format: "CODE - Name (Rp X.XXX.XXX)"
- [ ] Create product with formula selected
- [ ] Create product with "No Formula" selected
- [ ] Verify both work correctly

### 14. Export to Excel
- [ ] From products index, click "Export to Excel" button
- [ ] Verify file downloads (products_YYYY-MM-DD.xlsx)
- [ ] Open Excel file
- [ ] Verify header row has emerald-600 background with white text
- [ ] Verify columns: No | SKU | Name | Stock (qty) | Price | Created At
- [ ] Verify all products listed
- [ ] Verify Price formatted as "Rp X.XXX.XXX"
- [ ] Verify qty formatted with 2 decimals
- [ ] Click "Filter" button on index
- [ ] Select date range (From: 1 day ago, To: today)
- [ ] Export again
- [ ] Verify exported file only contains products within date range

### 15. Responsive Design
- [ ] Test on desktop browser (1920x1080)
- [ ] Verify layout looks good
- [ ] Test on tablet size (768px width)
- [ ] Verify sidebar collapses/expands
- [ ] Test on mobile size (375px width)
- [ ] Verify table scrolls horizontally
- [ ] Verify buttons stack properly

### 16. Button Spinners
- [ ] On create form, verify "Create Product" button shows spinner on submit
- [ ] Verify button disabled during submit (cannot double-click)
- [ ] On edit form, verify "Update Product" button shows spinner
- [ ] Verify consistent with Users pattern

### 17. Photo Storage Verification
- [ ] Navigate to `storage/app/public/products` folder
- [ ] Verify uploaded photos exist with original filenames
- [ ] Upload 2 products with photos
- [ ] Edit one and replace photo
- [ ] Delete one product
- [ ] Verify only 1 photo remains in folder
- [ ] Verify `public/storage` symlink exists (points to `storage/app/public`)

### 18. Sidebar Menu State
- [ ] Navigate to /products
- [ ] Verify "Transaction" menu is expanded
- [ ] Verify "Products" link has active state (bg-emerald-700 text-white)
- [ ] Navigate to /formulas
- [ ] Verify "Transaction" menu stays expanded
- [ ] Verify "Formulas" link has active state
- [ ] Navigate to /users
- [ ] Verify "Transaction" menu collapses

### 19. Data Persistence
- [ ] Create a product with all fields filled
- [ ] Logout
- [ ] Login again
- [ ] Navigate to products
- [ ] Verify product still exists with all data
- [ ] Verify photo still displays

### 20. Performance
- [ ] With 10+ products in table, verify DataTables loads quickly (< 2 seconds)
- [ ] Upload a 2MB photo, verify upload completes within 5 seconds
- [ ] Verify no console errors in browser dev tools (F12)
- [ ] Verify no PHP errors in Laravel log (`storage/logs/laravel.log`)

---

## Integration Tests

### 21. Formula Integration
- [ ] Create a Formula with materials (if not exists)
- [ ] Create a Product with that Formula selected
- [ ] Edit the Product and change Formula
- [ ] Verify formula_id updates correctly in database
- [ ] Delete the Formula
- [ ] Verify Product's formula_id set to NULL (or handle error)

### 22. Consistency with Other Modules
- [ ] Compare Products CRUD with Users CRUD
- [ ] Verify same card styling (`.card`, `.card-header`)
- [ ] Verify same button classes (`.btn`, `.btn-primary`, `.btn-secondary`)
- [ ] Verify same form input classes (`.form-input`, `.form-label`)
- [ ] Verify same DataTables configuration
- [ ] Verify same SweetAlert2 delete confirmation
- [ ] Verify same spinner button pattern

---

## Bug Testing

### 23. Edge Cases
- [ ] Create product with Name = 255 characters (max length)
- [ ] Create product with SKU = 255 characters (max length)
- [ ] Create product with Price = 0
- [ ] Create product with Price = 0.01
- [ ] Create product with Price = 999999999999.99 (max decimal)
- [ ] Upload photo with special characters in filename
- [ ] Upload photo with spaces in filename
- [ ] Try accessing `/products/create` without authentication
- [ ] Try accessing `/products/999999` (non-existent product)

### 24. Concurrent Operations
- [ ] Open product edit form in two browser tabs
- [ ] Edit Name in first tab and save
- [ ] Edit Price in second tab and save
- [ ] Verify last save wins (no data corruption)
- [ ] Delete product in one tab
- [ ] Try to edit same product in another tab
- [ ] Verify appropriate error handling

---

## Final Verification

### 25. Code Quality
- ✅ No errors in `get_errors` output
- ✅ All routes registered correctly
- ✅ Controller follows Laravel best practices
- ✅ Blade templates use x-admin-layout
- ✅ Photo upload follows Laravel Storage best practices
- ✅ Validation rules comprehensive and secure

### 26. Documentation
- ✅ This testing checklist created
- [ ] All tests above completed successfully
- [ ] Any bugs found documented and fixed
- [ ] Feature marked as production-ready

---

## Test Results Summary

**Total Tests**: 26 sections (100+ individual test cases)

**Status**:
- Backend Setup: ✅ 100% Complete
- Frontend Manual Tests: ⏳ Pending User Testing
- Integration Tests: ⏳ Pending User Testing
- Bug Testing: ⏳ Pending User Testing
- Final Verification: ⏳ Pending User Testing

**Next Steps**:
1. Start development server: `php artisan serve`
2. Navigate to http://localhost:8000/products
3. Execute all manual tests in order
4. Report any failures for immediate fixing
5. Once all tests pass, mark feature as complete

---

## Quick Test Commands

```bash
# Start development server
php artisan serve

# Check routes
php artisan route:list --name=products

# View logs
tail -f storage/logs/laravel.log

# Check database
php artisan tinker
>>> \App\Models\Product::count()
>>> \App\Models\Product::first()

# Clear cache (if issues)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**Created**: 2025-01-25  
**Feature**: Products Transaction CRUD  
**Developer**: GitHub Copilot  
**Status**: Ready for Testing
