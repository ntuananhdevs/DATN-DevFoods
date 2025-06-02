<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120',
            'sender_type' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($conversationId);

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'public');
            $mime = $file->getMimeType();
            $attachmentType = str_starts_with($mime, 'image') ? 'image' : 'file';
        }

        $message = $conversation->messages()->create([
            'sender_id' => Auth::id(),
            'sender_type' => $request->sender_type,
            'message' => $request->message,
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'sent_at' => now(),
        ]);

        broadcast(new NewMessageEvent($message))->toOthers();

        return response()->json(['message' => $message], 201);
    }

    public function getMessages($conversationId)
    {
        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->orderBy('sent_at')
            ->get();

        return response()->json(['messages' => $messages], 200);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
        ]);

        $conversation = Conversation::create([
            'customer_id' => $request->customer_id,
            'status' => 'new',
        ]);

        return response()->json(['conversation' => $conversation], 201);
    }

    public function getConversations(Request $request)
    {
        $query = Conversation::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $conversations = $query->with(['messages', 'customer'])->latest()->get();

        return response()->json(['conversations' => $conversations], 200);
    }

    public function distributeConversation(Request $request, $conversationId)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        $conversation->update([
            'branch_id' => $request->branch_id,
            'status' => 'distributed',
        ]);

        return response()->json(['conversation' => $conversation], 200);
    }
}
