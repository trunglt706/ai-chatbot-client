<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'profile-updated')
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                    role="alert">
                    {{ __('Thông tin hồ sơ của bạn đã được cập nhật.') }}
                </div>
            @endif

            {{-- Bố cục 2 cột cho màn hình lớn --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Cột trái: Thông tin cập nhật hồ sơ --}}
                <div>
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Cột phải: Thông tin người dùng --}}
                <x-user-info :user="Auth::user()" />

            </div> {{-- Kết thúc grid --}}

            {{-- Các phần khác của profile (mật khẩu, xóa tài khoản) giữ nguyên --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
