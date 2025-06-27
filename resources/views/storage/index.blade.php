<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Quản lý Dung lượng Hệ thống') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <x-alert-message />

                    <div class="mb-5 p-4 bg-light rounded-3 shadow-sm">
                        <h3 class="h6 fw-bold mb-3">Thông số Tổng quan</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary">Tổng số tập tin:</p>
                                <p class="h4 fw-bold mb-0">{{ $totalFiles }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-secondary">Tổng dung lượng sử dụng:</p>
                                <p class="h4 fw-bold mb-0">{{ $totalSizeReadable }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <form action="{{ route('storage.clear-all') }}" method="POST"
                                onsubmit="return confirm('Bạn CÓ CHẮC CHẮN muốn xóa TẤT CẢ dữ liệu media? Hành động này không thể hoàn tác!');">
                                @csrf
                                <button type="submit" class="btn btn-danger text-uppercase fw-semibold text-xs">
                                    {{ __('Xóa tất cả dữ liệu Media') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <h3 class="h6 fw-bold mb-3">Danh sách Tập tin Media</h3>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">ID</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Ảnh / Tên file
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Loại</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Kích thước</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Liên kết với</th>
                                    <th scope="col" class="text-center text-uppercase small fw-bold">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allMediaFiles as $media)
                                    <tr>
                                        <td>{{ $media->id }}</td>
                                        <td class="d-flex align-items-center gap-2">
                                            @if (Str::startsWith($media->mime_type, 'image'))
                                                <img src="{{ $media->full_url }}" alt="{{ $media->file_name }}"
                                                    class="rounded object-fit-cover" style="height: 40px; width: 40px;">
                                            @else
                                                <i class="bi bi-file-earmark-text fs-3 text-secondary"></i>
                                            @endif
                                            <span>{{ Str::limit($media->file_name, 30) }}</span>
                                        </td>
                                        <td>{{ $media->mime_type }}</td>
                                        <td>{{ \App\Http\Controllers\Admin\StorageController::formatBytes($media->file_size) }}
                                        </td>
                                        <td>
                                            @if ($media->mediable)
                                                {{ class_basename($media->mediable_type) }} #{{ $media->mediable_id }}
                                                @if ($media->mediable_type === 'App\\Models\\Post')
                                                    - <a href="{{ route('posts.show', $media->mediable->slug) }}"
                                                        class="text-primary text-decoration-underline">{{ Str::limit($media->mediable->title, 20) }}</a>
                                                @endif
                                            @else
                                                <span class="text-secondary">Không liên kết</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('storage.destroy', $media->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa tập tin này?');">
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
                                        <td colspan="6" class="text-center text-secondary">
                                            Không có tập tin nào trong hệ thống.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
