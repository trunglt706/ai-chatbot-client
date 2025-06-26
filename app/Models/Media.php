<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'disk',
        'mediable_id',
        'mediable_type',
    ];

    // Định nghĩa mối quan hệ đa hình ngược
    public function mediable()
    {
        return $this->morphTo();
    }

    // Helper để lấy URL đầy đủ của file
    public function getFullUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }
}
