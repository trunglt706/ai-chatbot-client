<?php

// app/Events/ModulePublishedEvent.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Module;
use App\Models\User;

class ModulePublishedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $module;
    public $message;
    public $link;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct(Module $module, User $user, string $message, string $link)
    {
        $this->module = $module;
        $this->message = $message;
        $this->link = $link;
        $this->userId = $user->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Kênh riêng tư cho từng người dùng
        return [
            new PrivateChannel('users.' . $this->userId),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'ModulePublished'; // Tên event mà frontend sẽ lắng nghe
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->module->id,
            'name' => $this->module->name,
            'message' => $this->message,
            'link' => $this->link,
            'status' => $this->module->status,
            'created_at' => now()->diffForHumans(), // Thời gian thông báo
        ];
    }
}
