@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Chi tiết đơn hàng <span class="text-primary">#{{ $order->order_code ?? $order->id }}</span></h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">← Quay lại danh sách</a>
    </div>
    
    <!-- Progress Steps -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Tiến trình đơn hàng</h3>
        <div class="relative">
            @php
                // Map trạng thái thực tế về 5 bước chính
                $stepMap = [
                    'awaiting_confirmation' => 0, // Chờ xác nhận
                    'confirmed' => 1,            // Đã xác nhận
                    'awaiting_driver' => 2,      // Chờ tài xế
                    'driver_confirmed' => 2,
                    'waiting_driver_pick_up' => 2,
                    'driver_picked_up' => 3,     // Đang giao
                    'in_transit' => 3,
                    'delivered' => 4,            // Hoàn thành
                    'item_received' => 4,
                    'cancelled' => 0,            // Nếu hủy thì dừng ở bước 1
                    'refunded' => 0,
                    'payment_failed' => 0,
                    'payment_received' => 4,
                    'order_failed' => 0,
                ];
                $steps = [
                    ['label' => 'Chờ xác nhận', 'icon' => 'fa-hourglass-half'],
                    ['label' => 'Đã xác nhận', 'icon' => 'fa-check'],
                    ['label' => 'Chờ tài xế', 'icon' => 'fa-user-clock'],
                    ['label' => 'Đang giao', 'icon' => 'fa-truck'],
                    ['label' => 'Hoàn thành', 'icon' => 'fa-flag-checkered'],
                ];
                $currentIdx = $stepMap[$order->status] ?? 0;
            @endphp
            
            <!-- Progress Bar -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex-1 relative">
                    <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                        <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary transition-all duration-500" style="width: {{ ($currentIdx + 1) * 25 }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Steps -->
            <div class="flex justify-between">
                @foreach($steps as $idx => $step)
                    <div class="flex flex-col items-center">
                        <div class="rounded-full transition duration-500 ease-in-out h-12 w-12 py-3 border-2 flex items-center justify-center
                                    {{ $idx <= $currentIdx ? 'bg-primary border-primary text-white' : 'bg-white border-gray-300 text-gray-500' }}">
                            <i class="fas {{ $step['icon'] }}"></i>
                        </div>
                        <div class="text-xs mt-2 text-center {{ $idx <= $currentIdx ? 'text-primary font-semibold' : 'text-gray-500' }}">
                            {{ $step['label'] }}
                        </div>
                        @if($idx === $currentIdx)
                            <div class="text-xs text-primary font-bold mt-1">(Hiện tại)</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Order Details Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Thông tin đơn hàng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50 w-1/4">Mã đơn hàng</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->order_code ?? $order->id }}</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Trạng thái</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="badge badge-pill {{ $order->status === 'delivered' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }} px-3 py-1">
                                <i class="fas fa-info-circle mr-1"></i> {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Tổng tiền</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="text-primary font-semibold">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Ngày đặt</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->order_date }}</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Khách hàng</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-user text-blue-500 mr-2"></i>
                                {{ $order->customer->name ?? 'N/A' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Số điện thoại</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-green-500 mr-2"></i>
                                {{ $order->customer->phone ?? '---' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Chi nhánh</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <i class="fas fa-store text-indigo-500 mr-2"></i>
                                {{ $order->branch->name ?? 'N/A' }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Tài xế</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($order->driver)
                                <div class="flex items-center">
                                    <img src="{{ $order->driver->avatar_url ?? '/images/default-avatar.png' }}" alt="Avatar" class="w-8 h-8 rounded-full mr-2">
                                    <div>
                                        <div class="font-medium">{{ $order->driver->name }}</div>
                                        <div class="text-xs text-gray-500">SĐT: {{ $order->driver->phone }}</div>
                                        <div class="text-xs text-gray-500">Biển số: {{ $order->driver->license_plate ?? '---' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-motorcycle mr-2"></i>
                                    Chưa có tài xế nhận đơn
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-gray-50">Ghi chú</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="flex items-start">
                                <i class="fas fa-sticky-note text-yellow-500 mr-2 mt-1"></i>
                                {{ $order->notes ?? '---' }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-8">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
    </div>
</div>
@endsection 