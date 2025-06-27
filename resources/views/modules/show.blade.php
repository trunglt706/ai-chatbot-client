<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-semibold text-dark">
            {{ __('Chi tiết Module') }}: {{ $module->name }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">

                    <div class="mb-4">
                        <h5 class="text-muted">{{ __('Tên Module') }}</h5>
                        <p class="fs-5 fw-semibold">{{ $module->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted">{{ __('Mô tả') }}</h5>
                        <p>{{ $module->description }}</p>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted">{{ __('Trạng thái') }}</h5>
                        @php
                            $statusClasses = [
                                \App\Models\Module::STATUS_PUBLISHED => 'bg-success text-white',
                                \App\Models\Module::STATUS_DEVELOPING => 'bg-primary text-white',
                                \App\Models\Module::STATUS_PAUSED => 'bg-warning text-dark',
                                'default' => 'bg-danger text-white',
                            ];
                            $statusClass = $statusClasses[$module->status] ?? $statusClasses['default'];
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ \App\Models\Module::getStatuses()[$module->status] ?? 'Không xác định' }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-muted">{{ __('Ngày cập nhật cuối') }}</h5>
                        <p>{{ $module->updated_at->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            {{ __('Quay lại') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
