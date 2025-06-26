<?php

namespace App\Services;

use App\Models\Module;
use App\Models\User;
use App\Notifications\ModuleStatusUpdatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ModuleService
{
    public const STATUS_DEVELOPING = 1;
    public const STATUS_PUBLISHED = 2;
    public const STATUS_PAUSED = 3;
    public const STATUS_DISCONTINUED = 4;

    public static function getStatus(): array
    {
        return [
            self::STATUS_DEVELOPING => 'Đang phát triển',
            self::STATUS_PUBLISHED => 'Đang phát hành',
            self::STATUS_PAUSED => 'Tạm dừng',
            self::STATUS_DISCONTINUED => 'Ngừng phát hành',
        ];
    }

    /**
     * Kiểm tra xem một module có tồn tại và đang được phát hành hay không dựa trên code của nó.
     *
     * @param string $code The code of the module.
     * @return bool True if the module exists and is published, false otherwise.
     */
    public function isModulePublished(string $code): bool
    {
        // Có thể cache kết quả để cải thiện hiệu suất cho các lần kiểm tra lặp lại
        return Cache::rememberForever("module_published_{$code}", function () use ($code) {
            $module = Module::where('code', $code)
                ->where('status', self::STATUS_PUBLISHED)
                ->first();

            return (bool) $module;
        });
    }

    /**
     * Lấy một module đang phát hành bằng code.
     * Hữu ích nếu bạn muốn lấy cả đối tượng module sau khi kiểm tra.
     *
     * @param string $code The code of the module.
     * @return Module|null The published Module instance or null if not found/published.
     */
    public function getPublishedModule(string $code): ?Module
    {
        return Cache::rememberForever("get_published_module_{$code}", function () use ($code) {
            return Module::where('code', $code)
                ->where('status', self::STATUS_PUBLISHED)
                ->first();
        });
    }

    /**
     * Xóa cache của một module cụ thể (ví dụ: khi module được cập nhật).
     *
     * @param string $code
     * @return void
     */
    public function clearModuleCache(string $code): void
    {
        Cache::forget("module_published_{$code}");
        Cache::forget("get_published_module_{$code}");
    }

    /**
     * Lấy danh sách tất cả modules.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllModules($limit = 10)
    {
        return Module::latest()->paginate($limit);
    }

    /**
     * Tạo module mới.
     *
     * @param array $data
     * @return \App\Models\Module
     */
    public function createModule(array $data): Module
    {
        $module = Module::create($data);
        $this->logActivity($module, 'Module created');

        // Nếu module được tạo với trạng thái "Đang phát hành", gửi thông báo
        if ($module->status === self::STATUS_PUBLISHED) {
            $this->notifyActiveUsers($module, 'mới được phát hành');
        }

        return $module;
    }

    /**
     * Cập nhật module.
     *
     * @param \App\Models\Module $module
     * @param array $data
     * @return \App\Models\Module
     */
    public function updateModule(Module $module, array $data): Module
    {
        $oldStatus = $module->status; // Lấy trạng thái cũ trước khi cập nhật

        $module->update($data);
        $this->logActivity($module, 'Module updated');

        // Nếu trạng thái thay đổi thành "Đang phát hành"
        if ($oldStatus !== self::STATUS_PUBLISHED && $module->status === self::STATUS_PUBLISHED) {
            $this->notifyActiveUsers($module, 'hiện đã được phát hành');
        }

        return $module;
    }

    /**
     * Xóa module.
     *
     * @param \App\Models\Module $module
     * @return void
     */
    public function deleteModule(Module $module): void
    {
        $module->delete();
        $this->logActivity($module, 'Module deleted');
    }

    /**
     * Gửi thông báo cho tất cả người dùng đang kích hoạt về trạng thái module.
     *
     * @param \App\Models\Module $module
     * @param string $actionDescription Mô tả hành động (ví dụ: 'mới được phát hành', 'đã được cập nhật')
     * @return void
     */
    protected function notifyActiveUsers(Module $module, string $actionDescription): void
    {
        // Lấy tất cả người dùng đang kích hoạt (có thể thêm các điều kiện khác như 'is_active' = true)
        $activeUsers = User::whereNotNull('email_verified_at')->get();

        if ($activeUsers->isEmpty()) {
            Log::info("No active users to notify about module '{$module->name}'.");
            return;
        }

        Notification::send($activeUsers, new ModuleStatusUpdatedNotification($module, $actionDescription));
        Log::info("Notified {$activeUsers->count()} active users about module '{$module->name}' ({$actionDescription}).");
    }

    /**
     * Ghi log hoạt động.
     *
     * @param \App\Models\Module $module
     * @param string $description
     * @return void
     */
    protected function logActivity(Module $module, string $description): void
    {
        activity()
            ->performedOn($module)
            ->causedBy(Auth::user() ?: null)
            ->withProperties([
                'module_name' => $module->name,
                'status' => Module::getStatuses()[$module->status] ?? __('Unknown'),
            ])
            ->log($description);
    }
}
