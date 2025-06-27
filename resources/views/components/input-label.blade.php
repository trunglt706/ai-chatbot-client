@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'form-label fw-medium small text-dark']) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
