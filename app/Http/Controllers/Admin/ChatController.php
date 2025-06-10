<?php

namespace App\Http\Controllers\Admin;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\Branch;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ChatConversation;
use App\Events\TypingStatus;

class ChatController extends Controller
{
    public function __construct()
    {
        // Bỏ middleware để test dễ dàng
        // $this->middleware('auth');
    }

    public function index()
    {
        $conversations = Conversation::with(['customer', 'branch', 'messages.sender'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $branches = Branch::all();

        // Get the first conversation as selected conversation
        $selectedConversation = $conversations->first();

        return view('admin.chat', compact('conversations', 'branches', 'selectedConversation'));
    }


    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'message' => 'nullable|string',
                'attachment' => 'nullable|file|max:10240' // 10MB max
            ]);

            // Debug log
            Log::info('Admin sending message', [
                'conversation_id' => $request->conversation_id,
                'message' => $request->message,
                'user_id' => Auth::id()
            ]);

            $attachmentPath = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentPath = $file->store('chat-attachments', 'public');
                $attachmentType = $file->getMimeType();

                if (str_starts_with($attachmentType, 'image/')) {
                    $attachmentType = 'image';
                } else {
                    $attachmentType = 'file';
                }
            }

            // Sử dụng user ID mặc định cho test
            $userId = Auth::id() ?? 1;

            // Lấy thông tin conversation để xác định receiver
            $conversation = Conversation::findOrFail($request->conversation_id);

            // Debug log conversation
            Log::info('Conversation found', [
                'conversation' => $conversation->toArray()
            ]);

            // Tạo data cho message
            $messageData = [
                'conversation_id' => $request->conversation_id,
                'sender_id' => $userId,
                'receiver_id' => $conversation->customer_id,
                'sender_type' => 'super_admin',
                'message' => $request->message,
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'sent_at' => now(),
                'status' => 'sent'
            ];

            // Debug log message data
            Log::info('Message data before create', $messageData);

            $message = ChatMessage::create($messageData);

            // Update conversation timestamp
            Conversation::where('id', $request->conversation_id)
                ->update(['updated_at' => now()]);

            // Broadcast message với Pusher
            try {
                broadcast(new NewMessage($message->load('sender'), $request->conversation_id))->toOthers();
            } catch (\Exception $e) {
                Log::error('Pusher broadcast error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi thành công',
                'data' => $message->load('sender')
            ]);
        } catch (\Exception $e) {
            Log::error('Admin send message error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function distributeConversation(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'branch_id' => 'required|exists:branches,id'
            ]);

            $conversation = Conversation::findOrFail($request->conversation_id);

            $conversation->update([
                'branch_id' => $request->branch_id,
                'status' => 'distributed',
            ]);

            // Create system message
            $branch = Branch::find($request->branch_id);
            $systemMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id() ?? 1,
                'receiver_id' => $conversation->customer_id,
                'sender_type' => 'super_admin',
                'message' => 'Cuộc trò chuyện đã được phân phối đến chi nhánh: ' . $branch->name,
                'sent_at' => now(),
                'status' => 'sent',
                'is_system_message' => true
            ]);

            // Broadcast system message
            try {
                broadcast(new NewMessage($systemMessage->load('sender'), $conversation->id))->toOthers();
            } catch (\Exception $e) {
                Log::error('Pusher broadcast error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Phân phối thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Distribute conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi phân phối: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMessages($conversationId)
    {
        try {
            Log::info('Bắt đầu lấy tin nhắn', [
                'conversation_id' => $conversationId,
                'user_id' => auth()->id(),
                'user_type' => 'admin'
            ]);

            $conversation = Conversation::findOrFail($conversationId);

            Log::info('Tìm thấy conversation', [
                'conversation' => $conversation->toArray()
            ]);

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();

            Log::info('Lấy được tin nhắn', [
                'message_count' => $messages->count(),
                'first_message' => $messages->first() ? $messages->first()->toArray() : null,
                'last_message' => $messages->last() ? $messages->last()->toArray() : null
            ]);

            // Mark messages as read
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', auth()->id())
                ->where('sender_type', '!=', 'App\Models\Admin')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            Log::info('Đã cập nhật trạng thái đọc');

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy tin nhắn', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'conversation_id' => $conversationId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleTyping(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'is_typing' => 'required|boolean'
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);
        $userId = Auth::id();

        // Kiểm tra quyền truy cập
        if (
            $conversation->admin_id !== $userId &&
            $conversation->customer_id !== $userId &&
            $conversation->branch_id !== $userId
        ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Xác định user type
        $userType = 'admin';
        if ($conversation->customer_id === $userId) {
            $userType = 'customer';
        } elseif ($conversation->branch_id === $userId) {
            $userType = 'branch';
        }

        // Broadcast typing status
        broadcast(new TypingStatus(
            $request->conversation_id,
            $userId,
            $request->is_typing,
            $userType
        ))->toOthers();

        return response()->json(['success' => true]);
    }
};
