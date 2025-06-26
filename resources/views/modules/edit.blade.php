<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.edit_module') }}: {{ __("messages.module_names.{$module->code}") }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('modules.update', $module->id) }}">
                        @csrf
                        @method('PATCH') {{-- Hoặc @method('PUT') --}}

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('messages.module_name') }}</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', __("messages.module_names.{$module->code}")) }}" required
                                readonly> {{-- readonly vì tên thường không đổi --}}
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">{{ __('messages.module_code') }}</label>
                            <input type="text" class="form-control" id="code" name="code"
                                value="{{ old('code', $module->code) }}" required readonly> {{-- readonly vì code không đổi --}}
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('messages.description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="3" readonly>{{ old('description', __("messages.module_descriptions.{$module->code}", ['default' => $module->description])) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Status') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm">
                                @foreach ($statuses as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('status', $module->status) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('modules.index') }}"
                                class="btn btn-secondary me-2">{{ __('messages.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
