@props(['active' => false])

@php
    $baseClasses = 'block w-full px-4 py-2 text-start text-sm leading-5 transition duration-150 ease-in-out';
    $defaultTextColor = 'text-gray-700 dark:text-gray-300';
    $hoverFocus =
        'hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800';

    $activeClass = $active ? 'bg-gray-100 dark:bg-gray-800 font-semibold' : $defaultTextColor;
@endphp

<a {{ $attributes->merge(['class' => "$baseClasses $activeClass $hoverFocus"]) }}>
    {{ $slot }}
</a>
