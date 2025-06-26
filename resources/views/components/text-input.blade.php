@props(['disabled' => false, 'required' => false, 'label' => null])

@php
    $type = $attributes->get('type', 'text');
    $cleanedAttributes = $attributes->except(['type', 'label', 'required']);
@endphp

@if ($label)
    <label for="{{ $attributes->get('id') }}" class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>
@endif

@if ($type === 'password')
    <div class="input-group">
        <input @disabled($disabled) @if ($required) required @endif
            {{ $cleanedAttributes->merge([
                'type' => 'password',
                'class' =>
                    'form-control border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm',
            ]) }}>

        <button class="btn btn-outline-secondary toggle-password" type="button"
            data-target="{{ $attributes->get('id') }}">
            <i class="bi bi-eye"></i>
        </button>
    </div>
@else
    <input @disabled($disabled) @if ($required) required @endif
        {{ $attributes->merge([
            'type' => $type,
            'class' =>
                'form-control border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm',
        ]) }}>
@endif
