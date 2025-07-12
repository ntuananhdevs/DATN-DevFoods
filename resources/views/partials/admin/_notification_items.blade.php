@foreach($notifications ?? [] as $notification)
    @php
        $orderId = $notification->data['order_id'] ?? null;
        $redirectUrl = $orderId ? route('admin.orders.index', ['order' => $orderId]) : '';
    @endphp
    <div class="flex items-start gap-3 px-2 py-2 hover:bg-accent rounded-md {{ $notification->read_at ? '' : 'bg-primary/10 text-primary font-semibold' }}"
         style="cursor:pointer;"
         onclick="markAdminNotificationAsRead('{{ $notification->id }}', '{{ $redirectUrl }}')">
        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium truncate">{{ $notification->data['message'] ?? '' }}</p>
            @if(isset($notification->data['branch_name']))
                <p class="text-xs text-muted-foreground truncate">Chi nhánh: {{ $notification->data['branch_name'] }}</p>
            @endif
            @if(isset($notification->data['customer_name']))
                <p class="text-xs text-muted-foreground truncate">Khách hàng: {{ $notification->data['customer_name'] }}</p>
            @endif
            <p class="text-xs text-muted-foreground">{{ $notification->created_at->diffForHumans() }}</p>
        </div>
        @if(!$notification->read_at)
            <div class="w-2 h-2 rounded-full bg-primary mt-2"></div>
        @endif
    </div>
@endforeach
@if(empty($notifications))
    <div class="text-center text-xs text-muted-foreground py-4">Không có thông báo nào</div>
@endif 