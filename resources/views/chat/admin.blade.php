@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản lý Chat')

@section('page-style')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .chat-sidebar {
            width: 25%;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }

        .chat-main {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 15px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #f8f9fa;
        }

        .chat-footer {
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }

        .chat-footer input {
            flex-grow: 1;
            border-radius: 20px;
            border: 1px solid #ccc;
            padding: 10px 15px;
        }

        .chat-footer button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 20px;
            font-weight: 600;
        }

        .chat-footer button:hover {
            background-color: #0056b3;
        }

        .chat-sidebar-item {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }

        .chat-sidebar-item:hover {
            background-color: #f1f1f1;
        }

        .chat-sidebar-item.active {
            background-color: #e9ecef;
        }

        .customer-info {
            width: 25%;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 1px solid #ddd;
        }

        .customer-info h6 {
            font-weight: bold;
        }

        .customer-info p {
            margin-bottom: 5px;
        }

        .customer-info .btn {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="p-3">
                <input type="text" class="form-control" placeholder="Tìm kiếm cuộc trò chuyện...">
            </div>
            @foreach ($conversations as $conversation)
                <div class="chat-sidebar-item {{ $loop->first ? 'active' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>{{ $conversation->customer->name }}</strong>
                        <span class="badge bg-warning text-dark">{{ $conversation->status }}</span>
                    </div>
                    <small>{{ $conversation->updated_at->diffForHumans() }}</small>
                </div>
            @endforeach
        </div>

        <!-- Main Chat -->
        <div class="chat-main">
            <div class="chat-header">
                <span>{{ $conversations->first()->customer->name ?? 'Khách hàng' }}</span>
                <span class="badge bg-warning text-dark">{{ $conversations->first()->status ?? '' }}</span>
            </div>
            <div class="chat-messages" id="chat-box">
                @foreach ($conversations->first()->messages ?? [] as $msg)
                    @php
                        $isSent = $msg->sender_id === auth()->id();
                    @endphp
                    <div class="d-flex {{ $isSent ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                        <div class="p-2 px-3 rounded-3 {{ $isSent ? 'bg-primary text-white' : 'bg-light text-dark' }}">
                            <div class="small fw-bold">
                                {{ $isSent ? 'Bạn' : $msg->sender_name ?? 'Khách hàng' }}
                            </div>
                            <div>{{ $msg->message }}</div>
                            <div class="text-end small text-muted mt-1">
                                {{ \Carbon\Carbon::parse($msg->sent_at)->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="chat-footer">
                <input type="text" id="message" placeholder="Nhập tin nhắn...">
                <button onclick="sendMessage()">Gửi</button>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <h6>Thông tin khách hàng</h6>
            <p><strong>Tên:</strong> {{ $conversations->first()->customer->name ?? '' }}</p>
            <p><strong>Email:</strong> {{ $conversations->first()->customer->email ?? '' }}</p>
            <p><strong>Số điện thoại:</strong> 0123456789</p>
            <p><strong>Trạng thái:</strong> <span class="badge bg-warning text-dark">Chờ phản hồi</span></p>
            <p><strong>Lần cuối hoạt động:</strong> {{ $conversations->first()->updated_at->diffForHumans() }}</p>
            <p><strong>Tổng đơn hàng:</strong> 22 đơn</p>
            <button class="btn btn-primary">Xem lịch sử đơn hàng</button>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        const chatBox = document.getElementById('chat-box');

        function sendMessage() {
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();
            if (!message) return;

            // Append message to chat box
            const messageHtml = `
                <div class="d-flex justify-content-end mb-2">
                    <div class="p-2 px-3 rounded-3 bg-primary text-white">
                        <div class="small fw-bold">Bạn</div>
                        <div>${message}</div>
                        <div class="text-end small text-muted mt-1">${new Date().toLocaleTimeString()}</div>
                    </div>
                </div>
            `;
            chatBox.innerHTML += messageHtml;
            chatBox.scrollTop = chatBox.scrollHeight;

            // Clear input
            messageInput.value = '';
        }
    </script>
@endsection
