<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;
use App\Models\ConversationUser;
use App\Models\User;

class ConversationUserSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các cuộc trò chuyện
        $conversations = Conversation::all();

        foreach ($conversations as $conversation) {
            // Thêm khách hàng vào cuộc trò chuyện
            ConversationUser::create([
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->customer_id,
                'user_type' => 'customer',
            ]);

            // Nếu cuộc trò chuyện đã phân phối, thêm admin chi nhánh
            if ($conversation->branch_id) {
                ConversationUser::create([
                    'conversation_id' => $conversation->id,
                    'user_id' => $conversation->branch_id,
                    'user_type' => 'branch_admin',
                ]);
            }
        }
    }
}
