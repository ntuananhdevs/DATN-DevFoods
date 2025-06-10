<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ConversationController extends Controller
{
    public function getMessages($conversationId)
    {
        try {
            Log::info("Getting messages for conversation: " . $conversationId);

            $conversation = Conversation::with(['messages.sender'])->find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            $messages = $conversation->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message ?? $message->content ?? '',
                    'attachment' => $message->attachment,
                    'attachment_type' => $message->attachment_type,
                    'is_system_message' => $message->is_system_message ?? false,
                    'created_at' => $message->created_at->toISOString(),
                    'sender' => [
                        'id' => $message->sender ? $message->sender->id : 0,
                        'name' => $message->sender ? ($message->sender->name ?? $message->sender->full_name ?? 'Unknown') : 'System',
                        'role' => $message->sender_type ?? 'user'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting messages: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải tin nhắn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getConversation($conversationId)
    {
        try {
            Log::info("Getting conversation: " . $conversationId);

            $conversation = Conversation::with(['branch', 'customer', 'messages.sender'])->find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            $messages = $conversation->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'sender_id' => $message->sender_id,
                    'message' => $message->message ?? $message->content ?? '',
                    'attachment' => $message->attachment,
                    'attachment_type' => $message->attachment_type,
                    'is_system_message' => $message->is_system_message ?? false,
                    'created_at' => $message->created_at->toISOString(),
                    'sender' => [
                        'id' => $message->sender ? $message->sender->id : 0,
                        'name' => $message->sender ? ($message->sender->name ?? $message->sender->full_name ?? 'Unknown') : 'System',
                        'role' => $message->sender_type ?? 'user'
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'conversation' => [
                    'id' => $conversation->id,
                    'subject' => $conversation->subject ?? 'Cuộc trò chuyện #' . $conversation->id,
                    'status' => $conversation->status,
                    'is_distributed' => $conversation->is_distributed ?? false,
                    'created_at' => $conversation->created_at->toISOString(),
                    'updated_at' => $conversation->updated_at->toISOString(),
                    'customer' => [
                        'id' => $conversation->customer ? $conversation->customer->id : 0,
                        'name' => $conversation->customer ? ($conversation->customer->name ?? $conversation->customer->full_name ?? 'Unknown Customer') : 'Test Customer',
                        'email' => $conversation->customer ? $conversation->customer->email : 'test@customer.com'
                    ],
                    'branch' => $conversation->branch ? [
                        'id' => $conversation->branch->id,
                        'name' => $conversation->branch->name
                    ] : null
                ],
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting conversation: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải cuộc trò chuyện: ' . $e->getMessage()
            ], 500);
        }
    }
}
