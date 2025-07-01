@props(['sponsor'])

<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Sponsors'), 'url' => route('sponsors.index')], ['name' => __('Edit Sponsor')]]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="row">
                <div class="col-md-10 mb-2">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <form method="POST" action="{{ route('sponsors.update', $sponsor) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <x-input-label required for="name" :value="__('Name')" />
                                        <x-text-input id="name" class="form-control mt-1" type="text"
                                            name="name" :value="old('name', $sponsor->name)" required autofocus />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <x-input-label for="image" :value="__('Image') . __(' (JPG, PNG, JPEG, GIF, SVG)')" />
                                        <input id="image" type="file" name="image" class="form-control mt-1"
                                            accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml" />
                                        <small class="form-text text-muted mt-1">
                                            {{ __('Leave blank to keep current image.') }}
                                        </small>
                                        <x-input-error :messages="$errors->get('image')" class="mt-2" />
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <x-input-label for="status" :value="__('Status')" />
                                        <select id="status" name="status" class="form-select mt-1" required>
                                            <option value="active"
                                                {{ old('status', $sponsor->status) == 'active' ? 'selected' : '' }}>
                                                {{ __('Active') }}</option>
                                            <option value="inactive"
                                                {{ old('status', $sponsor->status) == 'inactive' ? 'selected' : '' }}>
                                                {{ __('Inactive') }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <x-textarea id="description" name="description"
                                        class="form-control mt-1">{{ old('description', $sponsor->description) }}</x-textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <x-primary-button>
                                        <i class="bi bi-floppy-fill"></i> {{ __('Save Changes') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Thêm phần hiển thị danh sách Module đã tài trợ --}}
                    <div class="card shadow-sm mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ __('Sponsored Modules') }}</h5>
                            {{-- Nút mở modal Bootstrap 5 thuần --}}
                            @if ($availableModules->isNotEmpty())
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#registerSponsorshipModal">
                                    <i class="bi bi-plus-circle-fill me-1"></i> {{ __('Add New Sponsorship') }}
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if ($sponsor->modules->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="text-start text-uppercase small fw-bold">
                                                    {{ __('Module Name') }}
                                                </th>
                                                <th scope="col" width="10%"
                                                    class="text-start text-uppercase small fw-bold">
                                                    {{ __('Total Amount') }}
                                                </th>
                                                <th scope="col" width="10%"
                                                    class="text-start text-uppercase small fw-bold">
                                                    {{ __('Start Date') }}
                                                </th>
                                                <th scope="col" width="10%"
                                                    class="text-start text-uppercase small fw-bold">
                                                    {{ __('End Date') }}
                                                </th>
                                                <th scope="col" width="10%"
                                                    class="text-center text-uppercase small fw-bold">
                                                    {{ __('Actions') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sponsor->modules as $module)
                                                <tr>
                                                    <td>
                                                        {{ $module->name }}
                                                    </td>
                                                    <td>
                                                        {{ number_format($module->pivot->total_amount, 0, ',', '.') }}
                                                        {{ __('VND') }}
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($module->pivot->start_date)->format('d/m/Y') }}
                                                    </td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($module->pivot->end_date)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-center">
                                                        <form
                                                            action="{{ route('sponsors.cancelModuleSponsorship', [$sponsor, $module]) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('@lang('Are you sure you want to delete this sponsorship?')');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-title="@lang('Delete')">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">
                                    {{ __('This sponsor has not sponsored any modules yet.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-2 text-center">
                    <img src="{{ !empty($sponsor->logo_url) ? $sponsor->logo_url : asset('images/no-image.jpeg') }}"
                        alt="{{ $sponsor->name }}" class="img-thumbnail">
                    <p class="mt-1">{{ __('Current Image') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Bootstrap 5 thuần cho chức năng đăng ký tài trợ module mới --}}
    <div class="modal fade" id="registerSponsorshipModal" tabindex="-1" aria-labelledby="registerSponsorshipModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerSponsorshipModalLabel">
                        {{ __('Register New Module Sponsorship') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('sponsors.attachModule', $sponsor) }}">
                    @csrf
                    <div class="modal-body">
                        @if ($availableModules->isNotEmpty())
                            <div class="mb-3">
                                <x-input-label for="module_id" required :value="__('Select Module')" />
                                <select id="module_id" name="module_id"
                                    class="form-select @error('module_id', 'attachModule') is-invalid @enderror"
                                    required>
                                    <option value="">--- {{ __('Select') }} ---</option>
                                    @foreach ($availableModules as $module)
                                        <option value="{{ $module->id }}"
                                            {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                            {{ $module->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('module_id', 'attachModule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <x-input-label for="total_amount" required :value="__('Total Amount')" />
                                <input id="total_amount"
                                    class="form-control @error('total_amount', 'attachModule') is-invalid @enderror"
                                    type="number" name="total_amount" value="{{ old('total_amount') }}" required
                                    min="0" step="1000" />
                                @error('total_amount', 'attachModule')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <x-input-label for="start_date" required :value="__('Start Date')" />
                                    <input id="start_date"
                                        class="form-control @error('start_date', 'attachModule') is-invalid @enderror"
                                        type="date" name="start_date"
                                        value="{{ old('start_date', date('Y-m-d')) }}" required />
                                    @error('start_date', 'attachModule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <x-input-label for="end_date" required :value="__('End Date')" />
                                    <input id="end_date"
                                        class="form-control @error('end_date', 'attachModule') is-invalid @enderror"
                                        type="date" name="end_date" value="{{ old('end_date') }}" required />
                                    @error('end_date', 'attachModule')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <p class="text-muted">
                                {{ __('All modules are already sponsored by this sponsor, or no modules available.') }}
                            </p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if ($availableModules->isNotEmpty())
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="bi bi-plus-circle-fill me-1"></i> {{ __('Add Sponsorship') }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
