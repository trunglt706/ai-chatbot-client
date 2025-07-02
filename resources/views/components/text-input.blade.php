@props(['disabled' => false, 'required' => false, 'label' => null, 'placeholder' => null])

@php
    $type = $attributes->get('type', 'text');
    $name = $attributes->get('name');
    $cleanedAttributes = $attributes->except(['type', 'label', 'required']);
    $errorClass = $name && $errors->has($name) ? 'is-invalid' : '';
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
                'class' => 'form-control ' . $errorClass,
            ]) }}>

        <button class="btn btn-secondary toggle-password" type="button" data-target="{{ $attributes->get('id') }}">
            <i class="bi bi-eye"></i>
        </button>
    </div>
@else
    <input placeholder="{{ __($placeholder) }}" @disabled($disabled)
        @if ($required) required @endif
        {{ $attributes->merge([
            'type' => $type,
            'class' => 'form-control ' . $errorClass,
        ]) }}>
@endif
@error($name)
    <div class="invalid-feedback">{{ $message }}</div>
@enderror
