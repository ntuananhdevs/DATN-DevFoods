<?php

namespace App\Http\Controllers\Customer;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use App\Events\NewMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            Log::info('Customer sending message', [
                'conversation_id' => $request->conversation_id,
                'message' => $request->message,
                'user_id' => auth()->id()
            ]);

            $conversation = Conversation::where('id', $request->conversation_id)
                ->where('customer_id', auth()->id())
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc trò chuyện'
                ], 404);
            }

            Log::info('Customer conversation found', ['conversation' => $conversation]);

            // Nếu chưa phân phối thì receiver_id là admin (id=11), nếu đã phân phối thì là branch_id
            $receiverId = $conversation->branch_id ?? 11;

            $messageData = [
                'conversation_id' => $request->conversation_id,
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'sender_type' => 'customer',
                'message' => $request->message,
                'attachment' => null,
                'attachment_type' => null,
                'sent_at' => now(),
                'status' => 'sent'
            ];

            Log::info('Customer message data before create', $messageData);

            $message = ChatMessage::create($messageData);

            // Load sender info với trường full_name
            $message->load(['sender' => function ($query) {
                $query->select('id', 'full_name');
            }]);

            broadcast(new NewMessage($message, $request->conversation_id))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi thành công',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            Log::error('Customer send message error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi tin nhắn! Vui lòng thử lại sau.'
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        try {
            $userId = Auth::id();
            $conversationId = $request->conversation_id;

            Log::info('Getting messages', [
                'user_id' => $userId,
                'conversation_id' => $conversationId
            ]);

            // Kiểm tra cuộc hội thoại có thuộc về user không
            $conversation = Conversation::where('id', $conversationId)
                ->where('customer_id', $userId)
                ->first();

            if (!$conversation) {
                Log::warning('Conversation not found', [
                    'user_id' => $userId,
                    'conversation_id' => $conversationId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc trò chuyện'
                ], 404);
            }

            Log::info('Found conversation', [
                'conversation' => $conversation->toArray()
            ]);

            // Lấy tin nhắn và thông tin người gửi
            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->with(['sender' => function ($query) {
                    $query->select('id', 'full_name', 'email');
                }])
                ->orderBy('sent_at', 'asc')
                ->get();

            Log::info('Found messages', [
                'count' => $messages->count(),
                'messages' => $messages->toArray()
            ]);

            // Đánh dấu tin nhắn đã đọc
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error('Get messages error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));

            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }


    public function createConversation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $userId = Auth::id();

            // Tạo conversation mới
            $conversation = Conversation::create([
                'customer_id' => $userId,
                'branch_id' => null, // Ban đầu chưa có branch
                'status' => 'new',
                'is_distributed' => false,
            ]);

            // Xử lý file đính kèm nếu có
            $attachmentPath = null;
            $attachmentType = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentPath = $file->store('chat_attachments', 'public');
                $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            }

            // Tạo tin nhắn đầu tiên
            $message = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'receiver_id' => 11, // Super admin ID
                'sender_type' => 'customer',
                'receiver_type' => 'super_admin',
                'message' => $request->message,
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'sent_at' => now(),
                'status' => 'sent'
            ]);

            Log::info('New conversation created with first message', [
                'conversation_id' => $conversation->id,
                'customer_id' => $userId,
                'message_id' => $message->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cuộc trò chuyện đã được tạo thành công',
                'data' => [
                    'conversation' => $conversation,
                    'message' => $message
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tạo cuộc trò chuyện: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConversations()
    {
        try {
            $userId = Auth::id();

            $conversations = Conversation::where('customer_id', $userId)
                ->with(['branch', 'messages' => function ($query) {
                    $query->latest()->limit(1);
                }])
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json(['conversations' => $conversations]);
        } catch (\Exception $e) {
            Log::error('Get conversations error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy danh sách cuộc trò chuyện: ' . $e->getMessage()
            ], 500);
        }
    }
}
