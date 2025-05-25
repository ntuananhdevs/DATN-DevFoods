<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function customerChat()
    {
        $userId = Auth::id();
        $adminId = 1; // Gán ID của admin cố định hoặc lấy theo logic khác

        $messages = ChatMessage::where(function ($q) use ($userId, $adminId) {
            $q->where('sender_id', $userId)->where('receiver_id', $adminId);
        })->orWhere(function ($q) use ($userId, $adminId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $userId);
        })->orderBy('sent_at')->get();

        return view('chat.customer', compact('messages'));
    }





    public function adminChat()
    {
        $adminId = Auth::id();

        // Load tin nhắn với tất cả khách hàng (hoặc một customer cụ thể)
        $messages = ChatMessage::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->orderBy('sent_at')
            ->get();

        return view('chat.admin', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120', // Max 5MB
            'receiver_id' => 'required|integer',
            'sender_type' => 'required|string',
            'receiver_type' => 'required|string',
        ]);

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'public');
            $mime = $file->getMimeType();
            $attachmentType = str_starts_with($mime, 'image') ? 'image' : 'file';
        }

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message ?? '',
            'sent_at' => now(),
            'sender_type' => $request->sender_type,
            'receiver_type' => $request->receiver_type,
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        // Send via Pusher
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );

        $pusher->trigger('chat-channel', 'new-message', [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'message' => $message->message,
            'sent_at' => $message->sent_at->toDateTimeString(),
            'sender_type' => $message->sender_type,
            'receiver_type' => $message->receiver_type,
            'attachment' => $message->attachment,
            'attachment_type' => $message->attachment_type,
        ]);

        return response()->json(['status' => 'Message sent successfully']);
    }
}
