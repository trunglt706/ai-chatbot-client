@php
    use App\Services\ModuleService;
    $ModuleService = new ModuleService();
@endphp
<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Modules']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                @forelse ($modules as $module)
                    @php
                        $status = $ModuleService->getStatus($module->status);
                    @endphp
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0 text-truncate">{{ __($module->name) }}</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <p class="card-text mb-2">
                                    <strong>{{ __('messages.version') }}:</strong>
                                    {{ $module->version ?? __('N/A') }}
                                </p>
                                <p class="card-text mb-2">
                                    <strong>{{ __('messages.status') }}:</strong>
                                    {{ $status }}
                                </p>
                                <p class="card-text mb-2">
                                    <strong>{{ __('Publish At') }}:</strong>
                                    {{ $module->created_at->format('d/m/Y') ?? __('N/A') }}
                                </p>
                                <p class="card-text mb-2">
                                    <strong>{{ __('Updated At') }}:</strong>
                                    {{ $module->updated_at->format('d/m/Y') ?? __('N/A') }}
                                </p>
                            </div>
                            <div
                                class="card-footer d-flex justify-content-between align-items-center bg-light border-top-0">
                                @can('edit modules')
                                    <a href="{{ route('modules.edit', $module->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> {{ __('Edit') }}
                                    </a>
                                @endcan

                                @if ($module->url)
                                    <a href="{{ $module->url }}" target="_blank"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> {{ __('View Demo') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            {{ __('No data.') }}
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $modules->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
