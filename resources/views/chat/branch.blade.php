@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Branch Chat')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5>Branch Chat</h5>
            </div>
            <div class="card-body">
                <h6>Distributed Conversations</h6>
                <ul>
                    @foreach ($conversations as $conversation)
                        <li>
                            Conversation #{{ $conversation->id }} - Customer: {{ $conversation->customer->name }}
                            <a href="{{ route('chat.branch', $conversation->id) }}" class="btn btn-sm btn-primary">
                                View Chat
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5>Branch Chat</h5>
            </div>
            <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto;">
                @foreach ($messages as $msg)
                    @php
                        $isCurrentUser = $msg->sender_id === auth()->id();
                    @endphp
                    <div class="d-flex {{ $isCurrentUser ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                        <div
                            class="p-2 px-3 rounded-3 {{ $isCurrentUser ? 'bg-primary text-white' : 'bg-light text-dark' }}">
                            <div class="small fw-bold">
                                {{ $isCurrentUser ? 'You' : 'Customer #' . $msg->sender_id }}
                            </div>
                            <div>{{ $msg->message }}</div>
                            <div class="text-end small text-muted mt-1">
                                {{ \Carbon\Carbon::parse($msg->sent_at)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <form id="chat-form" class="d-flex gap-2" onsubmit="event.preventDefault(); sendMessage();">
                    <input type="text" id="message" class="form-control" placeholder="Type your message...">
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const conversationId = {{ $conversationId ?? 'null' }}; // Ensure $conversationId is defined
        const chatBox = document.getElementById('chat-box');

        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();
            if (!message) return;

            fetch(`/chat/${conversationId}/send`, { // Use conversationId in the URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    sender_type: 'branch_staff'
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Failed to send message.');
                }
                appendMessage(message, true);
                messageInput.value = '';
            }).catch(error => {
                console.error(error);
                alert('Failed to send message. Please try again.');
            });
        }

        function appendMessage(message, isCurrentUser) {
            const messageHtml = `
                <div class="d-flex ${isCurrentUser ? 'justify-content-end' : 'justify-content-start'} mb-2">
                    <div class="p-2 px-3 rounded-3 ${isCurrentUser ? 'bg-primary text-white' : 'bg-light text-dark'}">
                        <div class="small fw-bold">${isCurrentUser ? 'You' : 'Customer'}</div>
                        <div>${message}</div>
                        <div class="text-end small text-muted mt-1">${new Date().toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
            chatBox.innerHTML += messageHtml;
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        // Scroll chat to the bottom when the page loads
        chatBox.scrollTop = chatBox.scrollHeight;

        // Allow pressing Enter to send a message
        document.getElementById('message').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
@endsection
