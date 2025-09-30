<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:system,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notification = Notification::create($validator->validated());

        return response()->json([
            'message' => 'Notification created successfully',
            'notification' => $notification,
        ], 201);
    }

    public function markAsRead(Notification $notification)
    {
        // $this->authorize('update', $notification);

        $notification->update(['is_read' => true]);

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->notifications()->update(['is_read' => true]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function unreadCount(Request $request)
    {
        $count = $request->user()->notifications()->where('is_read', false)->count();

        return response()->json(['unread_count' => $count]);
    }
}
