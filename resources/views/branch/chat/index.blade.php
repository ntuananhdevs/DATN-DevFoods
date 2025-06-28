@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Chat - Chi nhánh')

@section('content')
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Chat với khách hàng</h1>
        
        @if($conversations->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Danh sách conversations -->
                <div class="lg:col-span-1">
                    <h2 class="text-lg font-semibold mb-4">Danh sách cuộc trò chuyện</h2>
                    <div class="space-y-2">
                        @foreach($conversations as $conversation)
                            <div class="border rounded-lg p-3 hover:bg-gray-50 cursor-pointer conversation-item" 
                                 data-conversation-id="{{ $conversation->id }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-medium">
                                            {{ $conversation->customer->name ?? 'Khách hàng' }}
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            {{ $conversation->messages->first()->message ?? 'Chưa có tin nhắn' }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $conversation->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full 
                                        @if($conversation->status === 'active') bg-green-100 text-green-800
                                        @elseif($conversation->status === 'resolved') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $conversation->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Chat area -->
                <div class="lg:col-span-2">
                    <div class="border rounded-lg h-96 flex flex-col">
                        <div class="bg-gray-50 p-4 border-b">
                            <h3 class="font-semibold">Chọn cuộc trò chuyện để bắt đầu</h3>
                        </div>
                        <div class="flex-1 p-4" id="chat-messages">
                            <p class="text-gray-500 text-center mt-8">Chọn một cuộc trò chuyện từ danh sách bên trái</p>
                        </div>
                        <div class="p-4 border-t" id="chat-input" style="display: none;">
                            <form id="message-form" class="flex gap-2">
                                <input type="text" id="message-input" 
                                       class="flex-1 border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Nhập tin nhắn...">
                                <button type="submit" 
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    Gửi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Chưa có cuộc trò chuyện nào</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentConversationId = null;
    const currentUserId = {{ Auth::guard('manager')->id() }};
    
    // Xử lý click vào conversation
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const conversationId = this.dataset.conversationId;
            loadConversation(conversationId);
            
            // Highlight conversation được chọn
            document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('bg-blue-50'));
            this.classList.add('bg-blue-50');
        });
    });
    
    function loadConversation(conversationId) {
        currentConversationId = conversationId;
        
        fetch(`/branch/chat/api/conversation/${conversationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.messages, data.conversation);
                    document.getElementById('chat-input').style.display = 'block';
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải cuộc trò chuyện');
            });
    }
    
    function displayMessages(messages, conversation) {
        const chatMessages = document.getElementById('chat-messages');
        const customerName = conversation.customer ? conversation.customer.name : 'Khách hàng';
        
        chatMessages.innerHTML = `
            <div class="bg-gray-50 p-4 border-b mb-4">
                <h3 class="font-semibold">${customerName}</h3>
                <p class="text-sm text-gray-600">Trạng thái: ${conversation.status_label}</p>
            </div>
        `;
        
        messages.forEach(message => {
            const messageDiv = document.createElement('div');
            const isCurrentUser = message.sender && message.sender.id == currentUserId;
            messageDiv.className = `mb-4 ${isCurrentUser ? 'text-right' : 'text-left'}`;
            
            messageDiv.innerHTML = `
                <div class="inline-block max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${
                    isCurrentUser 
                        ? 'bg-blue-500 text-white' 
                        : 'bg-gray-200 text-gray-800'
                }">
                    <p class="text-sm">${message.message}</p>
                    <p class="text-xs mt-1 opacity-75">${new Date(message.created_at).toLocaleTimeString()}</p>
                </div>
            `;
            
            chatMessages.appendChild(messageDiv);
        });
        
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Xử lý gửi tin nhắn
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!currentConversationId) return;
        
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        const formData = new FormData();
        formData.append('conversation_id', currentConversationId);
        formData.append('message', message);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('/branch/chat/send-message', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                loadConversation(currentConversationId); // Reload messages
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi tin nhắn');
        });
    });
});
</script>
@endsection 