<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customer Chat</h5>
            </div>
            <div class="card-body p-3" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;"
                id="chat-box">
                @foreach ($messages as $msg)
                    @php $isCurrentUser = $msg->sender_id === auth()->id(); @endphp
                    <div class="d-flex {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                        <div class="p-2 px-3 rounded-3 {{ $isCurrentUser ? 'bg-success text-white' : 'bg-light text-dark' }}"
                            style="max-width: 70%;">
                            <div class="small fw-bold">{{ $isCurrentUser ? 'You' : 'Admin #' . $msg->sender_id }}</div>
                            @if ($msg->message)
                                <div>{{ $msg->message }}</div>
                            @endif
                            @if ($msg->attachment)
                                @if ($msg->attachment_type === 'image')
                                    <img src="{{ asset('storage/' . $msg->attachment) }}" class="img-fluid mt-2 rounded"
                                        style="max-height: 150px;">
                                @else
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank"
                                        class="d-block mt-2 text-dark">Download file</a>
                                @endif
                            @endif
                            <div class="text-end small text-muted mt-1">
                                {{ \Carbon\Carbon::parse($msg->sent_at)->format('H:i') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer">
                <form id="chat-form" class="d-flex gap-2" onsubmit="event.preventDefault(); sendMessage();">
                    <input type="text" id="message" class="form-control" placeholder="Type your message...">
                    <button type="submit" class="btn btn-success px-4">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const currentUserId = {{ auth()->id() }};
        let conversationId = {!! json_encode($conversationId ?? null) !!};
        const chatBox = document.getElementById('chat-box');

        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('chat-channel');
        channel.bind('new-message', function(data) {
            if (data.conversation_id !== conversationId) return;

            const isCurrentUser = currentUserId === data.sender_id;
            let contentHtml = '';

            if (data.message) {
                contentHtml += `<div>${data.message}</div>`;
            }

            if (data.attachment) {
                if (data.attachment_type === 'image') {
                    contentHtml +=
                        `<img src="/storage/${data.attachment}" class="img-fluid mt-2 rounded" style="max-height: 150px;">`;
                } else {
                    contentHtml +=
                        `<a href="/storage/${data.attachment}" class="d-block mt-2 text-dark" target="_blank">Download file</a>`;
                }
            }

            const messageHtml = `
                <div class="d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-2">
                    <div class="p-2 px-3 rounded-3 ${isCurrentUser ? 'bg-success text-white' : 'bg-light text-dark'}" style="max-width: 70%;">
                        <div class="small fw-bold">${isCurrentUser ? 'You' : 'Admin #' + data.sender_id}</div>
                        ${contentHtml}
                        <div class="text-end small text-muted mt-1">${new Date(data.sent_at).toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
            chatBox.innerHTML += messageHtml;
            chatBox.scrollTop = chatBox.scrollHeight;
        });

        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();
            if (message === '') return;

            const headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };

            if (!conversationId) {
                // Tạo cuộc trò chuyện mới
                fetch(`/api/conversations`, {
                        method: 'POST',
                        headers: headers,
                        body: JSON.stringify({
                            customer_id: currentUserId,
                            message: message
                        })
                    }).then(res => res.json())
                    .then(data => {
                        conversationId = data.conversation.id;
                        appendMessage(message, true);
                        messageInput.value = '';
                    }).catch(error => {
                        console.error(error);
                        alert('Không thể tạo cuộc trò chuyện. Vui lòng thử lại.');
                    });
            } else {
                // Gửi tin nhắn
                fetch(`/api/conversations/${conversationId}/messages`, { // Sửa URL để đảm bảo chính xác
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify({
                        message: message,
                        sender_id: currentUserId,
                        sender_type: 'customer'
                    })
                }).then(response => {
                    if (!response.ok) throw new Error('Gửi thất bại');
                    appendMessage(message, true);
                    messageInput.value = '';
                }).catch(error => {
                    console.error(error);
                    alert('Không thể gửi tin nhắn. Vui lòng kiểm tra kết nối hoặc thử lại.');
                });
            }
        }

        function appendMessage(message, isCurrentUser) {
            const messageHtml = `
                <div class="d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-2">
                    <div class="p-2 px-3 rounded-3 ${isCurrentUser ? 'bg-success text-white' : 'bg-light text-dark'}" style="max-width: 70%;">
                        <div class="small fw-bold">${isCurrentUser ? 'You' : 'Admin'}</div>
                        <div>${message}</div>
                        <div class="text-end small text-muted mt-1">${new Date().toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
            chatBox.innerHTML += messageHtml;
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        chatBox.scrollTop = chatBox.scrollHeight;

        document.getElementById('message').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
</body>

</html>
