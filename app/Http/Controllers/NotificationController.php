<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $company = $user->companies->first();

        if (!$company) {
            return response()->json([
                'workspace' => [],
                'mentions' => [],
                'conversations' => [],
                'invites' => [],
                'unread_count' => 0,
            ]);
        }

        // Get only unread notifications by type (in descending order)
        $notifications = Notification::where('user_id', $user->id)
            ->where('company_id', $company->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(50) // Limit to 50 most recent
            ->get();

        $workspace = $notifications->where('type', 'workspace')->values();
        $mentions = $notifications->where('type', 'mention')->values();
        $conversations = $notifications->where('type', 'conversation')->values();
        $invites = $notifications->where('type', 'invite')->values();

        $unreadCount = $notifications->count();

        return response()->json([
            'workspace' => $workspace,
            'mentions' => $mentions,
            'conversations' => $conversations,
            'invites' => $invites,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        // Ensure user owns this notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = Auth::user();
        $company = $user->companies->first();

        Notification::where('user_id', $user->id)
            ->where('company_id', $company->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        // Ensure user owns this notification
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }
}
