@props([
    'name',
    'show' => false,
    'maxWidth' => 'lg', // Bootstrap modal max-widths: sm, md, lg, xl, xxl
    'backdrop' => true, // Thêm tùy chọn để kiểm soát backdrop (true/false/'static')
    'keyboard' => true, // Thêm tùy chọn để kiểm soát đóng bằng Esc
])

@php
    // Ánh xạ maxWidth sang class của Bootstrap modal
    $maxWidthClass =
        [
            'sm' => 'modal-sm',
            'md' => '', // Bootstrap 5 md là mặc định, không cần class riêng
            'lg' => 'modal-lg',
            'xl' => 'modal-xl',
            'xxl' => 'modal-fullscreen', // modal-xxl không tồn tại, dùng modal-fullscreen hoặc định nghĩa riêng
        ][$maxWidth] ?? '';

    // Nếu muốn xxl giống xl hoặc chỉ lớn hơn lg, bạn có thể tự định nghĩa
    // hoặc sử dụng modal-fullscreen nếu bạn muốn nó chiếm toàn màn hình
    if ($maxWidth === 'xxl') {
        $maxWidthClass = 'modal-xxl'; // Giả định bạn có CSS tùy chỉnh cho modal-xxl
    }
@endphp

<div x-data="{
    show: @js($show),
    // Cần đảm bảo rằng Alpine.js và Bootstrap 5 được tải đúng cách.
    // `modal-open` class được xử lý bởi Bootstrap's JS khi nó mở/đóng modal.
    // Logic focusables của bạn vẫn ổn, nhưng Bootstrap 5 đã có sẵn xử lý focus.
    // Tuy nhiên, giữ lại để đảm bảo tương thích với các component x-input, x-textarea của bạn.
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
}" x-init="// Khởi tạo Bootstrap Modal instance
let bsModal = new bootstrap.Modal($el, {
    backdrop: '{{ $backdrop === true ? 'true' : ($backdrop === 'static' ? 'static' : 'false') }}',
    keyboard: @js($keyboard)
});

$watch('show', value => {
    if (value) {
        bsModal.show();
        {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
    } else {
        bsModal.hide();
    }
});

// Lắng nghe sự kiện hide/hidden của Bootstrap modal để cập nhật trạng thái `show` của Alpine
$el.addEventListener('hidden.bs.modal', () => {
    show = false;
});

// Ngăn chặn đóng modal khi click vào backdrop nếu backdrop là 'static'
if ('{{ $backdrop }}' === 'static') {
    $el.querySelector('.modal-dialog').addEventListener('click', (e) => {
        if (e.target === $el.querySelector('.modal-dialog')) {
            e.stopPropagation(); // Ngăn chặn sự kiện click lan truyền lên backdrop
        }
    });
}"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="{{ $keyboard ? 'show = false' : '' }}" {{-- Sử dụng prop keyboard --}}
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()" class="modal fade" tabindex="-1" aria-hidden="true"
    {{-- Sửa từ aria-modal="true" role="dialog" sang aria-hidden="true" cho trạng thái ban đầu --}} aria-labelledby="{{ $name }}Label" {{-- Thêm aria-labelledby cho accessibility --}}>
    <div class="modal-dialog {{ $maxWidthClass }} modal-dialog-centered">
        <div class="modal-content">
            {{ $slot }}
        </div>
    </div>
</div>
