<div id="ordersGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @forelse($orders as $order)
        @include('branch.orders.partials.order_card', ['order' => $order])
    @empty
        <div class="col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-8">
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium mb-2 text-gray-900">Không có đơn hàng</h3>
                    <p class="text-gray-500">Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
<div id="ordersPagination">
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div> 