@props(['active' => false])

@php
    $baseClasses = 'dropdown-item text-sm';
    $activeClass = $active ? 'active fw-semibold' : '';
@endphp

<a {{ $attributes->merge(['class' => "$baseClasses $activeClass"]) }}>
    {{ $slot }}
</a>
