<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'System Info']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="row g-4">
                {{-- Card: Symbolic Link Info --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Storage Link Status') }}</h4>
                        <p class="text-secondary mb-1">
                            {{ __('Public path for uploaded files.') }}
                        </p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="fw-bold me-2">{{ __('Status:') }}</span>
                            @if ($storageLinkExists)
                                <span class="badge bg-success">{{ __('Created') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Not created') }}</span>
                            @endif
                        </div>
                        @if (!$storageLinkExists)
                            <form action="{{ route('system.create_storage_link') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit">
                                    {{ __('Create Symbolic Link now') }}
                                </x-primary-button>
                            </form>
                            <p class="small text-muted mt-2">
                                {{ __('This allows uploaded files to be publicly accessible.') }}
                            </p>
                        @else
                            <p class="text-secondary">
                                {{ __('Symbolic link already exists and is active.') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Card: Public Storage Size Info --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Public Storage Size') }}</h4>
                        <p class="text-secondary mb-1">
                            {{ __('Total size of publicly uploaded files.') }}
                        </p>
                        <div class="d-flex align-items-center mb-3">
                            <span class="fw-bold me-2">{{ __('Size:') }}</span>
                            <span>{{ $publicStorageSize }}</span>
                        </div>
                        <p class="small text-muted mt-2">
                            {{ __('Includes sponsor images, post images, etc.') }}
                        </p>
                    </div>
                </div>

                {{-- Card: General System Info --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('General Information') }}</h4>
                        <p class="mb-2">
                            <span class="fw-bold">{{ __('Laravel Version:') }}</span> {{ app()->version() }}
                        </p>
                        <p class="mb-2">
                            <span class="fw-bold">{{ __('Environment:') }}</span> {{ app()->environment() }}
                        </p>
                        <p class="text-secondary mb-0">
                            {{ __('Helps check the current configuration and environment of the application.') }}
                        </p>
                    </div>
                </div>

                {{-- Card: Clear All System Cache --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Clear System Cache') }}</h4>
                        <p class="text-secondary mb-3">
                            {{ __('Clear all caches (config, route, view, application) to refresh the system.') }}
                        </p>
                        <form action="{{ route('system.clear_all_data') }}" method="POST">
                            @csrf
                            <x-primary-button type="submit" class="btn-warning">
                                {{ __('Clear All Cache') }}
                            </x-primary-button>
                        </form>
                        <p class="small text-muted mt-2">
                            {{ __('Recommended after configuration or source code changes.') }}
                        </p>
                    </div>
                </div>

                {{-- Card: Broadcasting Status and Test --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Broadcasting Status') }}</h4>
                        <p class="text-secondary mb-1">
                            {{ __('Check Real-time Broadcasting configuration.') }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold me-2">{{ __('Config:') }}</span>
                            @if ($broadcastConfigStatus['status'] === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            {{ $broadcastConfigStatus['message'] }}
                        </p>
                        <p class="small text-muted mb-2">
                            <span class="fw-bold">{{ __('Driver:') }}</span>
                            {{ $broadcastConfigStatus['driver'] }}
                        </p>
                        @if (!empty($broadcastConfigStatus['config']))
                            <ul class="small text-muted mb-2 ps-3">
                                @foreach ($broadcastConfigStatus['config'] as $key => $value)
                                    <li><span class="fw-bold">{{ $key }}:</span> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-3 pt-3 border-top">
                            <h5 class="fw-semibold mb-2">{{ __('Test functionality:') }}</h5>
                            <form action="{{ route('system.test_broadcast') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit" class="btn-primary">
                                    {{ __('Send Test Event') }}
                                </x-primary-button>
                            </form>
                            @if (session('broadcast_test_result'))
                                @php $result = session('broadcast_test_result'); @endphp
                                <div
                                    class="mt-2 small {{ $result['status'] === 'success' ? 'text-success' : 'text-danger' }}">
                                    {{ $result['message'] }}
                                </div>
                            @endif
                            <p class="small text-muted mt-2">
                                {{ __('Check the log or dashboard of the broadcasting service (Pusher, Redis, etc.) to verify.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Card: Email Status and Test --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Email Status') }}</h4>
                        <p class="text-secondary mb-1">
                            {{ __('Check system email sending configuration.') }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold me-2">{{ __('Config:') }}</span>
                            @if ($emailConfigStatus['status'] === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            {{ $emailConfigStatus['message'] }}
                        </p>
                        <p class="small text-muted mb-2">
                            <span class="fw-bold">{{ __('Driver:') }}</span> {{ $emailConfigStatus['driver'] }}
                        </p>
                        @if (!empty($emailConfigStatus['config']))
                            <ul class="small text-muted mb-2 ps-3">
                                @foreach ($emailConfigStatus['config'] as $key => $value)
                                    <li><span class="fw-bold">{{ $key }}:</span> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-3 pt-3 border-top">
                            <h5 class="fw-semibold mb-2">{{ __('Test functionality:') }}</h5>
                            <form action="{{ route('system.test_email') }}" method="POST">
                                @csrf
                                <x-input-label for="email_recipient" :value="__('Test recipient email')" class="visually-hidden" />
                                <x-text-input id="email_recipient" name="email_recipient" type="email"
                                    class="form-control mb-2" placeholder="Enter test recipient email" required />
                                <x-primary-button type="submit" class="btn-primary">
                                    {{ __('Send Test Email') }}
                                </x-primary-button>
                            </form>
                            @if (session('email_test_result'))
                                @php $result = session('email_test_result'); @endphp
                                <div
                                    class="mt-2 small {{ $result['status'] === 'success' ? 'text-success' : 'text-danger' }}">
                                    {{ $result['message'] }}
                                </div>
                            @endif
                            <p class="small text-muted mt-2">
                                {{ __('An email will be sent to the address you provide.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Card: Slack Status and Test --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card card-body h-100">
                        <h4 class="h6 fw-bold mb-3">{{ __('Slack Status') }}</h4>
                        <p class="text-secondary mb-1">
                            {{ __('Check Slack integration configuration.') }}
                        </p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fw-bold me-2">{{ __('Config:') }}</span>
                            @if ($slackConfigStatus['status'] === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ __('Inactive') }}</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            {{ $slackConfigStatus['message'] }}
                        </p>
                        @if (!empty($slackConfigStatus['config']))
                            <ul class="small text-muted mb-2 ps-3">
                                @foreach ($slackConfigStatus['config'] as $key => $value)
                                    <li><span class="fw-bold">{{ $key }}:</span> {{ $value }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <div class="mt-3 pt-3 border-top">
                            <h5 class="fw-semibold mb-2">{{ __('Test functionality:') }}</h5>
                            <form action="{{ route('system.test_slack') }}" method="POST">
                                @csrf
                                <x-primary-button type="submit" class="btn-primary">
                                    {{ __('Send Test Notification') }}
                                </x-primary-button>
                            </form>
                            @if (session('slack_test_result'))
                                @php $result = session('slack_test_result'); @endphp
                                <div
                                    class="mt-2 small {{ $result['status'] === 'success' ? 'text-success' : 'text-danger' }}">
                                    {{ $result['message'] }}
                                </div>
                            @endif
                            <p class="small text-muted mt-2">
                                {{ __('A notification will be sent to the configured Slack channel.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
