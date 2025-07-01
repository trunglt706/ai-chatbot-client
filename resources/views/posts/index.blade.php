<x-app-layout>
    <x-slot name="header">
        <x-breadcrumb :items="[['name' => __('Posts')]]" />
    </x-slot>

    <div class="py-4">
        <div class="container">

            <x-alert-message />

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <form action="{{ route('posts.index') }}" method="GET" class="d-flex">
                                <x-text-input value="{{ $search }}" id="search" class="form-control"
                                    name="search" placeholder="Search" />
                            </form>
                        </div>
                        @can('create posts')
                            <x-href-button icon="bi bi-plus" url="{{ route('posts.create') }}" name="Add New" />
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Title') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Author') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-start text-uppercase small fw-bold">
                                        {{ __('Published Date') }}
                                    </th>
                                    <th scope="col" width="10%" class="text-center text-uppercase small fw-bold">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr>
                                        <td>
                                            <a href="{{ route('posts.show', $post->slug) }}"
                                                class="text-primary text-decoration-underline">
                                                {{ Str::limit($post->title, 50) }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $post->user->name ?? __('N/A') }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                                {{ $post->is_published ? 'bg-success' : 'bg-warning text-dark' }}">
                                                {{ $post->is_published ? __('Published') : __('Draft') }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : __('N/A') }}
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
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('@lang('Are you sure you want to delete this post?')')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-secondary">
                                            {{ __('No data.') }}
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
