@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản lý hạng thành viên')
@section('description', 'Quản lý các hạng thành viên và quyền lợi')

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

    /* Color classes */
    .bg-background { background-color: hsl(var(--background)); }
    .bg-foreground { background-color: hsl(var(--foreground)); }
    .bg-card { background-color: hsl(var(--card)); }
    .bg-card-foreground { background-color: hsl(var(--card-foreground)); }
    .bg-popover { background-color: hsl(var(--popover)); }
    .bg-popover-foreground { background-color: hsl(var(--popover-foreground)); }
    .bg-primary { background-color: hsl(var(--primary)); }
    .bg-primary-foreground { background-color: hsl(var(--primary-foreground)); }
    .bg-secondary { background-color: hsl(var(--secondary)); }
    .bg-secondary-foreground { background-color: hsl(var(--secondary-foreground)); }
    .bg-muted { background-color: hsl(var(--muted)); }
    .bg-muted-foreground { background-color: hsl(var(--muted-foreground)); }
    .bg-accent { background-color: hsl(var(--accent)); }
    .bg-accent-foreground { background-color: hsl(var(--accent-foreground)); }
    .bg-destructive { background-color: hsl(var(--destructive)); }
    .bg-destructive-foreground { background-color: hsl(var(--destructive-foreground)); }

    .text-background { color: hsl(var(--background)); }
    .text-foreground { color: hsl(var(--foreground)); }
    .text-card { color: hsl(var(--card)); }
    .text-card-foreground { color: hsl(var(--card-foreground)); }
    .text-popover { color: hsl(var(--popover)); }
    .text-popover-foreground { color: hsl(var(--popover-foreground)); }
    .text-primary { color: hsl(var(--primary)); }
    .text-primary-foreground { color: hsl(var(--primary-foreground)); }
    .text-secondary { color: hsl(var(--secondary)); }
    .text-secondary-foreground { color: hsl(var(--secondary-foreground)); }
    .text-muted { color: hsl(var(--muted)); }
    .text-muted-foreground { color: hsl(var(--muted-foreground)); }
    .text-accent { color: hsl(var(--accent)); }
    .text-accent-foreground { color: hsl(var(--accent-foreground)); }
    .text-destructive { color: hsl(var(--destructive)); }
    .text-destructive-foreground { color: hsl(var(--destructive-foreground)); }

    .border { border-color: hsl(var(--border)); }
    .border-border { border-color: hsl(var(--border)); }
    .border-input { border-color: hsl(var(--input)); }

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

    /* Custom input styles */
    input[type="text"],
    input[type="number"],
    input[type="date"],
    select {
        transition: all 0.2s ease;
    }

    input[type="text"]:hover,
    input[type="number"]:hover,
    input[type="date"]:hover,
    select:hover {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        outline: none;
    }

    /* Tier icon styling */
    .tier-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
    }

    /* Progress bar styling */
    .progress-bar {
        width: 100%;
        height: 8px;
        background-color: hsl(var(--muted));
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background-color: hsl(var(--primary));
        transition: width 0.3s ease;
    }

    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .status-badge.active {
        background-color: #dcfce7;
        color: #15803d;
    }

    .dark .status-badge.active {
        background-color: rgba(22, 163, 74, 0.2);
        color: #4ade80;
    }

    .status-badge.inactive {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .dark .status-badge.inactive {
        background-color: rgba(107, 114, 128, 0.2);
        color: #9ca3af;
    }

    /* Statistics cards */
    .stat-card {
        background-color: hsl(var(--card));
        border: 1px solid hsl(var(--border));
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dark .stat-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
    }

    .stat-icon {
        width: 16px;
        height: 16px;
        margin-right: 8px;
    }

    /* Benefits list styling */
    .benefits-list {
        max-width: 200px;
    }

    .benefit-item {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .benefit-icon {
        width: 12px;
        height: 12px;
        color: #22c55e;
        flex-shrink: 0;
    }

    .dark .benefit-icon {
        color: #4ade80;
    }

    /* Discount percentage badge */
    .discount-badge {
        display: inline-block;
        padding: 4px 12px;
        background-color: #dbeafe;
        color: #1e40af;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 500;
    }

    .dark .discount-badge {
        background-color: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    /* User count slider */
    .user-range-container {
        margin: 10px 0;
        padding: 10px 0;
        position: relative;
    }

    .user-slider {
        position: relative;
        height: 4px;
        background: hsl(var(--muted));
        margin: 20px 10px 30px;
        border-radius: 2px;
    }

    .user-slider-track {
        position: absolute;
        height: 100%;
        background: hsl(var(--primary));
        border-radius: 2px;
        max-width: 100%;
    }

    .user-slider-handle {
        position: absolute;
        width: 16px;
        height: 16px;
        background: hsl(var(--primary));
        border: 2px solid hsl(var(--background));
        border-radius: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .user-inputs {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
    }

    .user-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid hsl(var(--border));
        border-radius: 4px;
        background-color: hsl(var(--background));
        color: hsl(var(--foreground));
    }

    .user-display {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        margin-top: 5px;
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-take4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown">
                    <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm2 16h16"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Quản lý hạng thành viên</h2>
                <p class="text-muted-foreground">Quản lý các hạng thành viên và quyền lợi</p>
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
            <div class="dropdown relative">
                <button class="btn btn-outline flex items-center" id="exportDropdown" onclick="toggleDropdown('exportMenu')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Xuất
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>
                <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                    <div class="p-2">
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M8 13h2"></path>
                                <path d="M8 17h2"></path>
                                <path d="M14 13h2"></path>
                                <path d="M14 17h2"></path>
                            </svg>
                            Xuất Excel
                        </a>
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Xuất PDF
                        </a>
                        <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="M8 13h8"></path>
                                <path d="M8 17h8"></path>
                            </svg>
                            Xuất CSV
                        </a>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.user_ranks.create') }}" class="btn btn-primary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Thêm hạng mới
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Tổng thành viên</span>
            </div>
            <div class="text-2xl font-bold">{{ number_format($totalUsers) }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm2 16h16"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Số hạng hoạt động</span>
            </div>
            <div class="text-2xl font-bold">{{ $activeTiersCount }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-purple-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Hạng VIP</span>
            </div>
            <div class="text-2xl font-bold">{{ $vipUsersCount }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Tỷ lệ nâng hạng</span>
            </div>
            <div class="text-2xl font-bold">{{ $upgradeRate }}%</div>
        </div>
    </div>

    <!-- Card containing table -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách hạng thành viên</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <form method="GET" action="{{ route('admin.user_ranks.index') }}">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên hạng..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput" value="{{ request('search') }}">
                </form>
            </div>
            <div class="flex items-center gap-2">
                <button class="btn btn-outline flex items-center" id="selectAllButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>Chọn tất cả</span>
                </button>
                <div class="dropdown relative">
                    <button class="btn btn-outline flex items-center" id="actionsDropdown" onclick="toggleDropdown('actionsMenu')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <circle cx="12" cy="12" r="2"></circle>
                            <circle cx="12" cy="5" r="2"></circle>
                            <circle cx="12" cy="19" r="2"></circle>
                        </svg>
                        Thao tác
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </button>
                    <div id="actionsMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                        <div class="p-2">
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(1)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-green-500">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                Kích hoạt đã chọn
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground" onclick="updateSelectedStatus(0)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-red-500">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m15 9-6 6"></path>
                                    <path d="m9 9 6 6"></path>
                                </svg>
                                Vô hiệu hóa đã chọn
                            </a>
                        </div>
                    </div>
                </div>
                <button class="btn btn-outline flex items-center" onclick="toggleModal('filterModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    Lọc
                </button>
            </div>
        </div>

        <!-- Table container -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="py-3 px-4 text-left font-medium">
                            <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
                        </th>
                        <th class="py-3 px-4 text-left font-medium">Hạng</th>
                        <th class="py-3 px-4 text-left font-medium">Điều kiện</th>
                        <th class="py-3 px-4 text-center font-medium">Số thành viên</th>
                        <th class="py-3 px-4 text-center font-medium">Quyền lợi</th>
                        <th class="py-3 px-4 text-center font-medium">% Giảm giá</th>
                        <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                        <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="tierTableBody">
                    @forelse($ranks as $rank)
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <input type="checkbox" name="selected_ranks[]" value="{{ $rank->id }}" class="rank-checkbox rounded border-gray-300">
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="tier-icon" style="background-color: {{ $rank->color }}">
                                    {{ $rank->icon ?? substr($rank->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium">{{ $rank->name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ $rank->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-sm space-y-1">
                                <div>Chi tiêu: {{ number_format($rank->min_spending, 0, ',', '.') }} đ</div>
                                <div>Đơn hàng: {{ $rank->min_orders }}</div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <div class="font-medium">{{ number_format($rank->users_count) }}</div>
                                <div class="w-full max-w-[80px]">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $totalUsers > 0 ? ($rank->users_count / $totalUsers) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ $totalUsers > 0 ? number_format(($rank->users_count / $totalUsers) * 100, 1) : 0 }}%
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="benefits-list">
                                <div class="text-sm space-y-1">
                                    @php
                                    // Safely decode benefits, handling both JSON strings and arrays
                                    $benefits = $rank->benefits;
                                    if (is_string($rank->benefits)) {
                                    // Try to decode as JSON
                                    $decoded = json_decode($rank->benefits, true);
                                    if (json_last_error() === JSON_ERROR_NONE) {
                                    $benefits = is_array($decoded) ? $decoded : [];
                                    } else {
                                    // Handle double-encoded JSON
                                    $decodedAgain = json_decode(json_decode($rank->benefits, true), true);
                                    $benefits = is_array($decodedAgain) ? $decodedAgain : [];
                                    }
                                    }
                                    $benefits = is_array($benefits) ? $benefits : [];
                                    @endphp
                                    @forelse(array_slice($benefits, 0, 2) as $benefit)
                                    <div class="benefit-item">
                                        <svg class="benefit-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                                            <path d="M12 8v13"></path>
                                            <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                                            <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                                        </svg>
                                        <span>{{ $benefit }}</span>
                                    </div>
                                    @empty
                                    <div class="text-xs text-muted-foreground">Không có quyền lợi</div>
                                    @endforelse
                                    @if(count($benefits) > 2)
                                    <div class="text-xs text-muted-foreground">
                                        +{{ count($benefits) - 2 }} quyền lợi khác
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="discount-badge">
                                {{ number_format($rank->discount_percentage, 1) }}%
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge {{ $rank->is_active ? 'active' : 'inactive' }}">
                                {{ $rank->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ route('admin.user_ranks.edit', $rank->id) }}"
                                    class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                    title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.user_ranks.destroy', $rank->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                        onclick="dtmodalConfirmDelete({
                                                title: 'Xác nhận xóa hạng thành viên',
                                                subtitle: 'Bạn có chắc chắn muốn xóa hạng thành viên này?',
                                                message: 'Hành động này không thể hoàn tác.',
                                                itemName: '{{ $rank->name }}',
                                                onConfirm: () => this.closest('form').submit()
                                            })"
                                        title="Xóa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"></path>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                    <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm2 16h16"></path>
                                </svg>
                                <h3 class="text-lg font-medium">Không có hạng thành viên nào</h3>
                                <p class="text-sm">Hãy thêm hạng thành viên mới để bắt đầu</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-background rounded-lg shadow-lg w-full max-w-lg mx-4">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-medium">Lọc hạng thành viên</h3>
            <button type="button" class="text-muted-foreground hover:text-foreground" onclick="toggleModal('filterModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form id="filterForm" method="GET" action="{{ route('admin.user_ranks.index') }}">
            <div class="p-4 space-y-6">
                <!-- User Count Range -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Số lượng thành viên</label>
                    <div class="user-range-container">
                        <div class="user-slider" id="userSlider">
                            <div class="user-slider-track" id="userTrack"></div>
                            <div class="user-slider-handle" id="minUserHandle" data-handle="min"></div>
                            <div class="user-slider-handle" id="maxUserHandle" data-handle="max"></div>
                        </div>
                        <div class="user-display">
                            <span id="minUserDisplay">0</span>
                            <span id="maxUserDisplay">1500</span>
                        </div>
                    </div>
                    <div class="user-inputs">
                        <input type="number" id="minUserInput" name="user_min" class="user-input" placeholder="Số tối thiểu" value="{{ request('user_min', 0) }}">
                        <input type="number" id="maxUserInput" name="user_max" class="user-input" placeholder="Số tối đa" value="{{ request('user_max', 1500) }}">
                    </div>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Trạng thái</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="all" class="rounded border-gray-300 mr-2" {{ request('status', 'all') == 'all' ? 'checked' : '' }}>
                            Tất cả
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="active" class="rounded border-gray-300 mr-2" {{ request('status') == 'active' ? 'checked' : '' }}>
                            Đang hoạt động
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="inactive" class="rounded border-gray-300 mr-2" {{ request('status') == 'inactive' ? 'checked' : '' }}>
                            Không hoạt động
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end p-4 border-t space-x-2">
                <button type="button" class="btn btn-outline" onclick="resetFilters()">Xóa bộ lọc</button>
                <button type="button" class="btn btn-outline" onclick="toggleModal('filterModal')">Đóng</button>
                <button type="submit" class="btn btn-primary">Áp dụng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Define constants and global variables
const ROUTES = {
    search: '{{ route("admin.user_ranks.search") }}',
    edit: '{{ route("admin.user_ranks.edit", ":id") }}',
    destroy: '{{ route("admin.user_ranks.destroy", ":id") }}',
    updateStatus: '{{ route("admin.user_ranks.updateStatus") }}'
};

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

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

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function formatNumber(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function toggleModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.toggle('hidden');
    }
}

function resetFilters() {
    window.location.href = '{{ route("admin.user_ranks.index") }}';
}

function updateSelectedStatus(status) {
    const selectedRanks = Array.from(document.querySelectorAll('.rank-checkbox:checked')).map(cb => cb.value);

    if (selectedRanks.length === 0) {
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: 'Vui lòng chọn ít nhất một hạng để thực hiện thao tác.'
        });
        return;
    }

    if (!CSRF_TOKEN) {
        console.error('CSRF token not found');
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: 'Không tìm thấy CSRF token. Vui lòng tải lại trang.'
        });
        return;
    }

    // Hiển thị xác nhận bằng modal
    const actionText = status ? 'kích hoạt' : 'vô hiệu hóa';
    
    dtmodalConfirmIndex({
        title: `Xác nhận ${actionText} hạng thành viên`,
        subtitle: `Bạn có chắc chắn muốn ${actionText} các hạng thành viên đã chọn?`,
        message: "Hành động này sẽ thay đổi trạng thái của các hạng thành viên.",
        itemName: `${selectedRanks.length} hạng thành viên`,
        onConfirm: () => {
            // Gửi request
    fetch(ROUTES.updateStatus, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            rank_ids: selectedRanks,
            is_active: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật UI
            data.updated_ranks.forEach(rank => {
                const row = document.querySelector(`tr input.rank-checkbox[value="${rank.id}"]`)?.closest('tr');
                if (row) {
                    const statusBadge = row.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = `status-badge ${rank.is_active ? 'active' : 'inactive'}`;
                        statusBadge.textContent = rank.is_active ? 'Đang hoạt động' : 'Không hoạt động';
                    }
                    const checkbox = row.querySelector('.rank-checkbox');
                    if (checkbox) checkbox.checked = false;
                }
            });

            // Cập nhật số lượng hạng hoạt động
            const activeTiersCard = document.querySelector('.stat-card:nth-child(2) .text-2xl');
            if (activeTiersCard && data.active_tiers_count !== undefined) {
                activeTiersCard.textContent = formatNumber(data.active_tiers_count);
            }

            // Đặt lại checkbox chọn tất cả
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;

            dtmodalShowToast('success', {
                title: 'Thành công',
                message: data.message
            });

            toggleDropdown('actionsMenu');
        } else {
            dtmodalShowToast('error', {
                title: 'Lỗi',
                message: data.message || 'Cập nhật trạng thái thất bại.'
            });
        }
    })
    .catch(error => {
        console.error('Update status error:', error);
        let errorMessage = 'Đã xảy ra lỗi khi cập nhật trạng thái.';
        if (error.responseJSON && error.responseJSON.message) {
            errorMessage = error.responseJSON.message;
        } else if (error.status === 404) {
            errorMessage = 'Không tìm thấy hạng thành viên';
        } else if (error.status === 403) {
            errorMessage = 'Bạn không có quyền thực hiện thao tác này';
        } else if (error.status === 422) {
            errorMessage = 'Dữ liệu không hợp lệ';
        }
        dtmodalShowToast('error', {
            title: 'Lỗi',
            message: errorMessage
        });
            });
        }
    });
}

// Perform AJAX search
const performSearch = debounce(function(searchTerm) {
    const tierTableBody = document.getElementById('tierTableBody');
    if (!tierTableBody) {
        console.error('Tier table body not found');
        return;
    }

    if (!CSRF_TOKEN) {
        console.error('CSRF token not found');
        return;
    }

    fetch(ROUTES.search, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            search: searchTerm,
            user_min: parseInt(document.getElementById('minUserInput')?.value || 0),
            user_max: parseInt(document.getElementById('maxUserInput')?.value || 1500),
            status: document.querySelector('input[name="status"]:checked')?.value || 'all'
        })
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        tierTableBody.innerHTML = '';

        if (!data.ranks || data.ranks.length === 0) {
            tierTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="flex flex-col items-center justify-center text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                                <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm2 16h16"></path>
                            </svg>
                            <h3 class="text-lg font-medium">Không tìm thấy hạng thành viên</h3>
                            <p class="text-sm">Không có hạng nào khớp với từ khóa tìm kiếm</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        data.ranks.forEach(rank => {
            const benefits = Array.isArray(rank.benefits) ? rank.benefits : [];
            const benefitsHtml = benefits.length > 0 ? benefits.slice(0, 2).map(benefit => `
                <div class="benefit-item">
                    <svg class="benefit-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                        <path d="M12 8v13"></path>
                        <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                        <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                    </svg>
                    <span>${benefit}</span>
                </div>
            `).join('') + (benefits.length > 2 ? `<div class="text-xs text-muted-foreground">+${benefits.length - 2} quyền lợi khác</div>` : '') : `
                <div class="text-xs text-muted-foreground">Không có quyền lợi</div>
            `;

            const rowHtml = `
                <tr class="border-b">
                    <td class="py-3 px-4">
                        <input type="checkbox" name="selected_ranks[]" value="${rank.id}" class="rank-checkbox rounded border-gray-300">
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="tier-icon" style="background-color: ${rank.color}">
                                ${rank.icon || rank.name.charAt(0)}
                            </div>
                            <div>
                                <div class="font-medium">${rank.name}</div>
                                <div class="text-sm text-muted-foreground">${rank.slug}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-sm space-y-1">
                            <div>Chi tiêu: ${formatNumber(rank.min_spending)} đ</div>
                            <div>Đơn hàng: ${rank.min_orders}</div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex flex-col items-center gap-1">
                            <div class="font-medium">${formatNumber(rank.users_count)}</div>
                            <div class="w-full max-w-[80px]">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: ${data.total_users > 0 ? (rank.users_count / data.total_users) * 100 : 0}%"></div>
                                </div>
                            </div>
                            <div class="text-xs text-muted-foreground">
                                ${data.total_users > 0 ? ((rank.users_count / data.total_users) * 100).toFixed(1) : 0}%
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="benefits-list">
                            <div class="text-sm space-y-1">
                                ${benefitsHtml}
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="discount-badge">
                            ${parseFloat(rank.discount_percentage).toFixed(1)}%
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="status-badge ${rank.is_active ? 'active' : 'inactive'}">
                            ${rank.is_active ? 'Đang hoạt động' : 'Không hoạt động'}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex justify-center space-x-1">
                            <a href="${ROUTES.edit.replace(':id', rank.id)}"
                               class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                               title="Chỉnh sửa">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                            </a>
                            <form action="${ROUTES.destroy.replace(':id', rank.id)}" method="POST" class="delete-form">
                                <input type="hidden" name="_token" value="${CSRF_TOKEN}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                        onclick="dtmodalConfirmDelete({
                                            title: 'Xác nhận xóa hạng thành viên',
                                            subtitle: 'Bạn có chắc chắn muốn xóa hạng thành viên này?',
                                            message: 'Hành động này không thể hoàn tác.',
                                            itemName: '${rank.name}',
                                            onConfirm: () => this.closest('form').submit()
                                        })"
                                        title="Xóa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
            tierTableBody.insertAdjacentHTML('beforeend', rowHtml);
        });
    })
    .catch(error => {
        console.error('Search error:', error);
        tierTableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                <div class="flex flex-col items-center justify-center text-muted-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2">
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                        <path d="m15 9-6 6"></path>
                        <path d="m9 9 6 6"></path>
                    </svg>
                    <h3 class="text-lg font-medium">Đã xảy ra lỗi</h3>
                    <p class="text-sm">Không thể tải dữ liệu. Vui lòng thử lại.</p>
                </div>
            </td>
            </tr>
        `;
    });
}, 300);

// User range slider class
class UserRangeSlider {
    constructor(config) {
        this.min = config.min || 0;
        this.max = config.max || 1500;
        this.step = config.step || 10;
        this.minValue = config.minValue || this.min;
        this.maxValue = config.maxValue || this.max;
        this.slider = document.getElementById(config.sliderId);
        this.track = document.getElementById(config.trackId);
        this.minHandle = document.getElementById(config.minHandleId);
        this.maxHandle = document.getElementById(config.maxHandleId);
        this.minDisplay = document.getElementById('minUserDisplay');
        this.maxDisplay = document.getElementById('maxUserDisplay');
        this.minInput = document.getElementById('minUserInput');
        this.maxInput = document.getElementById('maxUserInput');
        this.isDragging = false;
        this.activeHandle = null;

        if (!this.slider || !this.track || !this.minHandle || !this.maxHandle) {
            console.error('Slider elements not found');
            return;
        }

        this.init();
    }

    init() {
        // Ensure initial values are within bounds
        this.minValue = Math.max(this.min, Math.min(this.minValue, this.max - this.step));
        this.maxValue = Math.min(this.max, Math.max(this.maxValue, this.min + this.step));
        
        // Set initial input values if they're empty
        if (this.minInput && !this.minInput.value) {
            this.minInput.value = this.minValue;
        }
        if (this.maxInput && !this.maxInput.value) {
            this.maxInput.value = this.maxValue;
        }
        
        // Read values from inputs if they exist
        if (this.minInput && this.minInput.value) {
            this.minValue = parseInt(this.minInput.value) || this.min;
        }
        if (this.maxInput && this.maxInput.value) {
            this.maxValue = parseInt(this.maxInput.value) || this.max;
        }
        
        this.updateVisual();
        this.attachEvents();
    }

    formatNumber(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }

    updateVisual() {
        const range = this.max - this.min;
        const minPercent = ((this.minValue - this.min) / range) * 100;
        const maxPercent = ((this.maxValue - this.min) / range) * 100;

        // Ensure percentages are within bounds
        const safeMinPercent = Math.max(0, Math.min(minPercent, 100));
        const safeMaxPercent = Math.max(0, Math.min(maxPercent, 100));

        // Update handle positions
        this.minHandle.style.left = `${safeMinPercent}%`;
        this.maxHandle.style.left = `${safeMaxPercent}%`;
        
        // Update track position and width
        this.track.style.left = `${safeMinPercent}%`;
        this.track.style.width = `${safeMaxPercent - safeMinPercent}%`;

        // Update displays and inputs
        if (this.minDisplay) this.minDisplay.textContent = this.formatNumber(this.minValue);
        if (this.maxDisplay) this.maxDisplay.textContent = this.formatNumber(this.maxValue);
        if (this.minInput) this.minInput.value = this.minValue;
        if (this.maxInput) this.maxInput.value = this.maxValue;
    }

    attachEvents() {
        const startDrag = (handle) => {
            this.isDragging = true;
            this.activeHandle = handle;
            document.body.style.cursor = 'grabbing';
        };

        const stopDrag = () => {
            this.isDragging = false;
            this.activeHandle = null;
            document.body.style.cursor = '';
        };

        const handleMove = (clientX) => {
            if (!this.isDragging) return;
            const value = this.getValueFromPosition(clientX);
            if (this.activeHandle === 'min') {
                this.minValue = Math.min(Math.max(value, this.min), this.maxValue - this.step);
            } else if (this.activeHandle === 'max') {
                this.maxValue = Math.max(Math.min(value, this.max), this.minValue + this.step);
            }
            this.updateVisual();
        };

        // Mouse events
        this.minHandle.addEventListener('mousedown', () => startDrag('min'));
        this.maxHandle.addEventListener('mousedown', () => startDrag('max'));
        document.addEventListener('mousemove', (e) => handleMove(e.clientX));
        document.addEventListener('mouseup', stopDrag);

        // Touch events
        this.minHandle.addEventListener('touchstart', (e) => {
            e.preventDefault();
            startDrag('min');
        }, { passive: false });
        this.maxHandle.addEventListener('touchstart', (e) => {
            e.preventDefault();
            startDrag('max');
        }, { passive: false });
        document.addEventListener('touchmove', (e) => {
            e.preventDefault();
            handleMove(e.touches[0].clientX);
        }, { passive: false });
        document.addEventListener('touchend', stopDrag);

        // Slider click
        this.slider.addEventListener('click', (e) => {
            if (this.isDragging) return;
            const value = this.getValueFromPosition(e.clientX);
            const minDiff = Math.abs(value - this.minValue);
            const maxDiff = Math.abs(value - this.maxValue);
            if (minDiff < maxDiff) {
                this.minValue = Math.min(value, this.maxValue - this.step);
            } else {
                this.maxValue = Math.max(value, this.minValue + this.step);
            }
            this.updateVisual();
        });

        // Input events
        if (this.minInput) {
            this.minInput.addEventListener('change', (e) => {
                const value = parseInt(e.target.value);
                if (!isNaN(value)) {
                    this.minValue = Math.max(this.min, Math.min(value, this.maxValue - this.step));
                    this.updateVisual();
                }
            });
        }
        if (this.maxInput) {
            this.maxInput.addEventListener('change', (e) => {
                const value = parseInt(e.target.value);
                if (!isNaN(value)) {
                    this.maxValue = Math.min(this.max, Math.max(value, this.minValue + this.step));
                    this.updateVisual();
                }
            });
        }
    }

    getValueFromPosition(clientX) {
        const rect = this.slider.getBoundingClientRect();
        let percent = (clientX - rect.left) / rect.width;
        percent = Math.max(0, Math.min(1, percent));
        return Math.round((this.min + percent * (this.max - this.min)) / this.step) * this.step;
    }
}

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme toggle
    initThemeToggle();
    
    // Check for session toast notification
    if (typeof dtmodalShowToast === 'function' && document.querySelector('[data-toast]')) {
        const toastData = document.querySelector('[data-toast]').dataset;
        dtmodalShowToast(toastData.type, {
            title: toastData.title,
            message: toastData.message
        });
    }
    
    // Initialize user range slider
    window.userSlider = new UserRangeSlider({
        min: parseInt(document.getElementById('minUserDisplay')?.textContent) || 0,
        max: parseInt(document.getElementById('maxUserDisplay')?.textContent) || 1500,
        minValue: parseInt(document.getElementById('minUserInput')?.value) || 0,
        maxValue: parseInt(document.getElementById('maxUserInput')?.value) || 1500,
        step: 10,
        sliderId: 'userSlider',
        trackId: 'userTrack',
        minHandleId: 'minUserHandle',
        maxHandleId: 'maxUserHandle'
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
        const dropdowns = document.querySelectorAll('.dropdown .absolute:not(.hidden)');
        dropdowns.forEach(dropdown => {
            const button = dropdown.previousElementSibling;
            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });

    // Handle checkboxes
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const selectAllButton = document.getElementById('selectAllButton');
    let rankCheckboxes = document.querySelectorAll('.rank-checkbox');

    function updateCheckboxes() {
        rankCheckboxes = document.querySelectorAll('.rank-checkbox');
        rankCheckboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = Array.from(rankCheckboxes).every(c => c.checked);
                }
            });
        });
    }

    updateCheckboxes();

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            rankCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
        });
    }

    if (selectAllButton) {
        selectAllButton.addEventListener('click', () => {
            const allChecked = Array.from(rankCheckboxes).every(cb => cb.checked);
            rankCheckboxes.forEach(cb => {
                cb.checked = !allChecked;
            });
            if (selectAllCheckbox) selectAllCheckbox.checked = !allChecked;
        });
    }

    // Observe table changes for checkbox updates
    const tierTableBody = document.getElementById('tierTableBody');
    if (tierTableBody) {
        new MutationObserver(updateCheckboxes).observe(tierTableBody, {
            childList: true,
            subtree: true
        });
    }

    // Handle search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', () => performSearch(searchInput.value.trim()));
        performSearch(searchInput.value.trim());
    }
});
</script>
@endsection