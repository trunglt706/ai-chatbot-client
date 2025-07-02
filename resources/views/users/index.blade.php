<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Users']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <form action="{{ route('users.index') }}" method="GET" class="d-flex">
                                <x-text-input value="{{ $search }}" id="search" class="form-control"
                                    name="search" placeholder="Search" />
                            </form>
                        </div>
                        @can('create users')
                            <x-href-button icon="bi bi-plus" url="{{ route('users.create') }}" name="Add New" />
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        @lang('Name')
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        @lang('Email')
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        @lang('Roles')
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        @lang('Created At')
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        @lang('Status')
                                    </th>
                                    <th scope="col" width="10%" class="text-center text-uppercase small fw-bold">
                                        @lang('Actions')
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>
                                            {{ $user->name }}
                                            @php
                                                $statusInfo = $onlineStatuses[$user->id] ?? [
                                                    'is_online' => false,
                                                    'last_activity_at' => null,
                                                ];
                                                $isOnline = $statusInfo['is_online'];
                                                $lastActivity = $statusInfo['last_activity_at'];

                                                if ($lastActivity) {
                                                    $tooltipText =
                                                        __('Last activity') .
                                                        ': ' .
                                                        $lastActivity->format('d M Y, H:i:s');
                                                } else {
                                                    $tooltipText = __('No recent activity');
                                                }
                                            @endphp
                                            <span class="badge {{ $isOnline ? 'bg-success' : 'bg-secondary' }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ $tooltipText }}">
                                                {{ $isOnline ? __('On') : __('Off') }}
                                            </span>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span class="badge bg-primary mb-1">
                                                    {{ __($role->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            {{ $user->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                                @if ($user->status === 'active') bg-success
                                                @elseif($user->status === 'blocked') bg-danger
                                                @else bg-warning text-dark @endif">
                                                {{ $user->status ? __(ucfirst($user->status)) : __('N/A') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @can('edit users')
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                    class="btn btn-primary btn-sm me-2" data-bs-toggle="tooltip"
                                                    data-bs-title="@lang('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan
                                            @can('delete users')
                                                @if ($user->id !== auth()->user()->id)
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        class="d-inline" onsubmit="return confirm('@lang('Are you sure you want to delete this user?')');">
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
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
