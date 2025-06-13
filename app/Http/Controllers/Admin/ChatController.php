<?php

namespace App\Http\Controllers\Admin;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\Branch;
use App\Models\User;
use App\Events\Chat\NewMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\TypingStatus;

class ChatController extends Controller
{
    public function __construct()
    {
        // Bỏ middleware để test dễ dàng
        // $this->middleware('auth');
    }

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
            $attachmentPath = $file->store('chat_attachments', 'public');
            $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            if (!$messageText) {
                $messageText = $attachmentType === 'image' ? 'Đã gửi ảnh' : 'Đã gửi file';
            }
        }
        if (!$messageText) {
            $messageText = '';
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
            'status' => 'sent'
        ]);
        // Broadcast event nếu cần
        return response()->json(['success' => true, 'data' => $message]);
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
                broadcast(new NewMessage($systemMessage->load('sender'), $conversation->id))->toOthers();
            } catch (\Exception $e) {
                Log::error('Pusher broadcast error: ' . $e->getMessage());
            }

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
