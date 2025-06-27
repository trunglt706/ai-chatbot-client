@props(['active'])

@php
    $classes = $active ?? false ? 'nav-link active fw-medium w-100 text-start' : 'nav-link fw-medium w-100 text-start';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
