 @extends('layouts.admin.contentLayoutMaster')

 @section('title', 'Quản Lý Chat - ' . ($branch->name ?? ''))
 <link rel="stylesheet" href="/css/admin/branchs/chat.css">
 @section('content')

     <div class="chat-container">
         <div class="chat-sidebar">
             <div class="chat-sidebar-header">
                 <h2>Cuộc trò chuyện</h2>
                 <p>Danh sách các cuộc trò chuyện của chi nhánh</p>
             </div>
             <div class="chat-list" id="chat-list">
                 @forelse ($conversations as $conv)
                     <div class="chat-item conversation-item {{ $loop->first ? 'active' : '' }}"
                         data-conversation-id="{{ $conv->id }}" data-status="{{ $conv->status }}"
                         data-customer-name="{{ $conv->customer->full_name ?? ($conv->customer->name ?? 'Khách hàng') }}"
                         data-customer-email="{{ $conv->customer->email }}"
                         data-branch-name="{{ $conv->branch ? $conv->branch->name : '' }}">
                         <div class="chat-item-header">
                             <span
                                 class="chat-item-name">{{ $conv->customer->full_name ?? ($conv->customer->name ?? 'Khách hàng') }}</span>

                         </div>
                         <div class="chat-item-preview">
                             {{ $conv->messages->last()?->message ? Str::limit($conv->messages->last()->message, 30) : '...' }}
                         </div>
                         <span
                             class="chat-item-time">{{ $conv->messages->last()?->created_at ? $conv->messages->last()->created_at->format('H:i') : '' }}</span>
                         <div class="chat-item-footer mt-2">

                             <div class="chat-item-badges">
                                 <span
                                     class="badge badge-distributed">{{ strtoupper($conv->status_label ?? 'Đã phân phối') }}</span>
                                 <span class="badge"
                                     style="background:#374151;color:#fff;">{{ $conv->branch?->name }}</span>
                             </div>
                             @if ($conv->unread_count ?? 0)
                                 <span class="unread-badge">{{ $conv->unread_count }}</span>
                             @endif
                         </div>
                     </div>
                 @empty
                     <div class="p-4 text-center ">
                         <strong>Hiện tại bạn chưa có cuộc trò chuyện nào với khách hàng.</strong><br>
                         Khi khách hàng nhắn tin, cuộc trò chuyện sẽ xuất hiện tại đây để bạn hỗ trợ.
                     </div>
                 @endforelse
             </div>
         </div>
         <div class="chat-main">
             <div class="chat-header" id="chat-header">
                 <div class="chat-header-user">
                     <div class="chat-avatar" id="chat-header-avatar"></div>
                     <div class="chat-header-info">
                         <div class="chat-header-name" id="chat-header-name"></div>
                         <div class="chat-header-email" id="chat-header-email"></div>
                     </div>
                 </div>
                 <div class="chat-header-actions">
                     <!-- Các action như gọi điện, video, v.v. nếu cần -->
                 </div>
             </div>
             <div class="chat-messages" id="chat-messages">
                 @if (isset($conversations) && count($conversations) > 0)
                     <!-- Tin nhắn sẽ được load bằng JS BranchChat -->
                 @else
                     <div class="flex flex-col items-center justify-center h-full p-8 text-center ">
                         <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No chat"
                             style="width:80px;height:80px;opacity:0.5;">
                         <h3 class="mt-4 mb-2 text-lg font-semibold">Chưa có cuộc trò chuyện nào</h3>
                         <p>Bạn sẽ thấy các cuộc trò chuyện với khách hàng tại đây khi có tin nhắn mới.</p>
                     </div>
                 @endif
             </div>
             <div class="chat-input">
                 <input type="text" id="chat-input-message" placeholder="Nhập tin nhắn..." autocomplete="off">
                 <div class="file-upload">
                     <label for="chat-input-image"><i class="fa fa-image"></i></label>
                     <input type="file" accept="image/*" id="chat-input-image" style="display:none;" />
                     <label for="chat-input-file"><i class="fa fa-paperclip"></i></label>
                     <input type="file"
                         accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/zip,application/x-rar-compressed,application/octet-stream"
                         id="chat-input-file" style="display:none;" />
                 </div>
                 <button id="chat-send-btn"><i class="fa fa-paper-plane"></i></button>
             </div>
         </div>
         <div class="chat-info-panel" id="chat-info-panel">
             <div style="margin-left: 70px;"
                 class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center font-bold text-xl mb-2 chat-info-avatar "
                 id="chat-info-avatar"></div>
             <div class="chat-info-header">

                 <div class="chat-info-details">
                     <div class="chat-info-name" id="chat-info-name"></div>
                     <div class="chat-info-email" id="chat-info-email"></div>
                     <div class="chat-info-status" id="chat-info-status"></div>
                     <div class="chat-info-branch" id="chat-info-branch"></div>
                 </div>
             </div>
             <div class="chat-info-actions flex flex-wrap gap-2 mt-4">
                 @php
                     $hasConversation = isset($conversations) && count($conversations) > 0;
                     $currentConversation = $hasConversation ? $conversations->first() : null;
                 @endphp
                 @if ($hasConversation && $currentConversation)
                     @if ($currentConversation->status === 'distributed')
                         <button id="btn-activate-conversation"
                             class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200 transition">
                             <i class="fas fa-play-circle text-blue-600"></i> Kích hoạt
                         </button>
                     @endif
                     @if ($currentConversation->status === 'active')
                         <button id="btn-resolve-conversation"
                             class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-green-700 bg-green-100 rounded-full hover:bg-green-200 transition">
                             <i class="fas fa-check-circle text-green-600"></i> Đã giải quyết
                         </button>
                         <button id="btn-close-conversation"
                             class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300 transition">
                             <i class="fas fa-times-circle text-gray-600"></i> Đóng
                         </button>
                     @endif
                     @if ($currentConversation->status === 'resolved')
                         <button id="btn-close-conversation"
                             class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-gray-700 bg-gray-200 rounded-full hover:bg-gray-300 transition">
                             <i class="fas fa-times-circle text-gray-600"></i> Đóng
                         </button>
                     @endif
                 @endif
             </div>

         </div>
     </div>
     <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
     <script src="/js/chat-realtime.js"></script>
     <script>
         document.addEventListener('DOMContentLoaded', function() {
             const userId = {{ $user->id }};
             let selectedConversationId = {{ $conversations->first()->id ?? 'null' }};
             let chatInstance = null;

             function getApiGetMessagesUrl(conversationId) {
                 return `/branch/chat/api/conversation/${conversationId}`;
             }

             // Khởi tạo thông tin ban đầu cho cuộc trò chuyện đầu tiên
             if (selectedConversationId && selectedConversationId !== 'null') {
                 const firstConversation = document.querySelector('.conversation-item');
                 if (firstConversation) {
                     const customerName = firstConversation.getAttribute('data-customer-name');
                     const customerEmail = firstConversation.getAttribute('data-customer-email');
                     const branchName = firstConversation.getAttribute('data-branch-name');
                     const status = firstConversation.getAttribute('data-status');

                     // Cập nhật header
                     document.getElementById('chat-header-name').textContent = customerName;
                     document.getElementById('chat-header-email').textContent = customerEmail;
                     document.getElementById('chat-header-avatar').textContent = customerName.charAt(0)
                         .toUpperCase();

                     // Cập nhật info panel
                     document.getElementById('chat-info-name').textContent = customerName;
                     document.getElementById('chat-info-email').textContent = customerEmail;
                     document.getElementById('chat-info-avatar').textContent = customerName.charAt(0).toUpperCase();
                     document.getElementById('chat-info-branch').textContent = `Chi nhánh: ${branchName}`;
                 }

                 chatInstance = new BranchChat({
                     conversationId: selectedConversationId,
                     userId: userId,
                     api: {
                         getMessages: getApiGetMessagesUrl(selectedConversationId),
                         send: '/branch/chat/send-message',
                         updateStatus: '/branch/chat/api/update-status',
                     },
                     messageInputSelector: '#chat-input-message',
                     sendButtonSelector: '#chat-send-btn',
                     fileInputSelector: '#chat-input-file',
                     imageInputSelector: '#chat-input-image',
                 });
             }

             // Xử lý sự kiện click vào cuộc trò chuyện
             document.querySelectorAll('.conversation-item').forEach(item => {
                 item.addEventListener('click', function() {
                     // Xóa active class từ tất cả các items
                     document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove(
                         'active'));
                     // Thêm active class cho item được click
                     this.classList.add('active');

                     const conversationId = this.getAttribute('data-conversation-id');
                     if (chatInstance) {
                         chatInstance.loadConversation(conversationId);
                     }
                 });
             });
         });
     </script>
 @endsection
