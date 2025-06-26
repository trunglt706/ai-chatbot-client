<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasMedia;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getThumbnailAttribute()
    {
        return $this->media()->where('file_path', 'like', 'thumbnails/%')->first();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });

        static::updating(function ($post) {
            if ($post->isDirty('title')) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::deleting(function ($post) {
            foreach ($post->media as $mediaItem) {
                Storage::disk($mediaItem->disk)->delete($mediaItem->file_path);
                $mediaItem->delete();
            }
        });
    }
}
