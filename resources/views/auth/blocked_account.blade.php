<x-guest-layout>
    <div class="mb-3 text-muted small">
        {{ __('Tài khoản của bạn đã bị khóa.') }}
    </div>

    <div class="mb-3 text-danger fw-medium small">
        {{ session('status') ?? 'Vui lòng liên hệ quản trị viên để biết thêm chi tiết.' }}
    </div>
</x-guest-layout>
