@props([
    'name',
    'show' => false,
    'maxWidth' => 'lg', // Bootstrap modal max-widths: sm, md, lg, xl, xxl
])

@php
    $maxWidth =
        [
            'sm' => 'modal-sm',
            'md' => '',
            'lg' => 'modal-lg',
            'xl' => 'modal-xl',
            'xxl' => 'modal-xxl',
        ][$maxWidth] ?? '';
@endphp

<div x-data="{
    show: @js($show),
    focusables() {
        let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
        return [...$el.querySelectorAll(selector)]
            .filter(el => !el.hasAttribute('disabled'))
    },
    firstFocusable() { return this.focusables()[0] },
    lastFocusable() { return this.focusables().slice(-1)[0] },
    nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
    prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
    nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
    prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
}" x-init="$watch('show', value => {
    if (value) {
        document.body.classList.add('modal-open');
        {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
    } else {
        document.body.classList.remove('modal-open');
    }
})"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null" x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false" x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" x-show="show" class="modal fade show" tabindex="-1"
    style="display: {{ $show ? 'block' : 'none' }};" aria-modal="true" role="dialog">
    <div x-show="show" class="modal-dialog {{ $maxWidth }} modal-dialog-centered" x-on:click.self="show = false"
        x-transition:enter="fade" x-transition:leave="fade">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
    <div x-show="show" class="modal-backdrop fade show"></div>
</div>
