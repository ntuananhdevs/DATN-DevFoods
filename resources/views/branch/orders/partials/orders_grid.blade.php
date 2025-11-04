<div id="ordersGrid" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="selectAll" class="rounded">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã đơn hàng</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng tiền</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thanh toán</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    @include('branch.orders.partials.order_card', ['order' => $order])
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8">
                            <div class="text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <h3 class="text-lg font-medium mb-2 text-gray-900">Không có đơn hàng</h3>
                                <p class="text-gray-500">Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div id="ordersPagination">
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @endif
</div>