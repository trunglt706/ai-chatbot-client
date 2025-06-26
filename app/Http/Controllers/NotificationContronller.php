<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationContronller extends Controller
{
    public function markAsRead(Request $request, $notificationId)
    {
        $notification = $request->user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true, 'link' => $notification->data['link'] ?? null]);
        }
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
