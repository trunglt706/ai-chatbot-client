<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Chatbot Settings')]]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message /> {{-- Component hiển thị thông báo thành công/lỗi --}}

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('chatbot.setting.update') }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Quan trọng: Dùng phương thức PUT cho cập nhật --}}

                        {{-- Cài đặt Tên Bot --}}
                        <div class="mb-3">
                            <x-input-label for="bot_name" :value="__('Bot Name')" />
                            <input type="text" name="bot_name" id="bot_name"
                                value="{{ old('bot_name', $botNameSetting->value ?? '') }}" class="form-control mt-1"
                                required>
                            <x-input-error :messages="$errors->get('bot_name')" class="mt-2" />
                            <small
                                class="form-text text-muted">{{ $botNameSetting->description ?? 'Tên hiển thị của chatbot.' }}</small>
                        </div>

                        {{-- Cài đặt Nội dung Huấn luyện --}}
                        <div class="mb-3">
                            <x-input-label for="subject_content" :value="__('Subject Content (Training Data)')" />
                            <textarea name="subject_content" id="subject_content" rows="8" class="form-control mt-1">{{ old('subject_content', $subjectContentSetting->value ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('subject_content')" class="mt-2" />
                            <small
                                class="form-text text-muted">{{ $subjectContentSetting->description ?? 'Nội dung dùng để huấn luyện và định hình phản hồi của bot.' }}</small>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary text-uppercase fw-semibold text-xs">
                                <i class="bi bi-save me-1"></i> {{ __('Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
