<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Roles']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <form action="{{ route('roles.index') }}" method="GET" class="d-flex">
                                <x-text-input value="{{ $search }}" id="search" class="form-control"
                                    name="search" placeholder="Search" />
                            </form>
                        </div>
                        <div class="btn-group">
                            @can('create roles')
                                <x-href-button icon="bi bi-plus" url="{{ route('roles.create') }}" name="Add New" />
                            @endcan
                            <x-href-button class="btn-secondary" icon="bi bi-sort-down" url="{{ route('roles.order') }}"
                                name="Order Roles" />
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        @lang('Name')
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        @lang('Permissions')
                                    </th>
                                    <th scope="col" class="text-center text-uppercase small fw-bold">
                                        @lang('Actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td class="text-wrap">
                                            @foreach ($role->permissions as $permission)
                                                <span class="badge bg-success mb-1">
                                                    {{ __($permission->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="text-center">
                                            @can('edit roles')
                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                    class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip"
                                                    data-bs-title="@lang('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete roles')
                                                @if ($role->name != 'admin')
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                        class="d-inline" onsubmit="return confirm('@lang('Are you sure you want to delete this role?')');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="tooltip" data-bs-title="@lang('Delete')">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
