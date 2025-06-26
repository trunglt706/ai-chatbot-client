<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sponsor extends Model
{
    use HasFactory, LogsActivity;

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
}
