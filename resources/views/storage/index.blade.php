<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Storage Report']]" />
    </x-slot>

    <div class="py-2">
        <div class="container">

            <x-alert-message />

            <div class="mb-2">
                <form action="{{ route('storage.clear-all') }}" method="POST"
                    onsubmit="return confirm('@lang('Are you sure you want to delete all media files? This action cannot be undone.')');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger text-uppercase fw-semibold text-xs">
                        <i class="bi bi-trash2"></i> {{ __('Delete all Media data') }}
                    </button>
                </form>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-5 p-4 bg-light rounded-3 shadow-sm">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary">{{ __('Total files') }}</p>
                                <p class="h4 fw-bold mb-0 text-primary">{{ $totalFiles }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary">{{ __('Total storage used') }}</p>
                                <p class="h4 fw-bold mb-0 text-success">{{ $totalSizeReadable }}</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="h6 fw-bold mb-3">{{ __('Media File List') }}</h3>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Disk') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Image / File name') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Size') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Created At') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-center text-uppercase small fw-bold">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allMediaFiles as $media)
                                    <tr>
                                        <td>{{ $media->disk }}</td>
                                        <td>{{ $media->name }}</td>
                                        <td>{{ $media->mime_type }}</td>
                                        <td>{{ format_size_units($media->size) }}
                                        </td>
                                        <td>{{ $media->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('storage.destroy', $media->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('@lang('Are you sure you want to delete this file?')');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="tooltip" data-bs-title="@lang('Delete')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary">
                                            {{ __('No data.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $allMediaFiles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
