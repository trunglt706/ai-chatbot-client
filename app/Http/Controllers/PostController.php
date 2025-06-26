<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Services\MediaService;

class PostController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'can:manage posts']);
        $this->mediaService = $mediaService;
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
        ]);

        $data = $request->except('thumbnail');
        $data['user_id'] = Auth::id();
        $data['published_at'] = $request->input('is_published') ? now() : null;

        $post = Post::create($data);

        if ($request->hasFile('thumbnail')) {
            $this->mediaService->uploadFile($request->file('thumbnail'), $post, 'thumbnails');
        }

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo thành công!');
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
        ]);

        $data = $request->except('thumbnail');
        $data['published_at'] = $request->input('is_published') && !$post->is_published ? now() : $post->published_at;

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                $this->mediaService->deleteMedia($post->thumbnail);
            }
            $this->mediaService->uploadFile($request->file('thumbnail'), $post, 'thumbnails');
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Remove the specified post from storage.
     * Model 'Post' đã có phương thức `deleting` để xử lý xóa media liên quan.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Bài viết đã được xóa thành công!');
    }
}
