<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Modules', 'url' => route('modules.index')], ['name' => 'Edit Module']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('modules.update', $module->id) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <x-input-label required for="name" :value="__('messages.module_name')" />
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $module->name) }}" required readonly>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <x-input-label for="code" :value="__('Version')" />
                                <input type="text" class="form-control" id="version" name="version"
                                    value="{{ old('version', $module->version) }}">
                                @error('version')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <x-input-label required for="status" :value="__('Status')" />
                                <select name="status" id="status" class="form-select" required>
                                    @foreach ($statuses as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('status', $module->status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('messages.description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $module->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <x-primary-button>
                                <i class="bi bi-floppy-fill"></i> {{ __('messages.update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
