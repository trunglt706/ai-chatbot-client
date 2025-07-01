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
                    {{ __('Your profile information has been updated.') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    {{-- Cột trái: Thông tin cập nhật hồ sơ --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Các phần khác của profile (mật khẩu, xóa tài khoản) giữ nguyên --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="mb-4">
                        {{-- Cột phải: Thông tin người dùng --}}
                        <x-user-info :user="Auth::user()" />
                    </div>

                    {{-- User Sessions Card --}}
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title h5 mb-4">{{ __('Active Sessions') }}</h3>

                            @forelse ($sessions as $session)
                                <div class="border-bottom pb-2 mb-3 {{ $loop->last ? 'border-0' : '' }}">
                                    <p class="mb-1 text-dark">
                                        @php
                                            // Tạo một instance của Agent với user_agent của phiên hiện tại
                                            $agent = new Jenssegers\Agent\Agent();
                                            $agent->setUserAgent($session->user_agent);
                                        @endphp
                                        <strong>{{ $agent->platform() }}</strong> -
                                        {{ $agent->browser() }}
                                        ({{ $agent->deviceType() }})
                                    </p>
                                    <p class="text-muted small mb-0">
                                        IP: {{ $session->ip_address }}<br>
                                        {{ __('Last Activity') }}:
                                        {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                        ({{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->format('d M Y, H:i') }})
                                    </p>
                                    <div class="text-end mt-2">
                                        @if ($session->id !== session()->getId())
                                            <form method="POST"
                                                action="{{ route('users.sessions.destroy', ['user' => $user->id, 'sessionId' => $session->id]) }}"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this session?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-link text-danger p-0 border-0 text-decoration-none">
                                                    {{ __('Delete Session') }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-success">{{ __('Current Session') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">{{ __('No active sessions found for this user.') }}</p>
                            @endforelse

                            <div class="mb-3">
                                <form method="POST" action="{{ route('users.logout-all-devices', $user->id) }}"
                                    onsubmit="return confirm('{{ __('Are you sure you want to log out this user from all other devices?') }}');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        {{ __('Logout All Other Devices') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
