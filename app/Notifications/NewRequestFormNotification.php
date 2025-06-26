<?php

// app/Notifications/NewRequestFormNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Models\RequestForm;
use App\Services\ContactService;

class NewRequestFormNotification extends Notification
{
    use Queueable;

    protected $requestForm;

    /**
     * Create a new notification instance.
     */
    public function __construct(RequestForm $requestForm)
    {
        $this->requestForm = $requestForm;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $senderInfo = $this->requestForm->user
            ? "User: {$this->requestForm->user->name} ({$this->requestForm->user->email})"
            : "Guest: {$this->requestForm->name} ({$this->requestForm->email})";

        $modules = $this->requestForm->modules_selected ? implode(', ', $this->requestForm->modules_selected) : 'N/A';
        $requestType = ContactService::getFormTypes()[$this->requestForm->type] ?? __('Unknown');

        return (new SlackMessage)
            ->from('Yêu cầu mới', ':page_facing_up:')
            ->content("🔔 *Yêu cầu mới đã được gửi!*")
            ->attachment(function ($attachment) use ($senderInfo, $requestType, $modules) {
                $attachment->title("Phân loại: {$requestType}")
                    ->fields([
                        'Người gửi' => $senderInfo,
                        'ID Yêu cầu' => $this->requestForm->id,
                        'Module đã chọn' => $modules,
                        'Nội dung' => $this->requestForm->content,
                    ])
                    ->color('#3AA3E3')
                    ->timestamp($this->requestForm->created_at);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->requestForm->id,
            'type' => $this->requestForm->type,
            'content' => $this->requestForm->content,
            'user_id' => $this->requestForm->user_id,
            'module_id' => $this->requestForm->module_id,
        ];
    }
}
