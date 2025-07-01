<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Sponsor extends Model implements HasMedia
{
    use HasFactory, LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
    ];

    // Tự động tạo mã nhà tài trợ khi tạo mới
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sponsor) {
            if (empty($sponsor->code)) {
                $sponsor->code = 'SP-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Register the media collections for the model.
     * This is where you define your media collections and their conversions.
     *
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('sponsor_logo')
            ->singleFile()
            ->useDisk('public');
    }

    /**
     * Register the media conversions for the model.
     * This allows you to define different sizes/formats for your images.
     *
     * @param Media|null $media
     * @return void
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->height(100)
            ->sharpen(10)
            ->queued();

        $this->addMediaConversion('web_optimized')
            ->width(800)
            ->height(600)
            ->quality(80)
            ->format('webp')
            ->queued();
    }

    /**
     * Get the URL of the sponsor's logo.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('sponsor_logo') ?: '';
    }

    /**
     * Get the URL of the sponsor's logo thumbnail.
     *
     * @return string|null
     */
    public function getThumbLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('sponsor_logo', 'thumb') ?: '';
    }

    // Định nghĩa mối quan hệ với Module (nhiều-nhiều)
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_sponsor')
            ->withPivot('total_amount', 'start_date', 'end_date')
            ->withTimestamps();
    }

    // Nếu dùng Activitylog
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Scope a query to search sponsors by name or code.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }
}
