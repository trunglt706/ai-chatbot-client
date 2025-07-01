<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;

class ChatbotSetting extends Model
{
    protected $table = 'chatbot_settings';

    protected $fillable = [
        'key',
        'value',
    ];
}
