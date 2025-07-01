<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Sponsors')]]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <form action="{{ route('sponsors.index') }}" method="GET" class="d-flex">
                                <x-text-input value="{{ $search }}" id="search" class="form-control"
                                    name="search" placeholder="Search" />
                            </form>
                        </div>
                        @can('create sponsors')
                            <x-href-button icon="bi bi-plus" url="{{ route('sponsors.create') }}" name="Add New" />
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Code') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Name') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Image') }}
                                    </th>
                                    <th scope="col" class="text-center text-uppercase small fw-bold">
                                        {{ __('Modules') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-center text-uppercase small fw-bold">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sponsors as $sponsor)
                                    <tr>
                                        <td>{{ $sponsor->code }}</td>
                                        <td>{{ $sponsor->name }}</td>
                                        <td>
                                            @if ($sponsor->logo_url)
                                                <img src="{{ $sponsor->logo_url }}" alt="{{ $sponsor->name }} Logo"
                                                    class="img-thumbnail"
                                                    style="width: 50px; height: 50px; object-fit: contain;">
                                            @else
                                                <i class="bi bi-image-fill text-muted" style="font-size: 2rem;"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $sponsor->modules()->count() }}</td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                                @if ($sponsor->status === 'active') bg-success
                                                @elseif($sponsor->status === 'inactive') bg-danger
                                                @else bg-warning text-dark @endif">
                                                {{ ucfirst($sponsor->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('sponsors.edit', $sponsor) }}"
                                                class="btn btn-primary btn-sm me-2" data-bs-toggle="tooltip"
                                                data-bs-title="@lang('Edit')">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('sponsors.destroy', $sponsor) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('@lang('Are you sure you want to delete this sponsor?')');">
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
                        {{ $sponsors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
