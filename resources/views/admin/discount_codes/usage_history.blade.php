@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Lịch sử sử dụng mã giảm giá')
@section('description', 'Theo dõi và quản lý lịch sử sử dụng mã giảm giá')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('vendors/css/forms/select/select2.min.css') }}">
@endsection

@section('styles')
<style>
    .stats-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .stats-gradient-green {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stats-gradient-red {
        background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    }
    .stats-gradient-blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .export-gradient {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .detail-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .detail-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .detail-content {
        padding: 1.5rem;
    }
    
    /* Dark mode styles */
    .dark .detail-card {
        background: hsl(var(--card));
        border-color: hsl(var(--border));
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
    }
    
    .dark .detail-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }
    
    .dark .detail-content {
        color: hsl(var(--foreground));
    }
    
    .dark .bg-white {
        background-color: hsl(var(--card));
    }
    
    .dark .bg-gray-50 {
        background-color: hsl(var(--muted));
    }
    
    .dark .text-gray-700 {
        color: hsl(var(--foreground));
    }
    
    .dark .text-gray-500 {
        color: hsl(var(--muted-foreground));
    }
    
    .dark .text-gray-900 {
        color: hsl(var(--foreground));
    }
    
    .dark .hover\:bg-gray-100:hover {
        background-color: hsl(var(--accent));
    }
    
    .dark .hover\:bg-gray-50:hover {
        background-color: hsl(var(--accent));
    }
    
    .dark .divide-gray-200 {
        border-color: hsl(var(--border));
    }
    
    .dark .border-gray-300 {
        border-color: hsl(var(--border));
    }
    
    .dark .ring-black {
        --tw-ring-color: hsl(var(--border));
    }
</style>
@endsection

@section('content')
<div class="fade-in flex flex-col gap-4 pb-4 p-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history">
                    <path d="M3 3v5h5"></path>
                    <path d="M3 8a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 4"></path>
                    <path d="M21 21v-5h-5"></path>
                    <path d="M21 16a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 20"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Lịch sử sử dụng mã giảm giá</h2>
                <p class="text-muted-foreground">Theo dõi lịch sử sử dụng mã: {{ $discountCode->code }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.discount_codes.show', $discountCode->id) }}" class="btn btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"></path>
                </svg>
                Xem chi tiết mã
            </a>
            <a href="{{ route('admin.discount_codes.index') }}" class="btn btn-outline flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="m12 19-7-7 7-7"></path>
                    <path d="M19 12H5"></path>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stats-gradient rounded-xl p-6 text-white transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 dark:bg-gray-800 dark:bg-opacity-30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-white text-opacity-80">Tổng lượt sử dụng</p>
                    <p class="text-2xl font-bold">{{ number_format($totalUsage ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="stats-gradient-red rounded-xl p-6 text-white transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 dark:bg-gray-800 dark:bg-opacity-30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-white text-opacity-80">Tổng tiền giảm</p>
                    <p class="text-2xl font-bold">{{ number_format($totalDiscount ?? 0) }} VND</p>
                </div>
            </div>
        </div>

        <div class="stats-gradient-green rounded-xl p-6 text-white transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 dark:bg-gray-800 dark:bg-opacity-30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-white text-opacity-80">Khách hàng sử dụng</p>
                    <p class="text-2xl font-bold">{{ number_format($uniqueUsers ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="stats-gradient-blue rounded-xl p-6 text-white transform hover:-translate-y-1 transition-transform duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white bg-opacity-20 dark:bg-gray-800 dark:bg-opacity-30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-white text-opacity-80">Giá trị TB/lần</p>
                    <p class="text-2xl font-bold">{{ number_format($avgDiscount ?? 0) }} VND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="detail-card mb-8">
        <div class="detail-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            <h2 class="text-xl font-semibold">Bộ lọc dữ liệu</h2>
        </div>
        <div class="detail-content">
            <form method="GET" action="">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mã giảm giá</label>
                        <select class="select2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" name="discount_code_id">
                            <option value="">Tất cả mã</option>
                            @foreach($discountCodes ?? [] as $code)
                                <option value="{{ $code->id }}" {{ request('discount_code_id') == $code->id ? 'selected' : '' }}>
                                    {{ $code->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Chi nhánh</label>
                        <select class="select2 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" name="branch_id">
                            <option value="">Tất cả chi nhánh</option>
                            @foreach($branches ?? [] as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Từ ngày</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" name="from_date" value="{{ request('from_date') }}">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Đến ngày</label>
                        <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" name="to_date" value="{{ request('to_date') }}">
                    </div>
                    <div class="lg:col-span-2 flex items-end space-x-2">
                        <button type="submit" class="btn btn-primary flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Lọc
                        </button>
                        <a href="" class="btn btn-outline flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Usage History Table -->
    <div class="detail-card">
        <div class="detail-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 3v5h5"></path>
                <path d="M3 8a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 4"></path>
                <path d="M21 21v-5h-5"></path>
                <path d="M21 16a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 20"></path>
            </svg>
            <h2 class="text-xl font-semibold">Lịch sử sử dụng mã giảm giá</h2>
        </div>
        <div class="detail-content">
            <div class="flex justify-end mb-4">
                <button onclick="exportData('excel')" class="export-gradient text-white px-3 py-2 rounded-md text-sm font-medium hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 mr-2 dark:shadow-gray-900">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </button>
                <button onclick="exportData('pdf')" class="export-gradient text-white px-3 py-2 rounded-md text-sm font-medium hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 mr-2 dark:shadow-gray-900">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </button>
                <button onclick="exportData('csv')" class="export-gradient text-white px-3 py-2 rounded-md text-sm font-medium hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 dark:shadow-gray-900">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    CSV
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mã giảm giá</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Người sử dụng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Chi nhánh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Số tiền gốc</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Số tiền giảm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tiết kiệm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thời gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($usageHistory as $index => $history)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    {{ $usageHistory->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $history->discountCode->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9">
                                            <div class="h-9 w-9 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                <svg class="h-4 w-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                                {{ $history->user?->name ?? 'Khách vãng lai' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $history->user?->email ?? $history->guest_phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200">
                                        {{ $history->branch->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                                    {{ number_format($history->original_amount) }} VND
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400">
                                    -{{ number_format($history->discount_amount) }} VND
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $savePercent = $history->original_amount > 0 ? ($history->discount_amount / $history->original_amount) * 100 : 0;
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ number_format($savePercent, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    <div>
                                        <div class="font-medium">{{ $history->used_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history->used_at->format('H:i:s') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click="open = !open" type="button" class="inline-flex items-center p-1 border border-transparent rounded-full text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="1"></circle>
                                                <circle cx="12" cy="5" r="1"></circle>
                                                <circle cx="12" cy="19" r="1"></circle>
                                            </svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black dark:ring-gray-700 ring-opacity-5 focus:outline-none z-10">
                                            <div class="py-1">
                                                <a href="{{ route('admin.discount_codes.show', $history->discount_code_id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                                        <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                                        <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"></path>
                                                    </svg>
                                                    Xem mã giảm giá
                                                </a>
                                                @if($history->user)
                                                    <a href="{{ route('admin.users.show', $history->user_id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                                            <circle cx="12" cy="7" r="4"></circle>
                                                        </svg>
                                                        Xem khách hàng
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.branches.show', $history->branch_id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                                                        <circle cx="12" cy="10" r="3"></circle>
                                                    </svg>
                                                    Xem chi nhánh
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Không có dữ liệu</h3>
                                        <p class="text-gray-500 dark:text-gray-400">Chưa có lịch sử sử dụng mã giảm giá nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($usageHistory->hasPages())
                <div class="mt-4">
                    {{ $usageHistory->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Chọn...',
        allowClear: true
    });
    
    // Apply dark mode to Select2 if needed
    if (document.documentElement.classList.contains('dark')) {
        applySelect2DarkMode();
    }
    
    // Listen for theme changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                if (document.documentElement.classList.contains('dark')) {
                    applySelect2DarkMode();
                } else {
                    removeSelect2DarkMode();
                }
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// Apply dark mode to Select2
function applySelect2DarkMode() {
    // Add dark styles to Select2 dropdowns
    if (!document.getElementById('select2-dark-styles')) {
        const style = document.createElement('style');
        style.id = 'select2-dark-styles';
        style.innerHTML = `
            .select2-container--default .select2-selection--single,
            .select2-container--default .select2-selection--multiple {
                background-color: hsl(var(--card)) !important;
                border-color: hsl(var(--border)) !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered {
                color: hsl(var(--foreground)) !important;
            }
            .select2-dropdown {
                background-color: hsl(var(--card)) !important;
                border-color: hsl(var(--border)) !important;
            }
            .select2-search__field {
                background-color: hsl(var(--input)) !important;
                color: hsl(var(--foreground)) !important;
                border-color: hsl(var(--border)) !important;
            }
            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: hsl(var(--accent)) !important;
            }
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: hsl(var(--primary)) !important;
                color: hsl(var(--primary-foreground)) !important;
            }
            .select2-container--default .select2-selection__choice {
                background-color: hsl(var(--accent)) !important;
                color: hsl(var(--accent-foreground)) !important;
                border-color: hsl(var(--border)) !important;
            }
        `;
        document.head.appendChild(style);
    }
}

// Remove dark mode from Select2
function removeSelect2DarkMode() {
    const style = document.getElementById('select2-dark-styles');
    if (style) {
        style.remove();
    }
}

// Export functions
function exportData(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    
    const url = `${window.location.pathname}?${params.toString()}`;
    window.open(url, '_blank');
}

// Auto-refresh every 30 seconds
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 30000);
</script>
@endsection
