<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Chatbot')]]" />
    </x-slot>
    <div class="py-2">
        <div class="container">
            <div class="text-center py-2">
                <h3 class="text-uppercase text-primary">
                    <b>@lang('Welcome to') {{ $botNameSetting?->value }}</b>
                </h3>
            </div>
            <div id="chat-support"></div>
        </div>
    </div>
</x-app-layout>
