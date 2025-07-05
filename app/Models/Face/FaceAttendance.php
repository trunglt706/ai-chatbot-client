<?php

namespace App\Models\Face;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class FaceAttendance extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'face_attendances';

    protected $fillable = [
        'user_id', // Đổi từ employee_id sang user_id
        'check_in_time',
        'check_out_time',
        'status',
        'confidence',
        // image_path không còn ở đây
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'confidence' => 'float',
    ];

    // Định nghĩa mối quan hệ với User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Tùy chọn: Định nghĩa collection media cho ảnh chấm công
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attendance_images')
            ->singleFile(); // Mỗi bản ghi chấm công có 1 ảnh duy nhất
    }
}
