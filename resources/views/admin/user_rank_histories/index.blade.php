@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Lịch sử thăng hạng thành viên')
@section('description', 'Quản lý lịch sử thăng hạng của thành viên trong hệ thống')

@section('content')
<style>
    /* Dark mode variables */
    :root {
        --background: 0 0% 100%;
        --foreground: 222.2 84% 4.9%;
        --card: 0 0% 100%;
        --card-foreground: 222.2 84% 4.9%;
        --popover: 0 0% 100%;
        --popover-foreground: 222.2 84% 4.9%;
        --primary: 221.2 83.2% 53.3%;
        --primary-foreground: 210 40% 98%;
        --secondary: 210 40% 96%;
        --secondary-foreground: 222.2 84% 4.9%;
        --muted: 210 40% 96%;
        --muted-foreground: 215.4 16.3% 46.9%;
        --accent: 210 40% 96%;
        --accent-foreground: 222.2 84% 4.9%;
        --destructive: 0 84.2% 60.2%;
        --destructive-foreground: 210 40% 98%;
        --border: 214.3 31.8% 91.4%;
        --input: 214.3 31.8% 91.4%;
        --ring: 221.2 83.2% 53.3%;
        --radius: 0.5rem;
    }

    .dark {
        --background: 222.2 84% 4.9%;
        --foreground: 210 40% 98%;
        --card: 222.2 84% 4.9%;
        --card-foreground: 210 40% 98%;
        --popover: 222.2 84% 4.9%;
        --popover-foreground: 210 40% 98%;
        --primary: 217.2 91.2% 59.8%;
        --primary-foreground: 222.2 84% 4.9%;
        --secondary: 217.2 32.6% 17.5%;
        --secondary-foreground: 210 40% 98%;
        --muted: 217.2 32.6% 17.5%;
        --muted-foreground: 215 20.2% 65.1%;
        --accent: 217.2 32.6% 17.5%;
        --accent-foreground: 210 40% 98%;
        --destructive: 0 62.8% 30.6%;
        --destructive-foreground: 210 40% 98%;
        --border: 217.2 32.6% 17.5%;
        --input: 217.2 32.6% 17.5%;
        --ring: 224.3 76.3% 94.1%;
    }

    * {
        border-color: hsl(var(--border));
    }

    body {
        background-color: hsl(var(--background));
        color: hsl(var(--foreground));
    }

    /* Theme toggle button */
    .theme-toggle {
        position: relative;
        width: 44px;
        height: 24px;
        background-color: hsl(var(--muted));
        border-radius: 12px;
        transition: background-color 0.3s ease;
        cursor: pointer;
        border: 1px solid hsl(var(--border));
    }

    .theme-toggle.dark {
        background-color: hsl(var(--primary));
    }

    .theme-toggle-handle {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 18px;
        height: 18px;
        background-color: hsl(var(--background));
        border-radius: 50%;
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
    }

    .theme-toggle.dark .theme-toggle-handle {
        transform: translateX(20px);
    }

    .filter-section {
        background-color: hsl(var(--card));
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        border: 1px solid hsl(var(--border));
    }

    .filter-section:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card {
        background-color: hsl(var(--card));
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        border: 1px solid hsl(var(--border));
    }
    
    .stats-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .rank-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }
    
    .bg-rank-default {
        background-color: #888;
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-8">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-history">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                    <path d="M3 3v5h5"></path>
                    <path d="M12 7v5l4 2"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Lịch sử thăng hạng thành viên</h2>
                <p class="text-muted-foreground">Xem lịch sử thay đổi hạng của thành viên</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <!-- Theme Toggle -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Theme:</span>
                <button id="themeToggle" class="theme-toggle">
                    <div class="theme-toggle-handle">
                        <span id="themeIcon">🌙</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stats-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-muted-foreground">Tổng số thành viên</p>
                    <h3 class="text-2xl font-bold">{{ number_format($totalUsers) }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center dark:bg-blue-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-muted-foreground">Thăng hạng (30 ngày)</p>
                    <h3 class="text-2xl font-bold">{{ number_format($recentUpgrades) }}</h3>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center dark:bg-green-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-muted-foreground">Hạng cao nhất</p>
                    <h3 class="text-2xl font-bold">{{ $topRank ? $topRank->name : 'N/A' }}</h3>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center dark:bg-purple-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-500">
                        <path d="M12 2 L15.09 8.26 L22 9.27 L17 14.14 L18.18 21.02 L12 17.77 L5.82 21.02 L7 14.14 L2 9.27 L8.91 8.26 L12 2" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="stats-card p-4">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-muted-foreground">Thành viên hạng cao</p>
                    <h3 class="text-2xl font-bold">{{ number_format($usersInTopRank) }}</h3>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center dark:bg-amber-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-500">
                        <path d="M20 6 L9 17 L4 12" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                Bộ lọc tìm kiếm
            </h3>
            <a href="{{ route('admin.user_rank_history.index') }}" class="text-sm text-muted-foreground hover:text-primary flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 12a9 9 0 1 0 18 0 9 9 0 0 0-18 0z"></path>
                    <path d="m13 13-3-3"></path>
                    <path d="M7 17 3.5 20.5"></path>
                    <path d="M17 17l3.5 3.5"></path>
                    <path d="M3.5 3.5 7 7"></path>
                    <path d="M14 10h.01"></path>
                </svg>
                Đặt lại bộ lọc
            </a>
        </div>
        
        <form action="{{ route('admin.user_rank_history.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="space-y-2">
                    <label class="text-sm font-medium flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                            <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7z"></path>
                            <path d="M4 20h16"></path>
                        </svg>
                        Hạng thành viên
                    </label>
                    <div class="relative">
                        <select name="rank_id" class="w-full rounded-md border-gray-300 pl-10 pr-8 py-2 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-background text-foreground appearance-none">
                            <option value="">Tất cả các hạng</option>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank->id }}" {{ request('rank_id') == $rank->id ? 'selected' : '' }}>
                                    {{ $rank->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M12 17.8 5.8 9.4A5 5 0 0 1 10.1 2h3.8a5 5 0 0 1 4.3 7.4L12 17.8Z"></path>
                                <path d="m12 17.8-6.2 4.1 2.4-7.8"></path>
                                <path d="m12 17.8 6.2 4.1-2.4-7.8"></path>
                            </svg>
                        </div>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                        </svg>
                        ID người dùng
                    </label>
                    <div class="relative">
                        <input type="text" name="user_id" value="{{ request('user_id') }}" placeholder="Nhập ID người dùng" 
                               class="w-full rounded-md border-gray-300 pl-10 py-2 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-background text-foreground">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                            <path d="M8 14h.01"></path>
                            <path d="M12 14h.01"></path>
                            <path d="M16 14h.01"></path>
                            <path d="M8 18h.01"></path>
                            <path d="M12 18h.01"></path>
                            <path d="M16 18h.01"></path>
                        </svg>
                        Từ ngày
                    </label>
                    <div class="relative">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="w-full rounded-md border-gray-300 pl-10 py-2 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-background text-foreground">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                            <line x1="16" x2="16" y1="2" y2="6"></line>
                            <line x1="8" x2="8" y1="2" y2="6"></line>
                            <line x1="3" x2="21" y1="10" y2="10"></line>
                            <path d="M8 14h.01"></path>
                            <path d="M12 14h.01"></path>
                            <path d="M16 14h.01"></path>
                            <path d="M8 18h.01"></path>
                            <path d="M12 18h.01"></path>
                            <path d="M16 18h.01"></path>
                        </svg>
                        Đến ngày
                    </label>
                    <div class="relative">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="w-full rounded-md border-gray-300 pl-10 py-2 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-background text-foreground">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                                <line x1="16" x2="16" y1="2" y2="6"></line>
                                <line x1="8" x2="8" y1="2" y2="6"></line>
                                <line x1="3" x2="21" y1="10" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 transition-colors flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Áp dụng bộ lọc
                </button>
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-card rounded-lg shadow overflow-hidden border border-border">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Người dùng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Hạng cũ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Hạng mới
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Chi tiêu
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Đơn hàng
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Lý do
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">
                            Ngày thăng hạng
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-card divide-y divide-border">
                    @forelse($histories as $history)
                        <tr class="hover:bg-accent/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-foreground">
                                            {{ $history->user->full_name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-muted-foreground">
                                            {{ $history->user->email ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($history->oldRank)
                                    <span class="rank-badge" @if($history->oldRank->color) style="background-color: {{ $history->oldRank->color }};" @else class="bg-rank-default" @endif>
                                        {{ $history->oldRank->name }}
                                    </span>
                                @else
                                    <span class="text-muted-foreground">Chưa có hạng</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($history->newRank)
                                    <span class="rank-badge" @if($history->newRank->color) style="background-color: {{ $history->newRank->color }};" @else class="bg-rank-default" @endif>
                                        {{ $history->newRank->name }}
                                    </span>
                                @else
                                    <span class="text-muted-foreground">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-foreground">{{ number_format($history->total_spending) }} đ</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-foreground">{{ $history->total_orders }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-foreground">{{ $history->reason ?? 'Tự động' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                                {{ $history->changed_at ? $history->changed_at->format('d/m/Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground text-center">
                                Không có dữ liệu lịch sử thăng hạng
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-4 py-3 bg-muted border-t border-border sm:px-6">
            {{ $histories->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Theme Management
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;
    
    // Load saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
    
    function setTheme(theme) {
        if (theme === 'dark') {
            html.classList.add('dark');
            themeToggle.classList.add('dark');
            themeIcon.textContent = '☀️';
        } else {
            html.classList.remove('dark');
            themeToggle.classList.remove('dark');
            themeIcon.textContent = '🌙';
        }
        localStorage.setItem('theme', theme);
    }
    
    themeToggle.addEventListener('click', function() {
        const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });
}

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme toggle
    initThemeToggle();
});
</script>
@endsection