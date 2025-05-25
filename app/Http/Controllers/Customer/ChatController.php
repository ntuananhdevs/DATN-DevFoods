<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Send a message to admin
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'required|in:text,image,file',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Here you would typically:
        // 1. Save the message to database
        // 2. Send notification to admin
        // 3. Return appropriate response

        $message = [
            'id' => uniqid(),
            'content' => $request->message,
            'sender' => 'user',
            'timestamp' => now(),
            'type' => $request->type,
        ];

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat-files', 'public');
            
            $message['file_path'] = $path;
            $message['file_name'] = $file->getClientOriginalName();
            $message['file_size'] = $file->getSize();
        }

        // Simulate admin response (in real app, this would be handled by admin interface)
        $adminResponse = $this->generateAdminResponse($request->message);

        return response()->json([
            'success' => true,
            'user_message' => $message,
            'admin_response' => $adminResponse,
        ]);
    }

    /**
     * Submit chat rating
     */
    public function submitRating(Request $request): JsonResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        // Here you would save the rating to database
        // ChatRating::create([
        //     'session_id' => $request->session()->getId(),
        //     'rating' => $request->rating,
        //     'feedback' => $request->feedback,
        //     'created_at' => now(),
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã đánh giá!',
        ]);
    }

    /**
     * Get chat history
     */
    public function getChatHistory(Request $request): JsonResponse
    {
        // Here you would fetch chat history from database
        $messages = [
            [
                'id' => '1',
                'content' => 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊',
                'sender' => 'admin',
                'timestamp' => now()->subMinutes(5),
                'type' => 'text',
            ]
        ];

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Generate admin response (simulation)
     */
    private function generateAdminResponse(string $userMessage): array
    {
        $responses = [
            'Cảm ơn bạn đã liên hệ! Tôi sẽ hỗ trợ bạn ngay. 😊',
            'Để tôi kiểm tra thông tin cho bạn nhé. 🔍',
            'Bạn có thể cho tôi biết thêm chi tiết không? 🤔',
            'Tôi hiểu rồi, để tôi hỗ trợ bạn. ✅',
            'Bạn có cần tôi gọi điện tư vấn trực tiếp không? 📞',
        ];

        return [
            'id' => uniqid(),
            'content' => $responses[array_rand($responses)],
            'sender' => 'admin',
            'timestamp' => now()->addSeconds(2),
            'type' => 'text',
        ];
    }
}
