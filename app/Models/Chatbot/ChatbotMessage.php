<?php

namespace App\Models\Chatbot;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    protected $table = 'chatbot_messages';

    protected $fillable = [
        'user_id',
        'ip_address',
        'content',
        'type',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'content' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
