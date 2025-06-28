@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản lý Đơn hàng')
@section('description', 'Quản lý danh sách đơn hàng của bạn')

@section('content')
    <div class="fade-in flex flex-col gap-4 pb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1" />
                        <circle cx="20" cy="21" r="1" />
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý Đơn hàng</h2>
                    <p class="text-muted-foreground">Đơn hàng của chi nhánh: {{ $branch->name ?? 'N/A' }}</p>
                </div>
            </div>
            {{-- Xóa nút "Thêm mới" vì đơn hàng được tạo từ phía khách hàng --}}
        </div>
    </div>

    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách đơn hàng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50">
                    <tr class="text-left">
                        <th class="p-4 font-medium text-muted-foreground">Mã đơn</th>
                        <th class="p-4 font-medium text-muted-foreground">Khách hàng</th>
                        <th class="p-4 font-medium text-muted-foreground">Tổng tiền</th>
                        <th class="p-4 font-medium text-muted-foreground">Ngày đặt</th>
                        <th class="p-4 font-medium text-muted-foreground text-center">Trạng thái</th>
                        <th class="p-4 font-medium text-muted-foreground text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        {{-- Thay đổi: Thêm class để highlight các đơn hàng mới cần xử lý --}}
                        <tr class="border-b hover:bg-muted/50 transition-colors {{ $order->status == 'awaiting_confirmation' ? 'bg-orange-50' : '' }}">
                            <td class="p-4 font-semibold text-primary">#{{ $order->order_code ?? $order->id }}</td>
                            <td class="p-4">
                                {{-- Bổ sung: Hiển thị cả tên và SĐT khách hàng --}}
                                <div class="font-medium">{{ $order->customerName }}</div>
                                <div class="text-xs text-muted-foreground">{{ $order->customerPhone }}</div>
                            </td>
                            <td class="p-4 text-green-600 font-semibold">{{ number_format($order->total_amount) }} VNĐ</td>
                            <td class="p-4">{{ $order->order_date ? $order->order_date->format('H:i - d/m/Y') : '' }}</td>
                            <td class="p-4 text-center">
                                {{-- Thay đổi: Sử dụng Accessor từ Model để hiển thị trạng thái động và nhất quán --}}
                                <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background-color: {{ $order->status_color['bg'] }}; color: {{ $order->status_color['text'] }};">
                                    {{ $order->status_text }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Thay đổi: Logic hiển thị nút bấm tùy theo trạng thái đơn hàng --}}
                                    @if($order->status == 'awaiting_confirmation')
                                        {{-- Nút Xác nhận đơn hàng --}}
                                        <form action="{{ route('branch.orders.updateStatus', $order) }}" method="POST" class="form-confirm">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-sm bg-green-500 text-white hover:bg-green-600" title="Xác nhận đơn hàng">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                <span>Xác nhận</span>
                                            </button>
                                        </form>
                                        {{-- Nút Từ chối/Hủy đơn hàng --}}
                                        <form action="{{ route('branch.orders.updateStatus', $order) }}" method="POST" class="form-cancel">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled_by_branch">
                                            <button type="submit" class="btn btn-sm bg-red-500 text-white hover:bg-red-600" title="Từ chối đơn hàng">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Nút Xem chi tiết cho các đơn đã xử lý --}}
                                        <a href="#" class="btn btn-outline btn-sm" title="Xem chi tiết">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Không có đơn hàng nào cho chi nhánh này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Bổ sung: Thêm hộp thoại xác nhận để tránh bấm nhầm
    document.addEventListener('DOMContentLoaded', function() {
        const confirmForms = document.querySelectorAll('.form-confirm');
        const cancelForms = document.querySelectorAll('.form-cancel');

        confirmForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Bạn có chắc chắn muốn XÁC NHẬN đơn hàng này không?')) {
                    e.preventDefault();
                }
            });
        });

        cancelForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Bạn có chắc chắn muốn TỪ CHỐI đơn hàng này không? Hành động này không thể hoàn tác.')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush