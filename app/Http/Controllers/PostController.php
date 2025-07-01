<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'permission:view posts'])->only('index');
        $this->middleware(['auth', 'permission:create posts'])->only('create', 'store');
        $this->middleware(['auth', 'permission:edit posts'])->only('edit', 'update');
        $this->middleware(['auth', 'permission:delete posts'])->only('destroy');
    }

    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $search = request('search', '');
        $pageSize = request('pageSize', 10);

        $posts = Post::query();
        $posts = $search ? $posts->search($search) : $posts;
        $posts = $posts->paginate($pageSize);
        return view('posts.index', compact('posts', 'search'));
    }

    /**
     * Show the form for creating a new posts.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_published' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        $data = $request->except('thumbnail');
        $data['user_id'] = Auth::id();
        $data['published_at'] = $request->input('is_published') ? now() : null;

        // Xử lý dữ liệu tags từ Tagify
        $parsedTags = [];
        if (!empty($data['tags'])) {
            $tagsArray = json_decode($data['tags'], true);
            // Tagify gửi về một mảng các đối tượng dạng [{value: "tag1"}, {value: "tag2"}]
            // Chúng ta chỉ cần lấy ra các giá trị
            $parsedTags = array_column($tagsArray, 'value');
        }
        // Gán mảng tags đã xử lý vào validatedData
        $data['tags'] = $parsedTags;

        $post = Post::create($data);

        if ($request->hasFile('thumbnail')) {
            $post->addMediaFromRequest('thumbnail')->toMediaCollection('post_thumbnail');
        }

        return redirect()->route('posts.index')->with('success', __('The post has been added successfully!'));
    }

    /**
     * Show the form for editing the specified posts.
     */
    public function edit(Post $post)
    {
        $currentTags = $post->tags ? array_map(function ($tag) {
            return ['value' => $tag];
        }, $post->tags) : [];

        // Chuyển đổi thành JSON string mà Tagify mong đợi
        $currentTagsJson = json_encode($currentTags);
        $currentThumbnail = $post->getFirstMediaUrl('post_thumbnail');

        return view('posts.edit', compact('post', 'currentTagsJson', 'currentThumbnail'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:posts,title,' . $post->id,
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_published' => 'boolean',
            'tags' => 'nullable|string',
        ]);

        $data = $request->except('thumbnail');
        $data['published_at'] = $request->input('is_published') && !$post->is_published ? now() : $post->published_at;

        // Xử lý dữ liệu tags từ Tagify
        $parsedTags = [];
        if (!empty($data['tags'])) {
            $tagsArray = json_decode($data['tags'], true);
            // Tagify gửi về một mảng các đối tượng dạng [{value: "tag1"}, {value: "tag2"}]
            // Chúng ta chỉ cần lấy ra các giá trị
            $parsedTags = array_column($tagsArray, 'value');
        }
        // Gán mảng tags đã xử lý vào validatedData
        $data['tags'] = $parsedTags;

        $post->update($data);

        if ($request->hasFile('thumbnail')) {
            $post->clearMediaCollection('post_thumbnail'); // Xóa ảnh cũ
            $post->addMediaFromRequest('thumbnail')->toMediaCollection('post_thumbnail'); // Thêm ảnh mới
        } elseif ($request->boolean('clear_thumbnail')) {
            $post->clearMediaCollection('post_thumbnail'); // Xóa ảnh nếu checkbox được chọn
        }

        return redirect()->route('posts.index')->with('success', __('The post has been updated successfully!'));
    }

    /**
     * Remove the specified post from storage.
     * Model 'Post' đã có phương thức `deleting` để xử lý xóa media liên quan.
     */
    public function destroy(Post $post)
    {
        $post->clearMediaCollection('post_thumbnail');
        $post->delete();
        return redirect()->route('posts.index')->with('success', __('The post has been deleted successfully!'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        if (! $post->is_published || !Auth::check()) {
            abort(404);
        }
        // Lấy URL của thumbnail gốc
        $thumbnailUrl = $post->getFirstMediaUrl('post_thumbnail');

        return view('posts.show', compact('post', 'thumbnailUrl'));
    }
}
