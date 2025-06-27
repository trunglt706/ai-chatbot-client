<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => 'Sponsors']]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-4">
                        @can('create sponsors')
                            <x-href-button url="{{ route('sponsors.create') }}" name="Add New Sponsor" />
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Mã') }}</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Tên') }}</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Hình ảnh') }}</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">
                                        {{ __('Trạng thái') }}</th>
                                    <th scope="col" class="text-center text-uppercase small fw-bold">
                                        {{ __('Hành động') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sponsors as $sponsor)
                                    <tr>
                                        <td>{{ $sponsor->code }}</td>
                                        <td>{{ $sponsor->name }}</td>
                                        <td>
                                            @if ($sponsor->image)
                                                <img src="{{ Storage::url($sponsor->image) }}"
                                                    alt="{{ $sponsor->name }}" class="rounded-circle object-fit-contain"
                                                    style="height: 40px; width: 40px;">
                                            @else
                                                {{ __('Không có ảnh') }}
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                                @if ($sponsor->status === 'active') bg-success
                                                @elseif($sponsor->status === 'inactive') bg-danger
                                                @else bg-warning text-dark @endif">
                                                {{ ucfirst($sponsor->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('sponsors.edit', $sponsor) }}"
                                                class="btn btn-primary btn-sm me-2" data-bs-toggle="tooltip"
                                                data-bs-title="@lang('Edit')">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('sponsors.destroy', $sponsor) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhà tài trợ này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="tooltip" data-bs-title="@lang('Delete')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary">
                                            {{ __('Không có nhà tài trợ nào được tìm thấy.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sponsors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
