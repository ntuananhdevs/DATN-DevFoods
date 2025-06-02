<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\Branch; // Import the Branch model
use App\Models\ConversationUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Pusher\Pusher;

class ChatController extends Controller
{
    public function customerChat($conversationId)
    {
        $messages = ChatMessage::where('conversation_id', $conversationId)
            ->orderBy('sent_at')
            ->get();

        return view('chat.customer', compact('messages', 'conversationId'));
    }

    public function adminChat()
    {
        $conversations = Conversation::with('messages')
            ->whereNull('branch_id') // Chỉ lấy các cuộc trò chuyện chưa được phân phối
            ->get();

        $branches = Branch::all(); // Lấy danh sách các chi nhánh

        return view('chat.admin', compact('conversations', 'branches'));
    }

    public function branchChat($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $messages = $conversation->messages()->orderBy('sent_at')->get();

        return view('chat.branch', compact('messages', 'conversationId'));
    }

    /**
     * Gửi tin nhắn trực tiếp (không thông qua conversation)
     */
    public function sendDirectMessage(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120',
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

        $this->triggerPusherEvent($message);

        return response()->json(['status' => 'Message sent successfully']);
    }

    /**
     * Gửi tin nhắn theo conversation_id
     */
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

    /**
     * Tạo cuộc trò chuyện mới
     */
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

    /**
     * Phân phối conversation về chi nhánh
     */
    public function distributeConversation(Request $request, $conversationId)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $conversation = Conversation::findOrFail($conversationId);
        $conversation->update([
            'branch_id' => $request->branch_id,
            'status' => 'distributed',
            'is_distributed' => true, // Cập nhật trạng thái phân phối
            'distribution_time' => now(), // Lưu thời gian phân phối
        ]);

        // Tự động thêm các thành viên của chi nhánh vào conversation_users
        $branchUsers = User::where('branch_id', $request->branch_id)->get();
        foreach ($branchUsers as $user) {
            ConversationUser::updateOrCreate(
                ['conversation_id' => $conversationId, 'user_id' => $user->id],
                ['user_type' => 'branch_staff']
            );
        }

        return response()->json(['conversation' => $conversation], 200);
    }

    /**
     * Branch admin or staff joins the conversation.
     */
    public function branchJoinConversation($conversationId)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($conversationId);

        // Ensure the branch admin or staff is part of the branch
        if ($conversation->branch_id !== Auth::user()->branch_id) {
            abort(403, 'You are not authorized to join this conversation.');
        }

        $messages = $conversation->messages()->orderBy('sent_at')->get();

        return view('chat.branch', compact('messages', 'conversationId'));
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


    /**
     * Đẩy sự kiện Pusher
     */
    protected function triggerPusherEvent($message)
    {
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
            'receiver_id' => $message->receiver_id ?? null,
            'message' => $message->message,
            'sent_at' => $message->sent_at->toDateTimeString(),
            'sender_type' => $message->sender_type,
            'receiver_type' => $message->receiver_type ?? null,
            'attachment' => $message->attachment,
            'attachment_type' => $message->attachment_type,
        ]);
    }
}
