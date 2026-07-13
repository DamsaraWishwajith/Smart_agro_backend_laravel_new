<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get chat history for the authenticated user.
     */
    public function getMessages(Request $request)
    {
        $user = $request->user();

        // Mark admin messages as read as they are being viewed by the user
        Message::where('user_id', $user->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Get the count of unread admin messages.
     */
    public function getUnreadCount(Request $request)
    {
        $user = $request->user();
        $count = Message::where('user_id', $user->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count
        ]);
    }

    /**
     * Send a message from the mobile user.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240', // max 10MB
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'Message or attachment is required'], 422);
        }

        $user = $request->user();

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("chat_attachments/{$user->id}", 'public');
            $attachmentPath = $path;
            
            $extension = strtolower($file->getClientOriginalExtension());
            $attachmentType = in_array($extension, ['pdf']) ? 'pdf' : 'image';
        }

        $message = Message::create([
            'user_id' => $user->id,
            'sender_type' => 'user',
            'message' => $request->message,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $message
        ], 201);
    }

    /**
     * Download a PDF attachment securely.
     */
    public function downloadAttachment(Request $request, Message $message)
    {
        $user = $request->user();

        // Ensure user owns the message
        if ($message->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!$message->attachment_path) {
            return response()->json(['message' => 'No attachment found'], 404);
        }

        $path = storage_path("app/public/{$message->attachment_path}");

        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found on server'], 404);
        }

        return response()->download($path);
    }
}
