<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Contact Request List') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">ID
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Sender') }}</th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Category') }}</th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Module') }}</th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Status') }}</th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Created At') }}</th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr>
                                        <td class="fw-medium">
                                            {{ $contact->id }}
                                        </td>
                                        <td>
                                            {{ $contact->user->name ?? __('N/A') }}
                                            ({{ $contact->user->email ?? __('N/A') }})
                                        </td>
                                        <td>
                                            {{ \App\Services\ContactService::getFormTypes()[$contact->type] ?? __('Unknown') }}
                                        </td>
                                        <td>
                                            {{ $contact->module->name ?? __('Not applicable') }}
                                        </td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    \App\Services\ContactService::STATUS_NEW => __('New'),
                                                    \App\Services\ContactService::STATUS_IN_PROGRESS => __(
                                                        'In Progress',
                                                    ),
                                                    \App\Services\ContactService::STATUS_COMPLETED => __('Completed'),
                                                    \App\Services\ContactService::STATUS_CANCELED => __('Canceled'),
                                                ];
                                            @endphp
                                            <span
                                                class="badge
                                                @if ($contact->status == \App\Services\ContactService::STATUS_NEW) bg-primary
                                                @elseif($contact->status == \App\Services\ContactService::STATUS_IN_PROGRESS) bg-warning text-dark
                                                @elseif($contact->status == \App\Services\ContactService::STATUS_COMPLETED) bg-success
                                                @elseif($contact->status == \App\Services\ContactService::STATUS_CANCELED) bg-danger
                                                @else bg-secondary @endif">
                                                {{ $statusMap[$contact->status] ?? __('Unknown') }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $contact->created_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('contacts.edit', $contact->id) }}"
                                                class="btn btn-primary btn-sm me-2" data-bs-toggle="tooltip"
                                                data-bs-title="@lang('Edit')">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
