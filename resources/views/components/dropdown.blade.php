@props(['align' => 'right', 'width' => 'auto', 'contentClasses' => 'bg-white'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'dropdown-menu-start',
        'top' => '', // Bootstrap doesn't support top-aligned dropdowns out of the box
    default => 'dropdown-menu-end',
};

$width = match ($width) {
    '48' => 'w-auto', // Bootstrap dropdowns are auto width by default
        default => $width,
    };
@endphp

<div class="position-relative me-1" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open" class="dropdown-toggle" role="button" aria-expanded="false">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition:enter="fade show" x-transition:leave="fade"
        class="dropdown-menu {{ $alignmentClasses }} {{ $width }}" style="display: none;" @click="open = false">
        <div class="dropdown-item-text {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
