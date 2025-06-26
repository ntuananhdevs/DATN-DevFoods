<?php

namespace App\Http\Controllers\Admin;

use App\Events\Chat\ConversationUpdated;
use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\Branch;

use App\Events\Chat\NewMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        $conversations = Conversation::with(['customer', 'messages', 'branch'])
            ->orderBy('updated_at', 'desc')
            ->get();
        $conversation = $conversations->first();
        $branches = Branch::all();
        return view('admin.chat.index', compact('conversations', 'conversation', 'branches'));
    }

    public function show($id)
    {
        $conversations = Conversation::with(['customer', 'messages', 'branch'])
            ->orderBy('updated_at', 'desc')
            ->get();
        $conversation = Conversation::with(['customer', 'messages', 'branch'])->findOrFail($id);
        $branches = Branch::all();
        return view('admin.chat.index', compact('conversations', 'conversation', 'branches'));
    }

    public function sendMessage(Request $request)
    {
        Log::info('Admin gửi tin nhắn', [
            'conversation_id' => $request->conversation_id,
            'message' => $request->message,
            'user_id' => Auth::id(),
            'has_attachment' => $request->hasFile('attachment')
        ]);
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240'
        ]);
        $conversation = Conversation::findOrFail($request->conversation_id);
        $attachmentPath = null;
        $attachmentType = null;
        $messageText = $request->message;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            Log::info('Admin gửi file', ['file' => $attachmentPath, 'type' => $attachmentType]);
            if (!$messageText) {
                $messageText = $attachmentType === 'image' ? 'Đã gửi ảnh' : 'Đã gửi file';
            }
        }
        Log::info('Admin tạo message', ['attachment' => $attachmentPath, 'attachment_type' => $attachmentType, 'message' => $messageText]);
        if (!$messageText && !$attachmentPath) {
            Log::warning('Admin gửi tin nhắn rỗng', ['conversation_id' => $request->conversation_id]);
            return response()->json([
                'success' => false,
                'message' => 'Bạn phải nhập nội dung hoặc đính kèm file!'
            ], 422);
        }
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $conversation->customer_id,
            'sender_type' => 'super_admin',
            'message' => $messageText,
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'sent_at' => now(),
            'status' => 'sent',
            'branch_id' => $conversation->branch_id, // <--- THÊM DÒNG NÀY
        ]);
        Log::info('Admin đã tạo message', ['message_id' => $message->id, 'message' => $message->toArray()]);
        $message->load(['sender' => function ($query) {
            $query->select('id', 'full_name');
        }]);
        Log::info('Sender loaded:', ['sender' => $message->sender]);
        broadcast(new NewMessage($message, $conversation->id))->toOthers();
        broadcast(new ConversationUpdated($conversation, 'created'))->toOthers();
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'message' => $message->message,
                'attachment' => $message->attachment,
                'attachment_type' => $message->attachment_type,
                'created_at' => $message->created_at,
                'sender' => [
                    'id' => $message->sender->id,
                    'full_name' => $message->sender->full_name,
                ],
            ]
        ]);
    }

    public function assignBranch(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'branch_id' => 'required|exists:branches,id'
        ]);
        $conversation = Conversation::findOrFail($request->conversation_id);
        $conversation->branch_id = $request->branch_id;
        $conversation->status = 'distributed';
        $conversation->save();
        // Broadcast event nếu cần
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,active,distributed,closed'
        ]);
        $conversation = Conversation::findOrFail($id);
        $conversation->status = $request->status;
        $conversation->save();
        return response()->json(['success' => true]);
    }

    public function distributeConversation(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'branch_id' => 'required|exists:branches,id'
            ]);

            $conversation = Conversation::findOrFail($request->conversation_id);

            if ($conversation->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuộc trò chuyện này đã được phân phối.'
                ], 400);
            }

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
                'is_system_message' => true,
                'branch_id' => $branch->id,
            ]);

            // Broadcast system message
            try {
                $systemMessage->load(['sender' => function ($query) {
                    $query->select('id', 'full_name');
                }]);
                Log::info('Sender loaded:', ['sender' => $systemMessage->sender]);
                broadcast(new NewMessage($systemMessage, $conversation->id))->toOthers();
            } catch (\Exception $e) {
                Log::error('Pusher broadcast error: ' . $e->getMessage());
            }

            broadcast(new ConversationUpdated($conversation, 'created'))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Phân phối thành công',
                'branch' => [
                    'id' => $branch->id,
                    'name' => $branch->name
                ],
                'conversation_id' => $conversation->id,
                'status' => $conversation->status
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
                'user_id' => Auth::id(),
                'user_type' => 'admin'
            ]);

            $conversation = Conversation::findOrFail($conversationId);

            Log::info('Tìm thấy conversation', [
                'conversation' => $conversation->toArray()
            ]);

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->with(['sender:id,full_name'])
                ->orderBy('created_at', 'asc')
                ->get();

            if ($messages->count()) {
                Log::info('Sender loaded:', ['sender' => $messages->first()->sender]);
            }

            Log::info('Lấy được tin nhắn', [
                'message_count' => $messages->count(),
                'first_message' => $messages->first() ? $messages->first()->toArray() : null,
                'last_message' => $messages->last() ? $messages->last()->toArray() : null
            ]);

            // Mark messages as read
            ChatMessage::where('conversation_id', $conversationId)
                ->where('sender_id', '!=', Auth::id())
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
};
