<?php

namespace App\Http\Controllers\Branch;

use App\Events\Chat\UserTyping;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\Branch;

use App\Models\PromotionProgram;
use App\Models\DiscountCode;

use App\Events\Chat\NewMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewChatMessageNotification;

class BranchChatController extends Controller
{
    public function __construct()
    {
        // Bỏ middleware để test dễ dàng
        // $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::guard('manager')->user();
        $branch = $user ? $user->branch : null;

        if (!$branch) {
            return redirect()->back()->with('error', 'Không tìm thấy chi nhánh');
        }

        // Lọc tất cả dữ liệu chat theo branch_id của manager
        $conversations = Conversation::with(['customer', 'messages.sender'])
            ->whereNotNull('branch_id')
            ->where('branch_id', $branch->id)
            ->whereIn('status', ['distributed', 'active', 'resolved', 'closed',])
            ->orderBy('updated_at', 'desc')
            ->get();

        $conversation = $conversations->first();

        $promotions = PromotionProgram::whereHas('branches', fn($q) => $q->where('branch_id', $branch->id))->get();
        $discountCodes = DiscountCode::whereHas('branches', fn($q) => $q->where('branch_id', $branch->id))->get();

        return view('branch.chat.index', compact('conversations', 'conversation', 'branch', 'promotions', 'discountCodes'));
    }

    public function apiGetConversation($id)
    {
        try {
            $user = Auth::guard('manager')->user();
            $branch = $user ? $user->branch : null;
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải quản lý của chi nhánh nào!'
                ], 403);
            }
            $branchId = $branch->id;

            $conversation = Conversation::with([
                'customer',
                'messages.sender'
            ])->where('branch_id', $branchId)
                ->findOrFail($id);

            $messages = $conversation->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message,
                    'attachment' => $message->attachment,
                    'attachment_type' => $message->attachment_type,
                    'created_at' => $message->created_at,
                    'type' => $message->is_system_message ? 'system' : 'normal',
                    'sender' => $message->sender ? [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                        'full_name' => $message->sender->full_name ?? $message->sender->name,
                        'email' => $message->sender->email,
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'messages' => $messages,
                'conversation' => [
                    'id' => $conversation->id,
                    'customer' => [
                        'id' => $conversation->customer?->id,
                        'name' => $conversation->customer?->name,
                        'full_name' => $conversation->customer?->full_name,
                        'email' => $conversation->customer?->email,
                    ],
                    'branch' => [
                        'id' => $conversation->branch?->id,
                        'name' => $conversation->branch?->name,
                    ],
                    'status' => $conversation->status,
                    'status_label' => method_exists($conversation, 'getStatusOptions') ? ($conversation::getStatusOptions()[$conversation->status] ?? $conversation->status) : $conversation->status,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('BranchChatController: Conversation not found', [
                'conversation_id' => $id,
                'branch_id' => isset($branchId) ? $branchId : null,
                'user_id' => Auth::guard('manager')->id(),
                'conversations_of_branch' => isset($branchId) ? Conversation::where('branch_id', $branchId)->pluck('id')->toArray() : [],
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cuộc trò chuyện này thuộc chi nhánh của bạn!'
            ], 404);
        } catch (\Exception $e) {
            Log::error('BranchChatController: Error loading conversation', [
                'conversation_id' => $id,
                'branch_id' => isset($branchId) ? $branchId : null,
                'user_id' => Auth::guard('manager')->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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

            $user = Auth::guard('manager')->user();
            $branch = $user ? $user->branch : null;
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải quản lý của chi nhánh nào!'
                ], 403);
            }
            $branchId = $branch->id;

            Log::info('Branch send message attempt', [
                'conversation_id' => $request->conversation_id,
                'branch_id' => $branchId,
                'user_id' => $user->id,
                'user_branch_id' => $user->branch_id,
                'manager_user_id' => $branch->manager_user_id
            ]);

            $conversation = Conversation::where('id', $request->conversation_id)
                ->where('branch_id', $branchId)
                ->first();

            if (!$conversation) {
                Log::error('Conversation not found or not belongs to branch', [
                    'conversation_id' => $request->conversation_id,
                    'branch_id' => $branchId,
                    'user_id' => $user->id,
                    'conversations_of_branch' => Conversation::where('branch_id', $branchId)->pluck('id')->toArray(),
                    'all_conversations' => Conversation::where('id', $request->conversation_id)->first()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc trò chuyện này thuộc chi nhánh của bạn!'
                ], 404);
            }

            $customerId = $conversation->customer_id;

            $attachmentPath = null;
            $attachmentType = null;
            $messageText = $request->message;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentPath = $file->store('chat-attachments', 'public');
                $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
                Log::info('Branch gửi file', ['file' => $attachmentPath, 'type' => $attachmentType]);
                if (!$messageText) {
                    $messageText = $attachmentType === 'image' ? 'Đã gửi ảnh' : 'Đã gửi file';
                }
            }
            Log::info('Branch tạo message', ['attachment' => $attachmentPath, 'attachment_type' => $attachmentType, 'message' => $messageText]);

            if (!$messageText && !$attachmentPath) {
                Log::warning('Branch gửi tin nhắn rỗng', ['conversation_id' => $request->conversation_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn phải nhập nội dung hoặc đính kèm file!'
                ], 422);
            }

            $senderId = Auth::guard('manager')->id();

            $message = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'receiver_id' => $conversation->customer_id,
                'sender_type' => 'branch_admin',
                'message' => $messageText,
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'sent_at' => now(),
                'status' => 'sent',
                'branch_id' => $conversation->branch_id,
            ]);
            // Load lại message từ DB để đảm bảo đủ trường attachment, attachment_type
            $message = ChatMessage::with(['sender' => function ($query) {
                $query->select('id', 'full_name');
            }])->find($message->id);
            broadcast(new NewMessage($message, $request->conversation_id))->toOthers();

            if ($conversation->status === 'distributed') {
                $conversation->update([
                    'status' => 'active',
                    'updated_at' => now()
                ]);
            } else {
                $conversation->update(['updated_at' => now()]);
            }

            // Gửi cho admin (tất cả hoặc phụ trách)
            $admins = \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->get();

            foreach ($admins as $admin) {

                $admin->notify(new NewChatMessageNotification($message));
                $existing = $admin->notifications()
                    ->whereNull('read_at')
                    ->where('type', 'App\\Notifications\\NewChatMessageNotification')
                    ->where('data->conversation_id', $conversation->id)
                    ->first();
                if ($existing) {
                    $existing->data = array_merge($existing->data, (new NewChatMessageNotification($message))->toDatabase($admin));
                    $existing->created_at = now();
                    $existing->save();
                }
                // Không tạo notification mới nếu đã đọc
            }

            // Gửi cho customer (nếu có)
            if ($conversation->customer_id) {
                $customer = \App\Models\User::find($conversation->customer_id);
                if ($customer) {
                    $existing = $customer->notifications()
                        ->whereNull('read_at')
                        ->where('type', 'App\\Notifications\\NewChatMessageNotification')
                        ->where('data->conversation_id', $conversation->id)
                        ->first();
                    if ($existing) {
                        $existing->data = array_merge($existing->data, (new NewChatMessageNotification($message))->toDatabase($customer));
                        $existing->created_at = now();
                        $existing->save();
                    } else {
                        $customer->notify(new NewChatMessageNotification($message));
                    }

                    // Broadcast lên channel tổng cho customer
                    broadcast(new \App\Events\Customer\NewNotification($customer->id, $message))->toOthers();
                }
            }

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
                        'id' => $user->id,
                        'name' => 'Branch Staff'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Branch send message error: ' . $e->getMessage(), [
                'conversation_id' => $request->conversation_id ?? null,
                'branch_id' => $branchId ?? null,
                'user_id' => $user->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
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

            $user = Auth::guard('manager')->user();
            $branch = $user ? $user->branch : null;
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không phải quản lý của chi nhánh nào!'
                ], 403);
            }
            $branchId = $branch->id;
            $userId = $user->id;

            // Log để debug trước khi tìm conversation
            Log::info('Branch updateStatus', [
                'conversation_id' => $request->conversation_id,
                'branch_id' => $branchId,
                'user_id' => $userId,
                'all_conversations_of_branch' => Conversation::where('branch_id', $branchId)->pluck('id')->toArray(),
            ]);

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
                'active' => ' Cuộc trò chuyện đã được kích hoạt',
                'resolved' => ' Cuộc trò chuyện đã được giải quyết',
                'closed' => ' Cuộc trò chuyện đã được đóng'
            ];

            $systemMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'receiver_id' => $conversation->customer_id,
                'sender_type' => 'branch_admin',
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
    public function typingIndicator(Request $request)
    {
        $user = Auth::guard('manager')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập!'], 401);
        }
        $conversationId = $request->input('conversation_id');
        $isTyping = $request->input('is_typing');
        broadcast(new UserTyping($conversationId, $user->id, $user->full_name ?? $user->name, $isTyping))->toOthers();
        return response()->json(['success' => true]);
    }

    public function getUnreadChatCount()
    {
        try {
            $user = Auth::guard('manager')->user();
            $branch = $user ? $user->branch : null;

            if (!$branch instanceof \App\Models\Branch) {
                return response()->json(['count' => 0]);
            }

            // Count unread chat messages (NewChatMessageNotification) for this branch
            $unreadChatCount = $branch->notifications()
                ->whereNull('read_at')
                ->where('type', 'App\\Notifications\\NewChatMessageNotification')
                ->count();

            return response()->json(['count' => $unreadChatCount]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0, 'error' => $e->getMessage()]);
        }
    }
}
