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
                <div>
                    <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg h-full"> {{-- h-full để card kéo dài bằng cột bên cạnh --}}
                        <div class="max-w-xl">
                            <section>
                                <header>
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Thông tin người dùng') }}
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Các thông tin cơ bản về tài khoản của bạn.') }}
                                    </p>
                                </header>

                                <div class="mt-6 space-y-4">
                                    <div>
                                        <x-input-label for="code" :value="__('User Code')" />
                                        <p id="code" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ Auth::user()->code ?? 'N/A' }}</p>
                                    </div>

                                    <div>
                                        <x-input-label for="registration_date" :value="__('Created At')" />
                                        <p id="registration_date" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ Auth::user()->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>

                                    <div>
                                        <x-input-label for="last_updated" :value="__('Last Updated At')" />
                                        <p id="last_updated" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ Auth::user()->updated_at->format('d/m/Y H:i:s') }}</p>
                                    </div>

                                    <div>
                                        <x-input-label for="status" :value="__('Status')" />
                                        <p id="status" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if (Auth::user()->status === 'active') bg-green-100 text-green-800
                                                @elseif(Auth::user()->status === 'blocked') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ Auth::user()->status ? ucfirst(Auth::user()->status) : 'N/A' }}
                                            </span>
                                        </p>
                                    </div>

                                    <div>
                                        <x-input-label for="description" :value="__('Description')" />
                                        <p id="description" class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            {{ Auth::user()->description ?? '' }}</p>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>

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
