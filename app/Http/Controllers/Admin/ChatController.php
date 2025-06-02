<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Display the chat management interface
     */
    public function index()
    {
        return view('admin.chat.index');
    }

    /**
     * Get all chat conversations
     */
    public function getChats(Request $request): JsonResponse
    {
        // Here you would fetch chats from database
        // $chats = Chat::with(['customer', 'messages'])
        //     ->orderBy('updated_at', 'desc')
        //     ->get();

        $chats = [
            [
                'id' => '1',
                'customer' => [
                    'name' => 'Nguyễn Văn Minh',
                    'email' => 'nguyenvanminh@gmail.com',
                    'phone' => '0123456789',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Tôi muốn hỏi về combo gia đình mới',
                'last_message_time' => now()->subMinutes(2),
                'status' => 'waiting',
                'unread_count' => 3,
            ],
            [
                'id' => '2',
                'customer' => [
                    'name' => 'Trần Thị Hương',
                    'email' => 'tranhuong2024@email.com',
                    'phone' => '0987654321',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Cảm ơn bạn đã hỗ trợ nhiệt tình!',
                'last_message_time' => now()->subMinutes(8),
                'status' => 'active',
                'unread_count' => 0,
            ],
            [
                'id' => '3',
                'customer' => [
                    'name' => 'Lê Hoàng Nam',
                    'email' => 'hoangnam.dev@outlook.com',
                    'phone' => '0369852147',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Đơn hàng #FF2024001 của tôi bị delay không ạ?',
                'last_message_time' => now()->subMinutes(15),
                'status' => 'waiting',
                'unread_count' => 1,
            ],
            [
                'id' => '4',
                'customer' => [
                    'name' => 'Phạm Thị Lan',
                    'email' => 'phamlan.work@gmail.com',
                    'phone' => '0912345678',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Tôi có thể đổi địa chỉ giao hàng được không?',
                'last_message_time' => now()->subMinutes(25),
                'status' => 'active',
                'unread_count' => 0,
            ],
            [
                'id' => '5',
                'customer' => [
                    'name' => 'Võ Minh Tuấn',
                    'email' => 'vominhtuan88@yahoo.com',
                    'phone' => '0834567890',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Món ăn rất ngon, cảm ơn FastFood!',
                'last_message_time' => now()->subHour(1),
                'status' => 'closed',
                'unread_count' => 0,
            ],
            [
                'id' => '6',
                'customer' => [
                    'name' => 'Đặng Thị Mai',
                    'email' => 'dangmai.student@edu.vn',
                    'phone' => '0756789012',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Có chương trình khuyến mãi nào cho sinh viên không ạ?',
                'last_message_time' => now()->subHour(2),
                'status' => 'waiting',
                'unread_count' => 2,
            ],
            [
                'id' => '7',
                'customer' => [
                    'name' => 'Bùi Văn Đức',
                    'email' => 'buivanduc.biz@company.com',
                    'phone' => '0678901234',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Tôi muốn đặt tiệc cho 50 người',
                'last_message_time' => now()->subHours(3),
                'status' => 'active',
                'unread_count' => 0,
            ],
            [
                'id' => '8',
                'customer' => [
                    'name' => 'Hoàng Thị Linh',
                    'email' => 'hoanglinh.designer@creative.vn',
                    'phone' => '0590123456',
                    'avatar' => '/placeholder.svg?height=40&width=40'
                ],
                'last_message' => 'Cảm ơn đã giải quyết vấn đề!',
                'last_message_time' => now()->subHours(5),
                'status' => 'closed',
                'unread_count' => 0,
            ],
        ];

        return response()->json([
            'success' => true,
            'chats' => $chats,
        ]);
    }

    /**
     * Get messages for a specific chat
     */
    public function getChatMessages(Request $request, $chatId): JsonResponse
    {
        // Here you would fetch messages from database
        // $messages = Message::where('chat_id', $chatId)
        //     ->orderBy('created_at', 'asc')
        //     ->get();

        $messagesByChat = [
            '1' => [
                [
                    'id' => '1',
                    'content' => 'Xin chào! Tôi muốn hỏi về combo gia đình mới mà FastFood vừa ra mắt',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(5),
                    'type' => 'text',
                ],
                [
                    'id' => '2',
                    'content' => 'Combo này có những món gì và giá bao nhiêu ạ?',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(4),
                    'type' => 'text',
                ],
                [
                    'id' => '3',
                    'content' => 'Tôi muốn hỏi về combo gia đình mới',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(2),
                    'type' => 'text',
                ],
            ],
            '2' => [
                [
                    'id' => '1',
                    'content' => 'Chào bạn! Tôi vừa đặt đơn hàng nhưng quên không chọn thêm nước uống',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(20),
                    'type' => 'text',
                ],
                [
                    'id' => '2',
                    'content' => 'Xin chào! Để tôi kiểm tra đơn hàng của bạn. Bạn có thể cung cấp mã đơn hàng không?',
                    'sender' => 'admin',
                    'timestamp' => now()->subMinutes(18),
                    'type' => 'text',
                ],
                [
                    'id' => '3',
                    'content' => 'Mã đơn hàng là FF2024002 ạ',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(17),
                    'type' => 'text',
                ],
                [
                    'id' => '4',
                    'content' => 'Tôi đã cập nhật đơn hàng và thêm 2 ly Coca cho bạn. Không tính thêm phí giao hàng nhé!',
                    'sender' => 'admin',
                    'timestamp' => now()->subMinutes(15),
                    'type' => 'text',
                ],
                [
                    'id' => '5',
                    'content' => 'Cảm ơn bạn đã hỗ trợ nhiệt tình!',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(8),
                    'type' => 'text',
                ],
            ],
            '3' => [
                [
                    'id' => '1',
                    'content' => 'Chào admin! Tôi đặt đơn hàng từ 45 phút trước rồi mà chưa thấy shipper liên hệ',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(20),
                    'type' => 'text',
                ],
                [
                    'id' => '2',
                    'content' => 'Đơn hàng #FF2024001 của tôi bị delay không ạ?',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(15),
                    'type' => 'text',
                ],
            ],
            '4' => [
                [
                    'id' => '1',
                    'content' => 'Xin chào! Tôi vừa đặt đơn hàng nhưng cần đổi địa chỉ giao hàng',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(30),
                    'type' => 'text',
                ],
                [
                    'id' => '2',
                    'content' => 'Chào bạn! Tôi có thể hỗ trợ bạn thay đổi địa chỉ. Bạn cho tôi mã đơn hàng nhé',
                    'sender' => 'admin',
                    'timestamp' => now()->subMinutes(28),
                    'type' => 'text',
                ],
                [
                    'id' => '3',
                    'content' => 'Mã đơn hàng FF2024003. Địa chỉ mới là: 123 Nguyễn Văn Linh, Quận 7, TP.HCM',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(27),
                    'type' => 'text',
                ],
                [
                    'id' => '4',
                    'content' => 'Đã cập nhật địa chỉ mới cho bạn. Shipper sẽ giao đến địa chỉ mới trong 20 phút nữa',
                    'sender' => 'admin',
                    'timestamp' => now()->subMinutes(26),
                    'type' => 'text',
                ],
                [
                    'id' => '5',
                    'content' => 'Tôi có thể đổi địa chỉ giao hàng được không?',
                    'sender' => 'customer',
                    'timestamp' => now()->subMinutes(25),
                    'type' => 'text',
                ],
            ],
            '6' => [
                [
                    'id' => '1',
                    'content' => 'Chào FastFood! Mình là sinh viên, có chương trình ưu đãi gì không ạ?',
                    'sender' => 'customer',
                    'timestamp' => now()->subHour(2)->subMinutes(5),
                    'type' => 'text',
                ],
                [
                    'id' => '2',
                    'content' => 'Có chương trình khuyến mãi nào cho sinh viên không ạ?',
                    'sender' => 'customer',
                    'timestamp' => now()->subHour(2),
                    'type' => 'text',
                ],
            ],
            '7' => [
                [
                    'id' => '1',
                    'content' => 'Xin chào! Công ty tôi muốn đặt tiệc cho sự kiện 50 người',
                    'sender' => 'customer',
                    'timestamp' => now()->subHours(3)->subMinutes(10),
                    'type' => 'text',
                ],
                [
                    'id' => '2',
                    'content' => 'Chào anh! Chúng tôi có gói tiệc doanh nghiệp rất phù hợp. Tôi sẽ gửi bảng giá cho anh',
                    'sender' => 'admin',
                    'timestamp' => now()->subHours(3)->subMinutes(8),
                    'type' => 'text',
                ],
                [
                    'id' => '3',
                    'content' => 'Tôi muốn đặt tiệc cho 50 người',
                    'sender' => 'customer',
                    'timestamp' => now()->subHours(3),
                    'type' => 'text',
                ],
            ],
        ];

        $messages = $messagesByChat[$chatId] ?? [];

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message from admin
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'chat_id' => 'required|string',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:text,image,file',
        ]);

        // Here you would save the message to database
        // $message = Message::create([
        //     'chat_id' => $request->chat_id,
        //     'content' => $request->message,
        //     'sender' => 'admin',
        //     'type' => $request->type,
        // ]);

        // Send real-time notification to customer
        // broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    }

    /**
     * Update admin online status
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'online' => 'required|boolean',
        ]);

        // Here you would update admin status in database or cache
        // Cache::put('admin_online_' . auth()->id(), $request->online, 3600);

        return response()->json([
            'success' => true,
            'status' => $request->online ? 'online' : 'offline',
        ]);
    }

    /**
     * Close a chat conversation
     */
    public function closeChat(Request $request, $chatId): JsonResponse
    {
        // Here you would update chat status in database
        // Chat::where('id', $chatId)->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Chat closed successfully',
        ]);
    }

    /**
     * Get chat statistics
     */
    public function getStatistics(): JsonResponse
    {
        // Here you would calculate real statistics
        $stats = [
            'total_chats' => 248,
            'active_chats' => 18,
            'waiting_chats' => 7,
            'closed_chats' => 223,
            'response_time' => '1.8 phút',
            'satisfaction_rate' => 4.7,
            'today_chats' => 32,
            'resolved_today' => 28,
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats,
        ]);
    }
}
