<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'is_published',
        'published_at',
        'description',
        'tags'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array'
    ];

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

    /**
     * Register the media collections for the model.
     * This is where you define your media collections and their conversions.
     *
     * @return void
     */
    /**
     * Define media collections and conversions.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('post_thumbnail')
            ->singleFile(); // Ensures only one file can be in this collection
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Define the "thumb" conversion for the smaller image
        // This will create a file like my-image-thumb.jpg
        $this->addMediaConversion('thumb')
            ->width(300) // Set width to 300px
            ->height(200) // Set height to 200px
            ->sharpen(10) // Optional: add some sharpening
            ->nonQueued(); // Use nonQueued if you don't have queues configured, otherwise remove
        // ->fit(300, 200); // Alternative: crop and resize to fit exactly 300x200
    }

    /**
     * Get the URL of the post's featured image.
     *
     * @return string|null
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('post_thumbnail') ?: '';
    }

    /**
     * Get the URL of the post's thumbnail featured image.
     *
     * @return string|null
     */
    public function getThumbFeaturedImageUrlAttribute(): ?string
    {
        // Lấy URL của phiên bản thumbnail của ảnh đại diện
        return $this->getFirstMediaUrl('post_thumbnail', 'thumb') ?: '';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to search posts by title or slug.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('slug', $search);
            });
        }

        return $query;
    }

    // === Cấu hình ghi log hoạt động của model này ===
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log tất cả các trường trong $fillable khi thay đổi
            ->logOnlyDirty() // Chỉ ghi log những trường đã thay đổi
            ->dontSubmitEmptyLogs(); // Không ghi log nếu không có gì thay đổi
    }
}
