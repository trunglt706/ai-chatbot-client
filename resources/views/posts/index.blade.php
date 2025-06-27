<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold h4 text-dark mb-0">
            {{ __('Quản lý Bài viết') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-4">
                        @can('create posts')
                            <x-href-button url="{{ route('posts.create') }}" name="Add New Post" />
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">ID</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Tiêu đề</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Tác giả</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Trạng thái</th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Ngày xuất bản
                                    </th>
                                    <th scope="col" class="text-start text-uppercase small fw-bold">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr>
                                        <td class="fw-medium">
                                            {{ $post->id }}
                                        </td>
                                        <td>
                                            <a href="{{ route('posts.show', $post->slug) }}"
                                                class="text-primary text-decoration-underline">
                                                {{ Str::limit($post->title, 50) }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $post->user->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                                {{ $post->is_published ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $post->is_published ? 'Đã xuất bản' : 'Bản nháp' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : 'N/A' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('posts.edit', $post->id) }}"
                                                class="btn btn-primary btn-sm me-2" data-bs-toggle="tooltip"
                                                data-bs-title="@lang('Edit')">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-secondary">
                                            Chưa có bài viết nào.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
