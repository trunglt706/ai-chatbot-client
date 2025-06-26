<?php

namespace App\Models;

use App\Services\ModuleService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'code',
        'status',
        'version',
        'url',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($module) {
            $module->code = Str::slug($module->name);
        });

        static::updating(function ($module) {});

        // Xóa cache khi module được tạo, cập nhật hoặc xóa
        static::created(function ($module) {
            app(ModuleService::class)->clearModuleCache($module->code);
        });

        static::updated(function ($module) {
            if ($module->isDirty('code')) {
                app(ModuleService::class)->clearModuleCache($module->getOriginal('code'));
            }
            app(ModuleService::class)->clearModuleCache($module->code);
        });

        static::deleted(function ($module) {
            app(ModuleService::class)->clearModuleCache($module->code);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Định nghĩa mối quan hệ với Sponsor (nhiều-nhiều)
    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'module_sponsor')
            ->withPivot('total_amount', 'start_date', 'end_date')
            ->withTimestamps();
    }
}
