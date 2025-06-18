<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Branch;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo người dùng mẫu
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'user_name' => 'Customer',
                'full_name' => 'John Doe',
                'phone' => '123456789',
                'avatar' => 'avatars/default.jpg',
                'password' => bcrypt('password'),
            ]
        );

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'user_name' => 'SuperAdmin',
                'full_name' => 'Admin User',
                'phone' => '987654321',
                'avatar' => 'avatars/default.jpg',
                'password' => bcrypt('password'),
            ]
        );

        // Lấy các chi nhánh đã có
        $branch1 = \App\Models\Branch::where('name', 'Chi nhánh Hà Nội')->first();
        $branch2 = \App\Models\Branch::where('name', 'Chi nhánh Hồ Chí Minh')->first();
        $branch3 = \App\Models\Branch::where('name', 'Chi nhánh Đà Nẵng')->first();

        // Giả sử bạn có các user branch admin với id 27, 28, 29 (hoặc lấy đúng user manager của từng chi nhánh)
        $branches = [
            ['branch_id' => $branch1 ? $branch1->id : null, 'user_id' => 16],
            ['branch_id' => $branch2 ? $branch2->id : null, 'user_id' => 17],
            ['branch_id' => $branch3 ? $branch3->id : null, 'user_id' => 18],
        ];

        // Tạo cuộc trò chuyện chưa phân phối
        $conversation1 = Conversation::create([
            'customer_id' => $customer->id,
            'branch_id' => null,
            'status' => 'new',
            'is_distributed' => false,
        ]);

        // Thêm tin nhắn vào cuộc trò chuyện chưa phân phối
        ChatMessage::create([
            'conversation_id' => $conversation1->id,
            'sender_id' => $customer->id,
            'receiver_id' => $superAdmin->id,
            'message' => 'Hello, I need help with my order.',
            'sent_at' => now(),
            'sender_type' => 'customer',
            'receiver_type' => 'super_admin',
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation1->id,
            'sender_id' => $superAdmin->id,
            'receiver_id' => $customer->id,
            'message' => 'Hi John, how can I assist you today?',
            'sent_at' => now(),
            'sender_type' => 'super_admin',
            'receiver_type' => 'customer',
        ]);

        // Thêm người tham gia vào cuộc trò chuyện chưa phân phối
        ConversationUser::create([
            'conversation_id' => $conversation1->id,
            'user_id' => $customer->id,
            'user_type' => 'customer',
        ]);

        ConversationUser::create([
            'conversation_id' => $conversation1->id,
            'user_id' => $superAdmin->id,
            'user_type' => 'super_admin',
        ]);

        // Tạo cuộc trò chuyện đã phân phối cho các chi nhánh
        foreach ($branches as $branch) {
            if (!$branch['branch_id']) continue;
            $conversation = Conversation::create([
                'customer_id' => $customer->id,
                'branch_id' => $branch['branch_id'],
                'status' => 'distributed',
                'is_distributed' => true,
                'distribution_time' => now(),
            ]);

            // Thêm tin nhắn vào cuộc trò chuyện đã phân phối
            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $customer->id,
                'receiver_id' => $branch['user_id'],
                'message' => 'I want to know the status of my branch order.',
                'sent_at' => now(),
                'sender_type' => 'customer',
                'receiver_type' => 'branch_admin',
            ]);

            ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $branch['user_id'],
                'receiver_id' => $customer->id,
                'message' => 'Thanks for contacting us. Your order is being processed.',
                'sent_at' => now(),
                'sender_type' => 'branch_admin',
                'receiver_type' => 'customer',
            ]);

            // Thêm người tham gia vào cuộc trò chuyện đã phân phối
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $customer->id,
                'user_type' => 'customer',
            ]);

            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $branch['user_id'],
                'user_type' => 'branch_admin',
            ]);
        }
    }
}
