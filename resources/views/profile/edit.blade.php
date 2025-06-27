<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ __('Thông tin hồ sơ của bạn đã được cập nhật.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- Cột trái: Thông tin cập nhật hồ sơ --}}
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Cột phải: Thông tin người dùng --}}
                <div class="col-12 col-lg-6">
                    <x-user-info :user="Auth::user()" />
                </div>
            </div>

            {{-- Các phần khác của profile (mật khẩu, xóa tài khoản) giữ nguyên --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
