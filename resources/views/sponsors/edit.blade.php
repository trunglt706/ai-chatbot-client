<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Sponsors', 'url' => route('sponsors.index')], ['name' => 'Edit Sponsor']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('sponsors.update', $sponsor) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="code" :value="__('Mã nhà tài trợ')" />
                            <p id="code" class="mt-1 small text-dark">{{ $sponsor->code }}</p>
                        </div>

                        <div class="mb-4">
                            <x-input-label required for="name" :value="__('Tên nhà tài trợ')" />
                            <x-text-input id="name" class="form-control mt-1" type="text" name="name"
                                :value="old('name', $sponsor->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Hình ảnh (Logo)')" />
                            @if ($sponsor->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($sponsor->image) }}" alt="{{ $sponsor->name }}"
                                        class="rounded-circle object-fit-contain" style="height: 80px; width: 80px;">
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="remove_image" id="remove_image"
                                            class="form-check-input">
                                        <label for="remove_image" class="form-check-label small">
                                            {{ __('Xóa hình ảnh hiện tại') }}
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <input id="image" type="file" name="image" class="form-control mt-1" />
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label required for="status" :value="__('Trạng thái')" />
                            <select id="status" name="status" class="form-select mt-1" required>
                                <option value="active"
                                    {{ old('status', $sponsor->status) == 'active' ? 'selected' : '' }}>
                                    {{ __('Active') }}</option>
                                <option value="inactive"
                                    {{ old('status', $sponsor->status) == 'inactive' ? 'selected' : '' }}>
                                    {{ __('Inactive') }}</option>
                                <option value="pending"
                                    {{ old('status', $sponsor->status) == 'pending' ? 'selected' : '' }}>
                                    {{ __('Pending') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Mô tả')" />
                            <x-textarea id="description" name="description"
                                class="form-control mt-1">{{ old('description', $sponsor->description) }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mb-5 border-top pt-4">
                            <h3 class="h6 fw-semibold text-dark mb-3">
                                {{ __('Tài trợ Modules') }}
                            </h3>
                            <div class="row g-3">
                                @forelse ($modules as $module)
                                    @php
                                        $isSponsored = $sponsoredModules->has($module->id);
                                        $pivot = $isSponsored ? $sponsoredModules[$module->id]->pivot : null;
                                    @endphp
                                    <div class="col-12">
                                        <div class="card border rounded-3 p-3 mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="form-check">
                                                    <input type="checkbox" id="module_{{ $module->id }}"
                                                        name="modules[{{ $module->id }}][is_sponsored]"
                                                        value="1" class="form-check-input"
                                                        {{ $isSponsored ? 'checked' : '' }}
                                                        onchange="toggleModuleSponsorDetails(this, {{ $module->id }})">
                                                    <label for="module_{{ $module->id }}"
                                                        class="form-check-label fw-semibold">
                                                        {{ $module->name }}
                                                    </label>
                                                </div>
                                                <span class="small text-muted">{{ __('Slug:') }}
                                                    {{ $module->slug }}</span>
                                            </div>
                                            <div id="module_details_{{ $module->id }}"
                                                class="row g-2 mt-3 {{ $isSponsored ? '' : 'd-none' }}">
                                                <div class="col-md-4">
                                                    <x-input-label for="total_amount_{{ $module->id }}"
                                                        :value="__('Tổng tiền tài trợ')" />
                                                    <x-text-input id="total_amount_{{ $module->id }}" type="number"
                                                        step="0.01"
                                                        name="modules[{{ $module->id }}][total_amount]"
                                                        class="form-control mt-1" :value="old(
                                                            'modules.' . $module->id . '.total_amount',
                                                            $pivot->total_amount ?? 0,
                                                        )" />
                                                    <x-input-error :messages="$errors->get('modules.' . $module->id . '.total_amount')" class="mt-2" />
                                                </div>
                                                <div class="col-md-4">
                                                    <x-input-label for="start_date_{{ $module->id }}"
                                                        :value="__('Ngày bắt đầu tài trợ')" />
                                                    <x-text-input id="start_date_{{ $module->id }}" type="date"
                                                        name="modules[{{ $module->id }}][start_date]"
                                                        class="form-control mt-1" :value="old(
                                                            'modules.' . $module->id . '.start_date',
                                                            $pivot->start_date ?? '',
                                                        )" />
                                                    <x-input-error :messages="$errors->get('modules.' . $module->id . '.start_date')" class="mt-2" />
                                                </div>
                                                <div class="col-md-4">
                                                    <x-input-label for="end_date_{{ $module->id }}"
                                                        :value="__('Ngày kết thúc tài trợ')" />
                                                    <x-text-input id="end_date_{{ $module->id }}" type="date"
                                                        name="modules[{{ $module->id }}][end_date]"
                                                        class="form-control mt-1" :value="old(
                                                            'modules.' . $module->id . '.end_date',
                                                            $pivot->end_date ?? '',
                                                        )" />
                                                    <x-input-error :messages="$errors->get('modules.' . $module->id . '.end_date')" class="mt-2" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">
                                        {{ __('Không có module nào để tài trợ.') }}</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <x-primary-button>
                                {{ __('Cập nhật') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleModuleSponsorDetails(checkbox, moduleId) {
                const detailsDiv = document.getElementById('module_details_' + moduleId);
                if (checkbox.checked) {
                    detailsDiv.classList.remove('d-none');
                    detailsDiv.querySelectorAll('input').forEach(input => {
                        if (input.type === 'number') input.value = input.value || 0;
                        if (input.type === 'date') input.value = input.value || '';
                    });
                } else {
                    detailsDiv.classList.add('d-none');
                    detailsDiv.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('input[type="checkbox"][name^="modules["][name$="[is_sponsored]"]').forEach(
                    checkbox => {
                        const moduleId = checkbox.id.split('_')[1];
                        toggleModuleSponsorDetails(checkbox, moduleId);
                    });
            });
        </script>
    @endpush
</x-app-layout>
