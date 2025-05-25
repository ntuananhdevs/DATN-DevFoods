@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Admin Chat')

@section('page-style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Container chat */
        #chat-box {
            height: 400px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 5px rgb(0 0 0 / 0.1);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Tin nhắn chung */
        .message {
            max-width: 70%;
            padding: 10px 14px;
            margin-bottom: 12px;
            border-radius: 20px;
            box-shadow: 0 1px 2px rgb(0 0 0 / 0.15);
            word-wrap: break-word;
            position: relative;
            font-size: 14px;
            line-height: 1.4;
        }

        /* Người gửi (Bạn) */
        .message.sent {
            background-color: #0084ff;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: 20px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        /* Người nhận (Khách hàng) */
        .message.received {
            background-color: #e4e6eb;
            color: #050505;
            margin-right: auto;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 20px;
            border-top-right-radius: 20px;
            border-top-left-radius: 20px;
        }

        /* Tên người gửi nhỏ trên đầu bong bóng */
        .sender-name {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 4px;
            opacity: 0.7;
        }

        /* Thời gian */
        .timestamp {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            position: absolute;
            bottom: 4px;
            right: 12px;
        }

        .message.received .timestamp {
            color: rgba(0, 0, 0, 0.5);
        }

        /* Ảnh đính kèm */
        .message img {
            max-width: 100%;
            border-radius: 10px;
            margin-top: 8px;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.2);
            cursor: pointer;
        }

        /* Link file đính kèm */
        .message a {
            display: inline-block;
            margin-top: 8px;
            color: #1877f2;
            text-decoration: none;
            font-weight: 600;
        }

        .message a:hover {
            text-decoration: underline;
        }

        /* Form chat footer */
        #chat-form {
            display: flex;
            gap: 10px;
            padding-top: 10px;
            align-items: center;
        }

        #message {
            flex-grow: 1;
            border-radius: 20px;
            border: 1px solid #ccc;
            padding: 10px 15px;
            font-size: 14px;
            resize: none;
            height: 40px;
            outline: none;
            transition: border-color 0.2s;
        }

        #message:focus {
            border-color: #0084ff;
            box-shadow: 0 0 5px #0084ff;
        }

        #attachment {
            border-radius: 20px;
            border: 1px solid #ccc;
            padding: 5px 10px;
            font-size: 14px;
            max-width: 160px;
        }

        #send-btn {
            background-color: #0084ff;
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            white-space: nowrap;
        }

        #send-btn:hover {
            background-color: #006fd6;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Admin Chat</h5>
                <small>Realtime messaging with customers</small>
            </div>
            <div class="card-body p-3" id="chat-box" aria-live="polite" aria-relevant="additions">
                @foreach ($messages as $msg)
                    @php
                        $isSent = $msg->sender_id === auth()->id();
                        $senderName = $isSent ? 'You' : $msg->sender_name ?? 'Customer';
                    @endphp
                    <div class="message {{ $isSent ? 'sent ms-auto' : 'received me-auto' }}">
                        <div class="sender-name">{{ $senderName }}</div>
                        @if ($msg->message)
                            <div>{!! nl2br(e($msg->message)) !!}</div>
                        @endif
                        @if ($msg->attachment)
                            @if ($msg->attachment_type === 'image')
                                <img src="{{ asset('storage/' . $msg->attachment) }}" alt="Image attachment"
                                    loading="lazy" />
                            @else
                                <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank"
                                    rel="noopener noreferrer">Download file</a>
                            @endif
                        @endif
                        <div class="timestamp">{{ \Carbon\Carbon::parse($msg->sent_at)->format('H:i') }}</div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <form id="chat-form" enctype="multipart/form-data" onsubmit="event.preventDefault(); sendMessage();">
                    <textarea id="message" placeholder="Type a message..." rows="1" autocomplete="off"></textarea>
                    <input type="file" id="attachment" accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt" />
                    <button type="submit" id="send-btn" aria-label="Send message">Send</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>

    <script>
        const currentUserId = {{ auth()->id() }};
        const chatBox = document.getElementById('chat-box');

        // Scroll xuống cuối cùng khi load trang
        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
        scrollToBottom();

        // Khởi tạo Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            useTLS: true
        });

        const channel = pusher.subscribe('chat-channel');

        channel.bind('new-message', function(data) {
            const isCurrentUser = currentUserId === data.sender_id;
            const senderName = isCurrentUser ? 'You' : (data.sender_name || 'Customer');

            const msgDiv = document.createElement('div');
            msgDiv.classList.add('message', isCurrentUser ? 'sent' : 'received');
            if (isCurrentUser) {
                msgDiv.classList.add('ms-auto');
            } else {
                msgDiv.classList.add('me-auto');
            }

            // Nội dung tin nhắn
            let contentHtml = `<div class="sender-name">${senderName}</div>`;
            if (data.message) {
                contentHtml += `<div>${data.message.replace(/\n/g, '<br>')}</div>`;
            }
            if (data.attachment) {
                if (data.attachment_type === 'image') {
                    contentHtml +=
                        `<img src="/storage/${data.attachment}" alt="Image attachment" loading="lazy" />`;
                } else {
                    contentHtml +=
                        `<a href="/storage/${data.attachment}" target="_blank" rel="noopener noreferrer">Download file</a>`;
                }
            }
            contentHtml +=
                `<div class="timestamp">${new Date(data.sent_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>`;

            msgDiv.innerHTML = contentHtml;
            chatBox.appendChild(msgDiv);

            scrollToBottom();
        });

        // Gửi tin nhắn
        function sendMessage() {
            const messageInput = document.getElementById('message');
            const fileInput = document.getElementById('attachment');
            const message = messageInput.value.trim();
            const file = fileInput.files[0];

            if (!message && !file) {
                alert('Please enter a message or select a file.');
                return;
            }

            const formData = new FormData();
            formData.append('message', message);
            if (file) {
                formData.append('attachment', file);
            }
            formData.append('receiver_id', 2); // Thay bằng ID customer phù hợp
            formData.append('sender_type', 'branch_admin');
            formData.append('receiver_type', 'customer');

            fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) throw new Error('Failed to send message');
                    messageInput.value = '';
                    fileInput.value = '';
                    scrollToBottom();
                })
                .catch(err => {
                    alert(err.message || 'Error sending message');
                });
        }

        // Gửi bằng phím Enter (Shift+Enter xuống dòng)
        document.getElementById('message').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
@endsection
