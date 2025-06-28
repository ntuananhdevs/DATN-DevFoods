@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý Đơn hàng')
@section('description', 'Quản lý danh sách đơn hàng của bạn')

@section('content')
    <div class="fade-in flex flex-col gap-4 pb-4">
        <!-- Main Header -->
        <div class="flex items-center justify-between">

            <div class="flex items-center gap-3">
                <div
                    class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <a class="bg-primary text-white px-4 py-2 rounded hover:bg-primary/90">+ Thêm mới</a>
        </div>



    </div>

    <!-- Card containing table -->
    <div class="card border rounded-lg overflow-hidden">
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách đơn hàng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b">
                    <tr class="text-left">
                        <th class="p-4 font-medium text-muted-foreground">Mã đơn</th>
                        <th class="p-4 font-medium text-muted-foreground">Khách hàng</th>
                        <th class="p-4 font-medium text-muted-foreground">Tổng tiền</th>
                        <th class="p-4 font-medium text-muted-foreground">Ngày đặt</th>
                        <th class="p-4 font-medium text-muted-foreground">Trạng thái</th>
                        <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="border-b hover:bg-muted/50 transition-colors">
                            <td class="p-4 font-semibold">#{{ $order->id }}</td>
                            <td class="p-4">{{ $order->customer->full_name ?? 'Khách lẻ' }}</td>
                            <td class="p-4 text-green-600 font-semibold">{{ number_format($order->total_amount) }} VNĐ
                            </td>
                            <td class="p-4">{{ $order->order_date ? $order->order_date->format('d/m/Y H:i') : '' }}
                            </td>
                            <td class="p-4">
                                @php
                                    $statusColor = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'active' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'delivering' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $status = $order->status ?? 'pending';
                                @endphp
                                <span class="status-tag {{ $statusColor[$status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <a class="btn btn-outline btn-sm" title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>

                                    <a class="btn btn-outline btn-sm" title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                                            <path d="m15 5 4 4"></path>
                                        </svg>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Không có đơn hàng nào cho chi nhánh
                                này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
@endsection
