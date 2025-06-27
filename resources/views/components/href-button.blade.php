@props(['url', 'name', 'icon' => null])

<a href="{{ $url }}"
    {{ $attributes->merge(['class' => 'btn btn-primary text-uppercase fw-semibold text-xs d-inline-flex align-items-center gap-1']) }}>

    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif

    {{ __($name) }}
</a>
