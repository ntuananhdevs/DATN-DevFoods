<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * Hiển thị danh sách các cuộc trò chuyện.
     */
    public function index()
    {
        $conversations = Conversation::with(['customer', 'messages'])->latest()->get();
        return view('admin.chat.index', compact('conversations'));
    }

    /**
     * Hiển thị chi tiết một cuộc trò chuyện.
     */
    public function show($id)
    {
        $conversation = Conversation::with(['customer', 'messages'])->findOrFail($id);
        return view('admin.chat.show', compact('conversation'));
    }

    /**
     * Cập nhật trạng thái của một cuộc trò chuyện.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:new,active,distributed,closed',
        ]);

        $conversation = Conversation::findOrFail($id);
        $conversation->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Trạng thái cuộc trò chuyện đã được cập nhật.');
    }

    /**
     * Xóa một cuộc trò chuyện.
     */
    public function destroy($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->delete();

        return redirect()->route('conversations.index')->with('success', 'Cuộc trò chuyện đã được xóa.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $conversation = Conversation::create([
            'customer_id' => Auth::id(),
            'status' => 'new'
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'sender_type' => 'customer',
            'receiver_type' => 'super_admin'
        ]);

        return response()->json([
            'message' => 'Cuộc hội thoại đã được tạo',
            'conversation' => $conversation
        ], 201);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $user = Auth::user();
        $senderType = $user->role;

        // Xác định người nhận dựa trên loại người gửi và trạng thái phân phối
        $receiverType = 'customer';
        $branchId = null;
        $receiverId = null;

        if ($senderType === 'customer') {
            // Nếu người gửi là khách hàng
            if ($conversation->is_distributed) {
                // Nếu đã phân phối, gửi đến chi nhánh
                $receiverType = 'branch_admin';
                $branchId = $conversation->branch_id;
                // Lấy ID của quản lý chi nhánh làm người nhận chính
                $receiverId = User::where('branch_id', $branchId)
                    ->where('role', 'branch_admin')
                    ->first()->id;
            } else {
                // Nếu chưa phân phối, gửi đến admin tổng
                $receiverType = 'super_admin';
                $receiverId = User::where('role', 'super_admin')->first()->id;
            }
        } else {
            // Nếu người gửi là nhân viên/ quản lý chi nhánh
            $receiverType = 'customer';
            $receiverId = $conversation->customer_id;
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'message' => $request->message,
            'sender_type' => $senderType,
            'receiver_type' => $receiverType,
            'branch_id' => $branchId,
            'status' => 'sent',
            'sent_at' => now()
        ]);

        // Lấy danh sách người nhận để thông báo
        $receivers = $message->getReceivers();

        // TODO: Gửi thông báo realtime cho người nhận
        // broadcast(new NewMessageEvent($message, $receivers));

        return response()->json([
            'message' => 'Tin nhắn đã được gửi',
            'data' => $message->load(['sender', 'receiver'])
        ]);
    }

    public function getNewConversations()
    {
        $conversations = Conversation::where('status', 'new')
            ->with(['customer', 'messages'])
            ->latest()
            ->get();

        return response()->json($conversations);
    }

    public function distribute(Request $request, Conversation $conversation)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $conversation->update([
            'branch_id' => $request->branch_id,
            'status' => 'distributed',
            'is_distributed' => true,
            'distribution_time' => now()
        ]);

        // Tự động thêm tất cả thành viên của chi nhánh vào cuộc hội thoại
        $conversation->addBranchMembers();

        return response()->json([
            'message' => 'Cuộc hội thoại đã được phân phối',
            'conversation' => $conversation->load('participants.user')
        ]);
    }

    public function getBranchConversations()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        // Lấy các cuộc hội thoại mà user hiện tại tham gia
        $conversations = Conversation::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('branch_id', $branchId)
            ->where('status', 'distributed')
            ->with(['customer', 'messages', 'participants.user'])
            ->latest()
            ->get();

        return response()->json($conversations);
    }
}
