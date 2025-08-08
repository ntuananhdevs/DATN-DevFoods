@php
use Illuminate\Support\Facades\Storage;
@endphp

@extends('layouts.branch.contentLayoutMaster')

@section('title', 'Quản lý Combo')
@section('description', 'Quản lý danh sách combo của bạn')

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
                        class="lucide lucide-package">
                        <path d="m7.5 4.27 9 5.15"></path>
                        <path
                            d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                        </path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý Combo</h2>
                    <p class="text-muted-foreground">Quản lý danh sách combo của bạn</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a class="btn btn-primary flex items-center">
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
                <h3 class="text-lg font-medium">Danh sách combo</h3>
            </div>
            <div class="overflow-x-auto">
                @forelse($combos as $combo)
                    @if ($loop->first)
                        <table class="w-full">
                            <thead class="border-b">
                                <tr class="text-left">
                                    <th class="p-4 font-medium text-muted-foreground">SKU</th>
                                    <th class="p-4 font-medium text-muted-foreground">Hình ảnh</th>
                                    <th class="p-4 font-medium text-muted-foreground">Tên combo</th>
                                    <th class="p-4 font-medium text-muted-foreground">Giá</th>
                                    <th class="p-4 font-medium text-muted-foreground">Trạng thái</th>
                                    <th class="p-4 font-medium text-muted-foreground">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                    <tr class="border-b hover:bg-muted/50 transition-colors" data-combo-id="{{ $combo->id }}">
                        <td class="p-4 font-mono text-sm text-muted-foreground">{{ $combo->sku }}</td>
                        <td class="p-4">
                            @if ($combo->image)
                                <img src="{{ Storage::disk('s3')->url($combo->image) }}" alt="{{ $combo->name }}"
                                    class="w-12 h-12 rounded-lg object-cover border">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-muted flex items-center justify-center border">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                        <rect width="18" height="18" x="3" y="3" rx="2" ry="2">
                                        </rect>
                                        <circle cx="9" cy="9" r="2"></circle>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-medium">{{ $combo->name }}</div>
                            @if ($combo->description)
                                <div class="text-sm text-muted-foreground mt-1">{{ Str::limit($combo->description, 40) }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="font-medium text-green-600">{{ number_format($combo->price) }}đ</div>
                        </td>
                        <td class="p-4">
                            <span class="status-tag {{ $combo->active ? 'success' : 'failed' }}">
                                {{ $combo->active ? 'Hoạt động' : 'Không hoạt động' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('branch.combos.show', $combo->slug) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground transition-colors"
                                    title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a class="inline-flex items-center justify-center w-8 h-8 rounded-md border border-input bg-background hover:bg-accent hover:text-accent-foreground transition-colors"
                                    title="Chỉnh sửa">
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
                    <div class="p-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="mx-auto text-muted-foreground mb-4">
                            <path
                                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                            </path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <h3 class="text-lg font-medium mb-2">Không có combo nào</h3>
                        <p class="text-muted-foreground mb-4">Hãy tạo combo đầu tiên cho cửa hàng của bạn.</p>
                        <a href="{{ route('branch.combos.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <path d="M5 12h14"></path>
                                <path d="M12 5v14"></path>
                            </svg>
                            Thêm combo
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
