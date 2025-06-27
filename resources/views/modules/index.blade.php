<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Modules']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 mb-0 fw-semibold">{{ __('messages.module_list') }}</h3>
                        <div class="d-flex align-items-center">
                            <x-text-input id="search" class="form-control" name="search" placeholder="Search" />
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('messages.module_name') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('messages.module_code') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('messages.description') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('messages.status') }}
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('messages.actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modules as $module)
                                    <tr>
                                        <td class="fw-medium">
                                            {{ __($module->name) }}
                                        </td>
                                        <td>
                                            {{ $module->code }}
                                        </td>
                                        <td>
                                            {{ __("messages.module_descriptions.{$module->code}", ['default' => $module->description]) }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                                {{ $module->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $module->is_active ? __('messages.active') : __('messages.inactive') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @can('edit modules')
                                                <a href="{{ route('modules.edit', $module->id) }}" class="text-primary"
                                                    data-bs-toggle="tooltip" data-bs-title="@lang('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $modules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
