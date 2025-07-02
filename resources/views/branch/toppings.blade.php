@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý Topping')
@section('description', 'Quản lý danh sách topping của bạn')

@section('content')

    <style>
        /* ... giữ nguyên phần style như file admin ... */
    </style>

    <div class="fade-in flex flex-col gap-4 pb-4">
        <!-- Main Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-plus-circle">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 12h8"></path>
                        <path d="M12 8v8"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý Topping</h2>
                    <p class="text-muted-foreground">Quản lý danh sách topping của bạn</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a class="btn btn-primary flex items-center mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mr-2">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    Thêm mới
                </a>
            </div>
        </div>
        <!-- Card containing table -->
        <div class="card border rounded-lg overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium">Danh sách topping</h3>
            </div>
            <div class="overflow-x-auto">
                @forelse($toppings as $topping)
                    @if ($loop->first)
                        <table class="w-full">
                            <thead class="border-b">
                                <tr class="text-left">
                                    <th class="p-4 font-medium text-muted-foreground">Mã topping</th>
                                    <th class="p-4 font-medium text-muted-foreground">Hình ảnh</th>
                                    <th class="p-4 font-medium text-muted-foreground">Tên topping</th>
                                    <th class="p-4 font-medium text-muted-foreground">Giá</th>
                                    <th class="p-4 font-medium text-muted-foreground">Mô tả</th>
                                    <th class="p-4 font-medium text-muted-foreground">Trạng thái</th>
                                    <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                    <tr class="border-b hover:bg-muted/50 transition-colors">
                        <td class="p-4">
                            <div class="font-mono text-sm">{{ $topping->sku ?? 'N/A' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center">
                                @if ($topping->image)
                                    <img src="{{ Storage::disk('s3')->url($topping->image) }}" alt="{{ $topping->name }}"
                                        class="w-20 h-20 rounded-lg object-cover border">
                                @else
                                    <div class="w-20 h-20 rounded-lg bg-muted flex items-center justify-center border">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2">
                                            </rect>
                                            <circle cx="9" cy="9" r="2"></circle>
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="font-medium">{{ $topping->name }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-medium text-green-600">{{ number_format($topping->price) }}đ</div>
                        </td>
                        <td class="p-4">
                            <div class="text-sm text-muted-foreground max-w-xs truncate">
                                {{ $topping->description ?? 'Không có mô tả' }}</div>
                        </td>
                        <td class="p-4">
                            @if ($topping->active == 1)
                                <span class="status-tag success">Hoạt động</span>
                            @else
                                <span class="status-tag failed">Không hoạt động</span>
                            @endif
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
                                <a class="btn btn-outline btn-sm" title="Quản lý tồn kho">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                                        </path>
                                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                                        <path d="M12 22V12"></path>
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
                    @if ($loop->last)
                        </tbody>
                        </table>
                    @endif
                @empty
                    <div class="flex flex-col items-center justify-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="text-muted-foreground mb-4">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 12h8"></path>
                            <path d="M12 8v8"></path>
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Chưa có topping nào</h3>
                        <p class="text-muted-foreground mb-4 text-center">Bắt đầu bằng cách tạo topping đầu tiên cho cửa
                            hàng của bạn.</p>
                        <a href="{{ route('branch.toppings.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Thêm topping đầu tiên
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
