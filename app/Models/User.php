<?php

namespace App\Models;

use App\Events\UserBlocked;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class User extends Authenticatable  implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'description',
        'code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    const UNACTIVE = 'unactive';
    const ACTIVE = 'active';
    const BLOCKED = 'blocked';

    public static function getFormTypes(): array
    {
        return [
            self::UNACTIVE => __('UnActive'),
            self::ACTIVE => __('Active'),
            self::BLOCKED => __('Blocked'),
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->code = $user->code ?? Str::slug($user->name);
            $user->status = $user->status ?? self::UNACTIVE;
        });

        static::updating(function ($user) {
            if ($user->isDirty('status') && $user->status == self::BLOCKED) {
                // send broadcast event
                UserBlocked::dispatch($user);
            }
        });

        static::deleting(function ($user) {
            // xóa thông tin liên hệ
            $user->contacts()->delete();
            // xóa log
            $user->activityLogs()->delete();
            // xóa thông báo
            $user->notifications()->delete();
            // xóa tài khoản social
            $user->socialAccounts()->delete();
        });
    }

    // === Cấu hình ghi log hoạt động của model này ===
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log tất cả các trường trong $fillable khi thay đổi
            ->logOnlyDirty() // Chỉ ghi log những trường đã thay đổi
            ->dontSubmitEmptyLogs(); // Không ghi log nếu không có gì thay đổi
    }

    public function contacts()
    {
        return $this->hasMany(RequestForm::class);
    }

    /**
     * Get the social accounts for the user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }
}
