@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="max-w-7xl mx-auto py-6">
    <!-- Trạng thái đơn hàng -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
        <div class="flex items-center mb-6">
            <i class="fas fa-truck text-2xl text-gray-700 mr-2"></i>
            <span class="text-2xl font-bold">Trạng thái đơn hàng</span>
        </div>
        @php
            $stepMap = [
                'awaiting_confirmation' => 0,
                'confirmed' => 1,
                'awaiting_driver' => 2,
                'driver_confirmed' => 2,
                'waiting_driver_pick_up' => 2,
                'driver_picked_up' => 3,
                'in_transit' => 3,
                'delivered' => 4,
                'item_received' => 4,
                'cancelled' => 0,
                'refunded' => 0,
                'payment_failed' => 0,
                'payment_received' => 4,
                'order_failed' => 0,
            ];
            $steps = [
                ['label' => 'Xác nhận', 'icon' => 'fa-check', 'time' => $order->confirmed_at ?? '--:--'],
                ['label' => 'Chuẩn bị', 'icon' => 'fa-check', 'time' => $order->prepared_at ?? '--:--'],
                ['label' => 'Đang giao', 'icon' => 'fa-truck', 'time' => $order->delivering_at ?? '--:--'],
                ['label' => 'Hoàn thành', 'icon' => 'fa-check', 'time' => $order->delivered_at ?? '--:--'],
            ];
            $currentIdx = $stepMap[$order->status] ?? 0;
        @endphp
        <div class="flex items-center justify-between">
            @foreach($steps as $idx => $step)
                <div class="flex flex-col items-center flex-1">
                    <div class="rounded-full h-12 w-12 flex items-center justify-center mb-1
                        @if($idx < $currentIdx) bg-green-500 text-white @elseif($idx == $currentIdx) bg-blue-400 text-white @else bg-gray-200 text-gray-400 @endif
                        text-2xl border-2 border-white z-10">
                        <i class="fas {{ $step['icon'] }}"></i>
                    </div>
                    <div class="font-semibold text-sm @if($idx <= $currentIdx) text-black @else text-gray-400 @endif">{{ $step['label'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ $step['time'] ?? '--:--' }}</div>
                </div>
                @if($idx < count($steps) - 1)
                    <div class="flex-1 h-0.5 {{ $idx < $currentIdx ? 'bg-green-500' : 'bg-gray-200' }} mx-1 z-0"></div>
                @endif
            @endforeach
        </div>
    </div>
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Cột trái -->
        <div class="flex-1 flex flex-col gap-6">
            <!-- Thông tin chi nhánh -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 flex gap-4 items-center">
                <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                    <img src="{{ $order->branch?->logo_url ?? asset('images/default-avatar.png') }}" alt="Logo" class="w-12 h-12 object-cover rounded">
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-gray-800 text-base flex items-center gap-2">
                        <i class="fas fa-store text-gray-400"></i> {{ $order->branch?->name ?? '---' }}
                    </div>
                    <div class="text-sm text-gray-500">{{ $order->branch?->address ?? '' }}</div>
                    <div class="text-xs text-gray-400 flex items-center gap-2 mt-1">
                        <i class="fas fa-phone-alt"></i> {{ $order->branch?->phone ?? '---' }}
                        <span class="ml-3 flex items-center"><i class="fas fa-star text-yellow-400 mr-1"></i> 4.8 (2.1k đánh giá)</span>
                    </div>
                </div>
            </div>
            <!-- Món đã đặt -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-utensils text-gray-400"></i> Món đã đặt
                </div>
                <div class="divide-y">
                    @forelse($order->orderItems as $item)
                        <div class="flex items-center py-3 gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                                <img src="{{ $item->productVariant?->product?->primaryImage?->img ?? $item->product?->primaryImage?->img ?? asset('images/default-topping.svg') }}" alt="{{ $item->productVariant?->product?->name ?? $item->product?->name ?? $item->combo?->name ?? '' }}" class="w-10 h-10 object-cover rounded">
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-800">
                                    @if($item->productVariant && $item->productVariant->product)
                                        {{ $item->productVariant->product->name }}
                                    @elseif($item->product)
                                        {{ $item->product->name }}
                                    @elseif($item->combo)
                                        {{ $item->combo->name }}
                                    @else
                                        ---
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if($item->productVariant)
                                        Size: {{ $item->productVariant->size ?? '---' }}
                                    @endif
                                    @if($item->toppings && $item->toppings->count())
                                        <span class="ml-2">Topping:
                                            @foreach($item->toppings as $topping)
                                                {{ $topping->topping->name ?? '' }}@if(!$loop->last), @endif
                                            @endforeach
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400">Số lượng: {{ $item->quantity }}</div>
                            </div>
                            <div class="font-semibold text-gray-800 text-right min-w-[80px]">{{ number_format($item->total_price, 0, ',', '.') }}đ</div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-6">Không có món nào</div>
                    @endforelse
                </div>
            </div>
            <!-- Thông tin tài xế -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 flex gap-4 items-center">
                @if($order->driver)
                    <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center overflow-hidden">
                        <img src="{{ $order->driver?->avatar_url ?? asset('images/default-avatar.png') }}" alt="Avatar" class="w-12 h-12 object-cover rounded">
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-800 flex items-center gap-2">{{ $order->driver?->full_name ?? '---' }}
                            <span class="text-xs text-gray-400">4.9 (1.2k chuyến)</span>
                        </div>
                        <div class="text-xs text-gray-500">Biển số: {{ $order->driver?->license_plate ?? '---' }}</div>
                    </div>
                    <div class="flex flex-col gap-2 items-end">
                        <a href="tel:{{ $order->driver?->phone_number ?? '' }}" class="flex items-center gap-1 text-primary font-semibold hover:underline"><i class="fas fa-phone"></i> Gọi</a>
                        <a href="#" class="flex items-center gap-1 text-gray-500 font-semibold hover:underline"><i class="fas fa-comment-dots"></i> Nhắn tin</a>
                    </div>
                @else
                    <div class="w-16 h-16 bg-gray-100 rounded flex items-center justify-center">
                        <i class="fas fa-motorcycle text-3xl text-gray-400"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-500 flex items-center gap-2">Chưa có tài xế nhận đơn</div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Cột phải -->
        <div class="w-full lg:w-1/3 flex flex-col gap-6">
            <!-- Thông tin khách hàng -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2"><i class="fas fa-user text-gray-400"></i> Thông tin khách hàng</div>
                <div class="text-sm text-gray-800 font-semibold">{{ $order->customer?->name ?? '---' }}</div>
                <div class="text-sm text-gray-500 mb-2">{{ $order->customer?->phone ?? '---' }}</div>
                <div class="text-xs text-gray-500 mb-1">Địa chỉ giao hàng:</div>
                <div class="text-sm text-gray-800 mb-2">{{ $order->delivery_address ?? '---' }}</div>
                <div class="text-xs text-gray-500 mb-1">Ghi chú:</div>
                <div class="text-sm text-gray-800">{{ $order->notes ?? '---' }}</div>
            </div>
            <!-- Tóm tắt thanh toán -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2"><i class="fas fa-receipt text-gray-400"></i> Tóm tắt thanh toán</div>
                <div class="flex justify-between text-sm mb-1"><span>Tạm tính</span><span>{{ number_format($order->subtotal ?? $order->total_amount, 0, ',', '.') }}đ</span></div>
                <div class="flex justify-between text-sm mb-1"><span>Phí giao hàng</span><span>{{ number_format($order->delivery_fee ?? 0, 0, ',', '.') }}đ</span></div>
                <div class="flex justify-between text-sm mb-1"><span>Giảm giá</span><span class="text-green-600">-{{ number_format($order->discount_amount ?? 0, 0, ',', '.') }}đ</span></div>
                <div class="flex justify-between text-base font-bold border-t pt-2 mt-2"><span>Tổng cộng</span><span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span></div>
                <div class="mt-2 text-xs text-gray-500">Phương thức thanh toán:</div>
                <div class="flex items-center gap-2 mt-1">
                    <i class="fas fa-credit-card text-primary"></i>
                    <span>{{ $order->payment?->payment_method ? ucfirst($order->payment->payment_method) : '---' }} @if($order->payment?->card_last4)(****{{ $order->payment->card_last4 }})@endif</span>
                    @if($order->payment?->payment_status === 'success')
                        <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded ml-2">Đã thanh toán</span>
                    @else
                        <span class="inline-block bg-gray-200 text-gray-600 text-xs font-semibold px-2 py-1 rounded ml-2">Chưa thanh toán</span>
                    @endif
                </div>
            </div>
            <!-- Thời gian giao hàng -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="font-semibold text-gray-800 mb-2 flex items-center gap-2"><i class="fas fa-clock text-gray-400"></i> Thời gian giao hàng</div>
                <div class="text-2xl text-primary font-bold mb-1">15-20 phút</div>
                <div class="text-xs text-gray-500">Dự kiến giao lúc {{ $order->estimated_delivery_time ? $order->estimated_delivery_time->format('H:i') : '--:--' }}</div>
            </div>
            <!-- Nút thao tác -->
            <div class="flex flex-col gap-2 mt-2 items-center">
                <a href="#" class="block w-full text-center bg-white border border-gray-300 rounded-lg py-2 font-semibold hover:bg-gray-100 transition">Theo dõi đơn hàng</a>
                <a href="#" class="block w-full text-center bg-blue-50 border border-blue-500 text-blue-600 rounded-lg py-2 font-semibold hover:bg-blue-100 transition">Liên hệ hỗ trợ</a>
                <form method="POST" action="{{ route('admin.orders.cancel', $order->id) }}" class="w-full">
                    @csrf
                    <button type="submit" class="block w-full text-center bg-red-500 text-white rounded-lg py-2 font-semibold hover:bg-red-600 transition">Hủy đơn hàng</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 