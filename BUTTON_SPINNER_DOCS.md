# Button Spinner Component Documentation

## Overview
The `button-spinner` component is a reusable Blade component that provides a professional loading state for submit buttons. It prevents double submissions and gives users visual feedback during form processing.

## Location
```
resources/views/components/button-spinner.blade.php
```

## Features
- ✅ Animated spinner icon during loading
- ✅ Customizable loading text
- ✅ Automatic form detection
- ✅ Disables button during loading
- ✅ Uses Alpine.js for reactivity
- ✅ Inherits all button styles

## Usage

### Basic Usage
```blade
<x-button-spinner>
    Submit
</x-button-spinner>
```

### With Custom Loading Text
```blade
<x-button-spinner loading-text="Processing Payment...">
    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24">
        <path d="..."/>
    </svg>
    Pay Now
</x-button-spinner>
```

### With Icon
```blade
<x-button-spinner loading-text="Creating User...">
    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
    Create User
</x-button-spinner>
```

### With Custom Type
```blade
<x-button-spinner type="button" loading-text="Saving Draft...">
    Save Draft
</x-button-spinner>
```

### With Additional Classes
```blade
<x-button-spinner class="w-full" loading-text="Logging In...">
    Login
</x-button-spinner>
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `type` | string | `'submit'` | Button type (submit, button, reset) |
| `loadingText` | string | `'Processing...'` | Text shown during loading state |
| `class` | string | `''` | Additional CSS classes (merged with default) |

## Default Classes
The component automatically inherits the `.btn .btn-primary` classes. You can override with custom classes:

```blade
<x-button-spinner class="btn btn-success">
    Save
</x-button-spinner>
```

## How It Works

### 1. Alpine.js State
```javascript
x-data="{ loading: false }"
```
Initializes a reactive `loading` state.

### 2. Click Handler
```javascript
@click="loading = true; $el.form.addEventListener('submit', () => loading = true)"
```
Sets `loading` to true when clicked and on form submit.

### 3. Disabled State
```javascript
:disabled="loading"
```
Disables the button when loading.

### 4. Conditional Display
```blade
<span x-show="!loading">{{ $slot }}</span>
<span x-show="loading" style="display: none;">
    <svg class="animate-spin ...">...</svg>
    {{ $loadingText }}
</span>
```
Shows normal content or loading state based on `loading` value.

## Animation
The spinner uses Tailwind's `animate-spin` utility:

```html
<svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white">
    <!-- SVG circle and path -->
</svg>
```

## Examples

### Login Form
```blade
<form action="{{ route('login') }}" method="POST">
    @csrf
    
    <x-form-field name="email" label="Email" type="email" />
    <x-form-field name="password" label="Password" type="password" />
    
    <x-button-spinner loading-text="Logging In..." class="w-full">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
        </svg>
        Login
    </x-button-spinner>
</form>
```

### Create Form
```blade
<form action="{{ route('users.store') }}" method="POST">
    @csrf
    
    <!-- Form fields -->
    
    <div class="flex gap-3">
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            Cancel
        </a>
        <x-button-spinner loading-text="Creating User...">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Create User
        </x-button-spinner>
    </div>
</form>
```

### Update Form
```blade
<form action="{{ route('users.update', $user) }}" method="POST">
    @csrf
    @method('PUT')
    
    <!-- Form fields -->
    
    <x-button-spinner loading-text="Updating...">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg>
        Update
    </x-button-spinner>
</form>
```

### Delete Confirmation
```blade
<form action="{{ route('users.destroy', $user) }}" method="POST">
    @csrf
    @method('DELETE')
    
    <x-button-spinner type="submit" class="btn btn-danger" loading-text="Deleting...">
        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>
        Delete
    </x-button-spinner>
</form>
```

## Styling

### Default Styling
```css
.btn.btn-primary {
    @apply bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500;
}
```

### Custom Styling
You can add custom classes:

```blade
<x-button-spinner class="px-6 py-3 text-lg rounded-full shadow-lg">
    Submit
</x-button-spinner>
```

## States

### 1. Initial State
```
[Create User]  ← Normal button with icon
```

### 2. Loading State
```
[⟳ Creating User...]  ← Spinner + custom text
```

### 3. Disabled State
```
Button is disabled (cursor-not-allowed)
```

## Browser Compatibility
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers

## Dependencies
- **Alpine.js**: For reactivity
- **Tailwind CSS**: For styling
- **SVG**: For spinner icon

## Best Practices

### 1. Use Descriptive Loading Text
```blade
<!-- ❌ Bad -->
<x-button-spinner loading-text="Loading...">Submit</x-button-spinner>

<!-- ✅ Good -->
<x-button-spinner loading-text="Creating User...">Create User</x-button-spinner>
```

### 2. Match Button Text with Loading Text
```blade
<!-- ✅ Good -->
<x-button-spinner loading-text="Logging In...">Login</x-button-spinner>
<x-button-spinner loading-text="Saving Changes...">Save</x-button-spinner>
```

### 3. Add Icons for Better UX
```blade
<x-button-spinner loading-text="Uploading File...">
    <svg class="mr-1.5 h-5 w-5"><!-- upload icon --></svg>
    Upload
</x-button-spinner>
```

### 4. Use Appropriate Button Types
```blade
<!-- Form submission -->
<x-button-spinner type="submit">Submit</x-button-spinner>

<!-- AJAX action -->
<x-button-spinner type="button" @click="handleAjax()">Process</x-button-spinner>
```

## Troubleshooting

### Spinner Not Showing
**Issue**: Button stays in normal state after click.

**Solution**:
1. Ensure Alpine.js is loaded: Check `resources/js/app.js`
2. Check browser console for errors
3. Verify form has `method="POST"` or similar

### Button Not Disabled
**Issue**: User can click multiple times.

**Solution**:
1. Check Alpine.js is initialized
2. Verify `:disabled="loading"` attribute exists
3. Check for JavaScript errors

### Spinner Stays Forever
**Issue**: Button never returns to normal state.

**Solution**:
1. Ensure form actually submits (check action/method)
2. Check for validation errors preventing submission
3. Verify redirect after successful submission

## Advanced Usage

### With Alpine.js Component
```blade
<div x-data="formHandler()">
    <form @submit.prevent="submitForm">
        <x-button-spinner loading-text="Processing...">
            Submit
        </x-button-spinner>
    </form>
</div>

<script>
function formHandler() {
    return {
        async submitForm() {
            // Your AJAX logic
            await axios.post('/api/endpoint', this.formData);
            // Redirect or show success
        }
    }
}
</script>
```

### Multiple Buttons
```blade
<div class="flex gap-3">
    <x-button-spinner loading-text="Saving..." class="btn btn-primary">
        Save
    </x-button-spinner>
    
    <x-button-spinner loading-text="Saving Draft..." class="btn btn-secondary">
        Save as Draft
    </x-button-spinner>
</div>
```

## Accessibility
- ✅ Keyboard accessible (can be focused and activated)
- ✅ Screen reader compatible
- ✅ Disabled state properly announced
- ✅ Loading state visually clear

## Performance
- ✅ Lightweight (< 1KB)
- ✅ No external dependencies (uses built-in Alpine.js)
- ✅ CSS animations (GPU accelerated)

---

**Note**: This component is part of the Estatia ERP project and follows the emerald theme color scheme.
