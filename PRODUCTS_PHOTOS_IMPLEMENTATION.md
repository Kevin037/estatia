# Products Multiple Photos Implementation

## Overview
Enhanced the Products module to support multiple photo uploads with drag-and-drop functionality using native Alpine.js (no external libraries needed).

## Implementation Date
December 2024

## Features Implemented

### 1. **Multiple Photo Upload with Drag & Drop**
- ✅ Main product photo field with drag-and-drop support
- ✅ Additional product photos field supporting multiple files
- ✅ Visual drag-over feedback (border color changes)
- ✅ File size validation (2MB per file)
- ✅ Image type validation (PNG, JPG, GIF)
- ✅ Real-time preview of uploaded photos
- ✅ Remove photos before upload

### 2. **Backend Enhancements**

#### ProductController Updates
- **store()**: 
  - Validates `product_photos` array
  - Loops through uploaded files
  - Creates ProductPhoto records with auto-numbered names
  - Wrapped in database transaction

- **edit()**:
  - Eager loads productPhotos relationship
  - Provides existing photos to edit view

- **update()**:
  - Handles `delete_photos[]` array for deletion
  - Deletes selected photos from storage and database
  - Processes new photo uploads
  - Wrapped in database transaction

- **destroy()**:
  - Deletes all product photos from storage
  - Removes all ProductPhoto records
  - Wrapped in database transaction

- **index() DataTable**:
  - Added `photos_count` column
  - Shows emerald badge with photo count
  - Displays "No photos" text if empty

#### ProductPhoto Model Enhancement
```php
protected $appends = ['photo_url'];

public function getPhotoUrlAttribute() {
    if ($this->photo) {
        return asset('storage/' . $this->photo);
    }
    return asset('images/no-image.png');
}
```

### 3. **Frontend Views**

#### create.blade.php
- Main photo section with drag-and-drop
- Additional photos section with drag-and-drop
- Grid preview of photos to be uploaded (2-4 columns responsive)
- Remove button on hover for each photo
- Alpine.js `productPhotos()` function managing state

#### edit.blade.php
- Main photo section with drag-and-drop (replace existing)
- Display existing photos grid with delete checkboxes
- Add new photos section with drag-and-drop
- Preview new photos before upload
- Delete selected existing photos on update

#### index.blade.php
- Added "Photos" column header
- DataTable shows photos_count badge
- Positioned between "Name" and "Stock" columns

### 4. **Alpine.js Implementation**

```javascript
function productPhotos() {
    return {
        photos: [],
        isDragging: false,
        
        handleFiles(files) {
            // Validates and adds photos to preview
            // Max 2MB per file
        },
        
        handleDrop(e) {
            // Handles drag and drop events
        },
        
        removePhoto(index) {
            // Removes photo from preview
            // Revokes object URL to prevent memory leaks
        },
        
        updateInput() {
            // Updates hidden file input with DataTransfer API
        }
    }
}
```

## File Changes

### Backend
1. `app/Http/Controllers/ProductController.php`
   - Added ProductPhoto and DB imports
   - Enhanced index, store, edit, update, destroy methods
   - Added photos_count column to DataTable

2. `app/Models/ProductPhoto.php`
   - Added photo_url accessor
   - Added $appends property

### Frontend
1. `resources/views/products/create.blade.php`
   - Replaced simple file input with drag-and-drop zones
   - Added preview grid
   - Added Alpine.js script

2. `resources/views/products/edit.blade.php`
   - Added existing photos grid with delete checkboxes
   - Added drag-and-drop for new photos
   - Added preview for new photos
   - Added Alpine.js script

3. `resources/views/products/index.blade.php`
   - Added "Photos" column header
   - Added photos_count to DataTable columns

## Usage

### Creating Product with Photos
1. Navigate to Products → Add Product
2. Fill in product details
3. For main photo:
   - Click "Upload a file" or drag image to drop zone
   - Preview appears above
4. For additional photos:
   - Click "Upload files" or drag multiple images to drop zone
   - Preview grid shows all selected photos
   - Hover and click X to remove before upload
5. Click "Create Product"

### Editing Product Photos
1. Navigate to Products → Edit (pencil icon)
2. Main photo: Drag new photo to replace
3. Existing photos section:
   - Check "Delete" on photos to remove
4. Add new photos section:
   - Drag multiple new photos
   - Preview appears in grid
5. Click "Update Product"

### Viewing Photos Count
1. Navigate to Products list
2. "Photos" column shows badge with count:
   - Green badge: "N photos" if has photos
   - Gray text: "No photos" if empty

## Database Schema

### product_photos table
- `id`: Primary key
- `product_id`: Foreign key to products
- `name`: Photo name (e.g., "Photo 1", "Photo 2")
- `photo`: File path in storage
- `timestamps`: created_at, updated_at

## Storage Location
- Main photo: `storage/app/public/products/`
- Additional photos: `storage/app/public/products/photos/`

## Validation Rules

### Create
```php
'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
'product_photos' => ['nullable', 'array'],
'product_photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
```

### Update
```php
'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
'product_photos' => ['nullable', 'array'],
'product_photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
'delete_photos' => ['nullable', 'array'],
'delete_photos.*' => ['exists:product_photos,id'],
```

## Technical Details

### Transaction Safety
All database operations are wrapped in transactions:
```php
DB::beginTransaction();
try {
    // Operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### File Naming
- Main photo: Original uploaded filename
- Additional photos: Auto-numbered "Photo 1", "Photo 2", etc.

### Memory Management
- Object URLs revoked when photos removed
- Prevents memory leaks in browser

## Browser Compatibility
- Modern browsers supporting:
  - DataTransfer API
  - FileList API
  - Object URLs
  - Alpine.js v3.x

## Benefits
- ✅ No external library dependencies (Dropzone.js not needed)
- ✅ Lightweight implementation using Alpine.js
- ✅ Consistent with existing codebase style
- ✅ Transaction-safe database operations
- ✅ Memory-efficient with proper cleanup
- ✅ Responsive design (2-4 column grid)
- ✅ Professional UI with hover effects

## Testing Checklist
- [ ] Create product with multiple photos
- [ ] Create product without photos
- [ ] Edit product: add new photos
- [ ] Edit product: delete existing photos
- [ ] Edit product: delete some, add some
- [ ] Delete product: verify all photos removed
- [ ] Check photos_count in list view
- [ ] Verify file size validation (>2MB rejected)
- [ ] Verify file type validation (non-images rejected)
- [ ] Test drag and drop on main photo
- [ ] Test drag and drop on additional photos
- [ ] Test remove photo before upload
- [ ] Check storage folder for uploaded files

## Related Documentation
- PRODUCTS_IMPLEMENTATION_SUMMARY.md
- PRODUCTS_TESTING_CHECKLIST.md

## Next Steps
1. Test all functionality via web interface
2. Create sample products with various photo counts
3. Verify storage cleanup on deletion
4. Consider adding photo reordering feature (future)
5. Consider adding photo zoom/lightbox (future)

## Notes
- Main photo remains single upload (can be replaced)
- Additional photos are unlimited (controlled by validation)
- Photos stored separately from main product photo
- Photo URLs use Laravel's asset() helper for public access
- Default "no-image.png" shown for missing photos
