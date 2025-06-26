<?php

// app/Notifications/ModuleStatusUpdatedNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Models\Module;
use App\Models\User;
use App\Services\ModuleService;

class ModuleStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $module;
    protected $actionDescription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Module $module, string $actionDescription)
    {
        $this->module = $module;
        $this->actionDescription = $actionDescription;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['slack'];
        if ($notifiable instanceof User) {
            array_push($channels, 'database');
        }
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $message = "Module *{$this->module->name}* {$this->actionDescription}!";
        if ($this->module->status === ModuleService::STATUS_PUBLISHED) {
            $message = "🎉 Module *{$this->module->name}* hiện đã được phát hành!";
        }

        return new DatabaseMessage([
            'module_id' => $this->module->id,
            'module_name' => $this->module->name,
            'status' => $this->module->status,
            'description' => $message,
            'link' => route('modules.show', $this->module->id),
            'icon' => 'bell',
        ]);
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $statusText = Module::getStatuses()[$this->module->status] ?? __('Unknown');

        return (new SlackMessage)
            ->from('Thông báo Module', ':rocket:')
            ->content("🚀 Cập nhật Module: *{$this->module->name}* {$this->actionDescription}!")
            ->attachment(function ($attachment) use ($statusText) {
                $attachment->title("Tên Module: {$this->module->name}")
                    ->fields([
                        'Trạng thái mới' => $statusText,
                        'Mô tả' => $this->module->description,
                        'Xem chi tiết' => route('modules.show', $this->module->id),
                    ])
                    ->color('#4299E1')
                    ->timestamp($this->module->updated_at);
            });
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'module_id' => $this->module->id,
            'module_name' => $this->module->name,
            'status' => $this->module->status,
            'action' => $this->actionDescription,
        ];
    }
}
