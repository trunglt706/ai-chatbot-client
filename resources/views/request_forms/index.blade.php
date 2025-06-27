<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Danh sách Yêu Cầu Liên Hệ') }}
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
                                    <th scope="col" class="text-start text-uppercase small fw-bold">ID</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Người gửi</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Phân loại</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Module</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Trạng thái</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Ngày tạo</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contacts as $contact)
                                    <tr>
                                        <td class="fw-medium">
                                            {{ $contact->id }}
                                        </td>
                                        <td>
                                            {{ $contact->user->name ?? 'N/A' }} ({{ $contact->user->email ?? 'N/A' }})
                                        </td>
                                        <td>
                                            {{ \App\Services\ContactService::getFormTypes()[$contact->type] ?? 'Không xác định' }}
                                        </td>
                                        <td>
                                            {{ $contact->module->name ?? 'Không áp dụng' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    \App\Services\ContactService::STATUS_NEW => 'Mới',
                                                    \App\Services\ContactService::STATUS_IN_PROGRESS => 'Đang xử lý',
                                                    \App\Services\ContactService::STATUS_COMPLETED => 'Đã hoàn thành',
                                                    \App\Services\ContactService::STATUS_CANCELED => 'Đã hủy',
                                                ];
                                            @endphp
                                            <span
                                                class="badge
                                                @if ($contact->status == \App\Services\ContactService::STATUS_NEW) bg-primary
                                                @elseif($contact->status == \App\Services\ContactService::STATUS_IN_PROGRESS) bg-warning text-dark
                                                @elseif($contact->status == \App\Services\ContactService::STATUS_COMPLETED) bg-success
                                                @elseif($contact->status == \App\Services\ContactService::STATUS_CANCELED) bg-danger
                                                @else bg-secondary @endif">
                                                {{ $statusMap[$contact->status] ?? 'Không xác định' }}
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
