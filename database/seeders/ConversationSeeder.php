<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Models\User;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo người dùng mới (khách hàng, admin, chi nhánh)
        $customer = User::firstOrCreate(
            ['email' => 'customer2@example.com'],
            [
                'user_name' => 'Customer2',
                'full_name' => 'John Doe',
                'phone' => '1-123-456-7890',
                'avatar' => 'avatars/default.jpg',
                'password' => bcrypt('secret123')
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin2@example.com'],
            [
                'user_name' => 'Admin2',
                'full_name' => 'Alice Smith',
                'phone' => '1-987-654-3210',
                'avatar' => 'avatars/default.jpg',
                'password' => bcrypt('secret123')
            ]
        );

        $branchAdmin = User::firstOrCreate(
            ['email' => 'branchadmin2@example.com'],
            [
                'user_name' => 'BranchAdmin2',
                'full_name' => 'Bob Johnson',
                'phone' => '1-555-555-5555',
                'avatar' => 'avatars/default.jpg',
                'password' => bcrypt('secret123')
            ]
        );

        // Tạo cuộc trò chuyện chưa phân phối
        $conversation = Conversation::create([
            'customer_id' => $customer->id,
            'branch_id' => null,
            'status' => 'new',
            'is_distributed' => false,
        ]);

        // Thêm tin nhắn vào cuộc trò chuyện chưa phân phối
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $customer->id,
            'receiver_id' => $admin->id,
            'message' => 'Hello, I need help with my order.',
            'attachment' => null,
            'attachment_type' => null,
            'sent_at' => now(),
            'status' => 'sent',
            'read_at' => null,
            'is_deleted' => false,
            'is_system_message' => false,
            'related_order_id' => null,
            'sender_type' => 'customer',
            'receiver_type' => 'super_admin',
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'receiver_id' => $customer->id,
            'message' => 'Hi John, how can I assist you today?',
            'attachment' => null,
            'attachment_type' => null,
            'sent_at' => now(),
            'status' => 'sent',
            'read_at' => null,
            'is_deleted' => false,
            'is_system_message' => false,
            'related_order_id' => null,
            'sender_type' => 'super_admin',
            'receiver_type' => 'customer',
        ]);

        // Tạo cuộc trò chuyện đã phân phối
        $distributedConversation = Conversation::create([
            'customer_id' => $customer->id,
            'branch_id' => $branchAdmin->id,
            'status' => 'distributed',
            'is_distributed' => true,
            'distribution_time' => now(),
        ]);

        ChatMessage::create([
            'conversation_id' => $distributedConversation->id,
            'sender_id' => $customer->id,
            'receiver_id' => $branchAdmin->id,
            'message' => 'I want to know the status of my branch order.',
            'attachment' => null,
            'attachment_type' => null,
            'sent_at' => now(),
            'status' => 'sent',
            'read_at' => null,
            'is_deleted' => false,
            'is_system_message' => false,
            'related_order_id' => null,
            'sender_type' => 'customer',
            'receiver_type' => 'branch_admin',
        ]);

        ChatMessage::create([
            'conversation_id' => $distributedConversation->id,
            'sender_id' => $branchAdmin->id,
            'receiver_id' => $customer->id,
            'message' => 'Thanks for contacting us. Your order is being processed.',
            'attachment' => null,
            'attachment_type' => null,
            'sent_at' => now(),
            'status' => 'sent',
            'read_at' => null,
            'is_deleted' => false,
            'is_system_message' => false,
            'related_order_id' => null,
            'sender_type' => 'branch_admin',
            'receiver_type' => 'customer',
        ]);
    }
}
