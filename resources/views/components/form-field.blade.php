@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'autocomplete' => 'off',
    'autofocus' => false,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <input 
        type="{{ $type }}" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        value="{{ old($name, $value) }}"
        @if($required) required @endif
        @if($autofocus) autofocus @endif
        autocomplete="{{ $autocomplete }}"
        aria-label="{{ $label }}"
        @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm @error($name) border-red-500 @enderror"
    />
    
    @error($name)
        <p id="{{ $name }}-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
    @enderror
</div>
