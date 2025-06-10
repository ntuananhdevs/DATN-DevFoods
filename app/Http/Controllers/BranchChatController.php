<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\Branch;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BranchChatController extends Controller
{
    public function __construct()
    {
        // Bỏ middleware để test dễ dàng
        // $this->middleware('auth');
    }

    public function index()
    {
        // Sử dụng branch mặc định cho test
        $branchId = 1; // Chi nhánh Hà Nội

        // Get conversations assigned to this branch
        $conversations = Conversation::with(['customer', 'messages.sender'])
            ->where('branch_id', $branchId)
            ->orderBy('updated_at', 'desc')
            ->get();

        $branch = Branch::find($branchId);

        // Tạo user mặc định cho test
        $user = (object) [
            'id' => 1,
            'name' => 'Nguyễn Văn Manager',
            'role' => 'branch_manager',
            'branch_id' => $branchId
        ];

        return view('branch.chat', compact('conversations', 'branch', 'user'));
    }

    public function apiGetConversation($id)
    {
        try {
            $conversation = Conversation::with(['customer', 'messages.sender'])
                ->findOrFail($id);

            // Format the response
            $formattedConversation = [
                'id' => $conversation->id,
                'status' => $conversation->status,
                'subject' => $conversation->subject,
                'priority' => $conversation->priority,
                'updated_at' => $conversation->updated_at,
                'customer' => $conversation->customer ? [
                    'id' => $conversation->customer->id,
                    'name' => $conversation->customer->name,
                    'email' => $conversation->customer->email,
                ] : null,
                'messages' => $conversation->messages->map(function ($message) {
                    return [
                        'id' => $message->id,
                        'sender_id' => $message->sender_id,
                        'message' => $message->message,
                        'attachment' => $message->attachment,
                        'attachment_type' => $message->attachment_type,
                        'created_at' => $message->created_at,
                        'is_system_message' => $message->is_system_message ?? false,
                        'sender' => $message->sender ? [
                            'id' => $message->sender->id,
                            'name' => $message->sender->name,
                            'email' => $message->sender->email,
                        ] : null
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'conversation' => $formattedConversation
            ]);
        } catch (\Exception $e) {
            Log::error('API Get conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi tải chi tiết cuộc trò chuyện: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'message' => 'nullable|string',
                'attachment' => 'nullable|file|max:10240' // 10MB max
            ]);

            $userId = Auth::id() ?? 1;
            $branchId = 1; // Chi nhánh mặc định

            // Debug log
            Log::info('Branch sending message', [
                'conversation_id' => $request->conversation_id,
                'message' => $request->message,
                'user_id' => $userId,
                'branch_id' => $branchId
            ]);

            // Lấy thông tin conversation trước để xác định receiver
            $conversation = Conversation::where('id', $request->conversation_id)
                ->where('branch_id', $branchId)
                ->firstOrFail();

            // Debug log conversation
            Log::info('Branch conversation found', [
                'conversation' => $conversation->toArray()
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

            // Tạo data cho message
            $messageData = [
                'conversation_id' => $request->conversation_id,
                'sender_id' => $userId,
                'receiver_id' => $conversation->customer_id, // QUAN TRỌNG: Branch gửi cho customer
                'sender_type' => 'branch_staff',
                'message' => $request->message,
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'sent_at' => now(),
                'status' => 'sent'
            ];

            // Debug log message data
            Log::info('Branch message data before create', $messageData);

            $message = ChatMessage::create($messageData);

            //Update conversation status
            if ($conversation->status === 'distributed') {
                $conversation->update([
                    'status' => 'active', // Chuyển sang active khi branch bắt đầu trả lời
                    'updated_at' => now()
                ]);
            } else {
                $conversation->update(['updated_at' => now()]);
            }

            // Load relationship để gửi đầy đủ thông tin
            $message->load(['sender' => function ($query) {
                $query->select('id', 'full_name');
            }]);

            // Broadcast tin nhắn mới
            broadcast(new NewMessage($message, $request->conversation_id))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Tin nhắn đã được gửi thành công',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'attachment' => $message->attachment,
                    'attachment_type' => $message->attachment_type,
                    'created_at' => $message->created_at,
                    'sender' => [
                        'id' => $userId,
                        'name' => 'Branch Staff'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Branch send message error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'conversation_id' => 'required|exists:conversations,id',
                'status' => 'required|in:active,resolved,closed'
            ]);

            $userId = Auth::id() ?? 1;
            $branchId = 1; // Chi nhánh mặc định

            // Verify the conversation belongs to this branch
            $conversation = Conversation::where('id', $request->conversation_id)
                ->where('branch_id', $branchId)
                ->firstOrFail();

            $oldStatus = $conversation->status;
            $newStatus = $request->status;

            $conversation->update([
                'status' => $newStatus,
                'updated_at' => now()
            ]);

            // Create system message about status change
            $statusMessages = [
                'active' => '🟢 Cuộc trò chuyện đã được kích hoạt',
                'resolved' => '✅ Cuộc trò chuyện đã được giải quyết',
                'closed' => '🔒 Cuộc trò chuyện đã được đóng'
            ];

            $systemMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'receiver_id' => $conversation->customer_id,
                'sender_type' => 'branch_staff',
                'message' => $statusMessages[$newStatus] . ' bởi nhân viên chi nhánh',
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
                'message' => 'Trạng thái đã được cập nhật thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Update status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi cập nhật trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }
}
