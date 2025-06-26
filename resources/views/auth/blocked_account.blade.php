<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Tài khoản của bạn đã bị khóa.') }}
    </div>

    <div class="mb-4 font-medium text-sm text-red-600 dark:text-red-400">
        {{ session('status') ?? 'Vui lòng liên hệ quản trị viên để biết thêm chi tiết.' }}
    </div>
</x-guest-layout>
