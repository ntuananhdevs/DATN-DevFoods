@extends('layouts/admin/contentLayoutMaster')

@section('title', 'Chương trình khuyến mãi')
@section('description', 'Quản lý các chương trình khuyến mãi và ưu đãi')

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

    /* Program type styling */
    .program-type {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1.25rem;
        transition: all 0.2s ease;
    }

    .program-type.discount {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .dark .program-type.discount {
        background-color: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    .program-type.cashback {
        background-color: #dcfce7;
        color: #15803d;
    }

    .dark .program-type.cashback {
        background-color: rgba(22, 163, 74, 0.2);
        color: #4ade80;
    }

    .program-type.flash-sale {
        background-color: #fef3c7;
        color: #d97706;
    }

    .dark .program-type.flash-sale {
        background-color: rgba(217, 119, 6, 0.2);
        color: #fbbf24;
    }

    .program-type.special {
        background-color: #f3e8ff;
        color: #7c3aed;
    }

    .dark .program-type.special {
        background-color: rgba(124, 58, 237, 0.2);
        color: #a78bfa;
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

    /* Progress bar will be controlled by inline styles */

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

    .status-badge.scheduled {
        background-color: #fef3c7;
        color: #d97706;
    }

    .dark .status-badge.scheduled {
        background-color: rgba(217, 119, 6, 0.2);
        color: #fbbf24;
    }

    .status-badge.expired {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .dark .status-badge.expired {
        background-color: rgba(220, 38, 38, 0.2);
        color: #f87171;
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

    /* Value display styling */
    .value-display {
        display: inline-block;
        padding: 4px 12px;
        background-color: #dcfce7;
        color: #15803d;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 500;
    }

    .dark .value-display {
        background-color: rgba(22, 163, 74, 0.2);
        color: #4ade80;
    }

    .value-display.percentage {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .dark .value-display.percentage {
        background-color: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
    }

    .value-display.amount {
        background-color: #fef3c7;
        color: #d97706;
    }

    .dark .value-display.amount {
        background-color: rgba(217, 119, 6, 0.2);
        color: #fbbf24;
    }

    /* Usage count styling */
    .usage-count {
        display: inline-block;
        padding: 4px 12px;
        background-color: #f3f4f6;
        color: #374151;
        border-radius: 9999px;
        font-size: 14px;
        font-weight: 500;
    }

    .dark .usage-count {
        background-color: rgba(55, 65, 81, 0.2);
        color: #9ca3af;
    }

    /* Date range styling */
    .date-range {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .dark .date-range {
        color: #9ca3af;
    }

    .date-range .start-date {
        font-weight: 600;
        color: #374151;
    }

    .dark .date-range .start-date {
        color: #e5e7eb;
    }

    /* Filter modal styling */
    .filter-modal {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-modal.hidden {
        display: none;
    }

    .filter-modal-content {
        background-color: hsl(var(--background));
        border-radius: 8px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 32rem;
        margin: 1rem;
    }

    .dark .filter-modal-content {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
    }

    /* View toggle */
    .view-toggle {
        display: flex;
        background: hsl(var(--muted));
        border-radius: 0.375rem;
        padding: 0.25rem;
    }

    .view-toggle button {
        flex: 1;
        padding: 0.5rem 1rem;
        border: none;
        background: transparent;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: hsl(var(--muted-foreground));
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-toggle button.active {
        background-color: hsl(var(--background));
        color: hsl(var(--foreground));
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .dark .view-toggle button.active {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
</style>

<div class="fade-in flex flex-col gap-4 pb-4 p-4">
    <!-- Main Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-gift">
                    <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                    <path d="M12 8v13"></path>
                    <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                    <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight">Chương trình khuyến mãi</h2>
                <p class="text-muted-foreground">Quản lý các chương trình khuyến mãi và ưu đãi</p>
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
            <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Tạo chương trình mới
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                    <path d="M12 8v13"></path>
                    <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                    <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Tổng chương trình</span>
            </div>
            <div class="text-2xl font-bold">{{ number_format($totalPrograms ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Đang hoạt động</span>
            </div>
            <div class="text-2xl font-bold">{{ $activePrograms ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-orange-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Sắp diễn ra</span>
            </div>
            <div class="text-2xl font-bold">{{ $scheduledPrograms ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="flex items-center gap-2 mb-2">
                <svg class="stat-icon text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m15 9-6 6"></path>
                    <path d="m9 9 6 6"></path>
                </svg>
                <span class="text-sm font-medium text-muted-foreground">Đã hết hạn</span>
            </div>
            <div class="text-2xl font-bold">{{ $expiredPrograms ?? 0 }}</div>
        </div>
    </div>

    <!-- Card containing table -->
    <div class="card border rounded-lg overflow-hidden">
        <!-- Table header -->
        <div class="p-6 border-b">
            <h3 class="text-lg font-medium">Danh sách chương trình khuyến mãi</h3>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b flex smleo:flex-row justify-between gap-4">
            <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <form action="">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên chương trình..." class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput" value="">
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
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground activate-selected">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-green-500">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                Kích hoạt đã chọn
                            </a>
                            <a href="#" class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground deactivate-selected">
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

        <!-- Table View -->
        <div id="tableView">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="py-3 px-4 text-left font-medium">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300">
                            </th>
                            <th class="py-3 px-4 text-left font-medium">Chương trình</th>
                            <th class="py-3 px-4 text-center font-medium">Loại</th>
                            <th class="py-3 px-4 text-center font-medium">Thời gian</th>
                            <th class="py-3 px-4 text-center font-medium">Giá trị</th>
                            <th class="py-3 px-4 text-center font-medium">Lượt sử dụng</th>
                            <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                            <th class="py-3 px-4 text-center font-medium">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                        <tr class="border-b" data-id="{{ $program->id }}" data-start-date="{{ $program->start_date }}" data-end-date="{{ $program->end_date }}">
                            <td class="py-3 px-4">
                                <input type="checkbox" name="selected_programs[]" value="{{ $program->id }}" class="program-checkbox rounded border-gray-300">
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    <div class="font-medium">{{ $program->name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ Str::limit($program->description ?? '', 50) }}</div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @php
                                $discountTypes = $program->discountCodes ? $program->discountCodes->pluck('discount_type')->unique()->toArray() : [];
                                if (count($discountTypes) == 1) {
                                switch ($discountTypes[0] ?? '') {
                                case 'percentage':
                                $typeClass = 'discount';
                                $typeText = 'Giảm giá %';
                                break;
                                case 'fixed_amount':
                                $typeClass = 'discount';
                                $typeText = 'Giảm giá cố định';
                                break;
                                case 'free_shipping':
                                $typeClass = 'special';
                                $typeText = 'Miễn phí vận chuyển';
                                break;
                                default:
                                $typeClass = 'special';
                                $typeText = 'Kết hợp';
                                break;
                                }
                                } else {
                                $typeClass = 'special';
                                $typeText = 'Kết hợp';
                                }
                                @endphp
                                <span class="program-type {{ $typeClass }}">{{ $typeText }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="date-range">
                                    <div class="start-date">{{ $program->start_date ? $program->start_date->format('d/m/Y') : 'N/A' }}</div>
                                    <div>đến {{ $program->end_date ? $program->end_date->format('d/m/Y') : 'N/A' }}</div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="value-display {{ $program->value_range === 'Chưa xác định' ? '' : 'percentage' }}">
                                    {{ $program->value_range ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    @php
                                    // Calculate total usage across all discount codes
                                    $totalUsageCount = $program->discountCodes->sum('current_usage_count');
                                    $maxUsage = $program->discountCodes->sum('max_total_usage');
                                    
                                    // Generate tooltip content with detailed information
                                    $tooltipContent = '';
                                    if ($program->discountCodes && $program->discountCodes->count() > 0) {
                                        $tooltipContent = $program->discountCodes->map(function($code) {
                                            $maxUsage = $code->max_total_usage ? number_format($code->max_total_usage) : 'Không giới hạn';
                                            return "{$code->code}: {$code->current_usage_count}/{$maxUsage}" . 
                                                   ($code->is_active ? '' : ' (Không hoạt động)');
                                        })->implode('<br>');
                                    } else {
                                        $tooltipContent = 'Chưa có mã giảm giá';
                                    }
                                    @endphp
                                    
                                    <div class="font-medium" data-tooltip="true" data-tooltip-content="{{ $tooltipContent }}">
                                        {{ number_format($totalUsageCount) }}
                                        @if($maxUsage > 0)
                                            <span class="text-xs text-muted-foreground">/ {{ number_format($maxUsage) }}</span>
                                        @endif
                                    </div>
                                    
                                    @if($maxUsage > 0)
                                        @php
                                        $percentage = min(100, (int)(($totalUsageCount / $maxUsage) * 100));
                                        @endphp
                                        <div class="w-full max-w-[100px]">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $percentage }}%;"></div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ $percentage }}%
                                        </div>
                                    @else
                                        <div class="text-xs text-muted-foreground">Không giới hạn</div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                $now = now();
                                if (!$program->is_active) {
                                $status = 'inactive';
                                $statusText = 'Không hoạt động';
                                } elseif ($program->start_date && $now->lt($program->start_date)) {
                                $status = 'scheduled';
                                $statusText = 'Sắp diễn ra';
                                } elseif ($program->end_date && $now->gt($program->end_date)) {
                                $status = 'expired';
                                $statusText = 'Đã hết hạn';
                                } else {
                                $status = 'active';
                                $statusText = 'Đang hoạt động';
                                }
                                @endphp
                                <span class="status-badge {{ $status }}">{{ $statusText }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex justify-center space-x-1">
                                    <a href="{{ route('admin.promotions.show', $program) }}"
                                        class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                        title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.promotions.edit', $program) }}"
                                        class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                        title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.promotions.destroy', $program) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                            onclick="confirmDelete('{{ $program->name }}', this)"
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
                                <div class="flex flex-col items-center justify-center text-muted-foreground py-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-2">
                                        <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                                        <path d="M12 8v13"></path>
                                        <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                                        <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium">Không có chương trình khuyến mãi nào</h3>
                                    <p class="text-sm">Hãy tạo chương trình khuyến mãi mới để bắt đầu</p>
                                    <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary mt-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2">
                                            <path d="M5 12h14"></path>
                                            <path d="M12 5v14"></path>
                                        </svg>
                                        Tạo chương trình mới
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($programs->hasPages())
            <div class="flex items-center justify-between px-4 py-4 border-t">
                <div class="text-sm text-muted-foreground">
                    Hiển thị {{ $programs->firstItem() }} đến {{ $programs->lastItem() }} của {{ $programs->total() }} mục
                </div>
                <div class="flex items-center space-x-2">
                    @unless ($programs->onFirstPage())
                    <a href="{{ $programs->previousPageUrl() }}" class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                    </a>
                    @endunless

                    @foreach ($programs->getUrlRange(1, $programs->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="h-8 min-w-8 rounded-md px-2 text-xs font-medium {{ $programs->currentPage() == $page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }} flex items-center justify-center">
                        {{ $page }}
                    </a>
                    @endforeach

                    @unless ($programs->currentPage() === $programs->lastPage())
                    <a href="{{ $programs->nextPageUrl() }}" class="h-8 w-8 rounded-md p-0 text-muted-foreground hover:bg-muted flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                    @endunless
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="filter-modal hidden">
    <div class="filter-modal-content">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-medium">Lọc chương trình khuyến mãi</h3>
            <button type="button" class="text-muted-foreground hover:text-foreground" onclick="toggleModal('filterModal')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form method="GET" action="{{ route('admin.promotions.index') }}" id="filterForm">
            <div class="p-4 space-y-6">
                <!-- Search -->
                <div class="space-y-2">
                    <label for="filter_search" class="text-sm font-medium">Tìm kiếm</label>
                    <input type="text" id="filter_search" name="search" value="{{ request('search') }}" class="w-full border rounded-md px-3 py-2 bg-background text-sm" placeholder="Tên chương trình...">
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Trạng thái</label>
                    <div class="flex flex-col gap-2">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="" class="rounded border-gray-300 mr-2" {{ request('status') === '' ? 'checked' : '' }}>
                            Tất cả
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="active" class="rounded border-gray-300 mr-2" {{ request('status') === 'active' ? 'checked' : '' }}>
                            Đang hoạt động
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="scheduled" class="rounded border-gray-300 mr-2" {{ request('status') === 'scheduled' ? 'checked' : '' }}>
                            Sắp diễn ra
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="expired" class="rounded border-gray-300 mr-2" {{ request('status') === 'expired' ? 'checked' : '' }}>
                            Đã hết hạn
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="inactive" class="rounded border-gray-300 mr-2" {{ request('status') === 'inactive' ? 'checked' : '' }}>
                            Không hoạt động
                        </label>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="space-y-2">
                    <label class="text-sm font-medium">Khoảng thời gian</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date_from" class="text-xs text-muted-foreground">Từ ngày</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                        </div>
                        <div>
                            <label for="date_to" class="text-xs text-muted-foreground">Đến ngày</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                        </div>
                    </div>
                </div>

                <!-- Program Type -->
                <div class="space-y-2">
                    <label for="filter_type" class="text-sm font-medium">Loại chương trình</label>
                    <select id="filter_type" name="type" class="w-full border rounded-md px-3 py-2 bg-background text-sm">
                        <option value="">Tất cả loại</option>
                        <option value="discount" {{ request('type') === 'discount' ? 'selected' : '' }}>Giảm giá</option>
                        <option value="cashback" {{ request('type') === 'cashback' ? 'selected' : '' }}>Hoàn tiền</option>
                        <option value="flash_sale" {{ request('type') === 'flash_sale' ? 'selected' : '' }}>Flash Sale</option>
                        <option value="special_event" {{ request('type') === 'special_event' ? 'selected' : '' }}>Sự kiện đặc biệt</option>
                    </select>
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
    // ----- Dropdown Toggle -----
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    // ----- Modal Toggle -----
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    // ----- Reset Filters -----
    function resetFilters() {
        const form = document.getElementById('filterForm');
        form.reset();
        toggleModal('filterModal');
        window.location.href = '{{ route("admin.promotions.index") }}';
    }

    // ----- Confirm Delete -----
    function confirmDelete(programName, button) {
        if (confirm(`Bạn có chắc chắn muốn xóa chương trình "${programName}"?`)) {
            button.closest('form').submit();
        }
    }
    
    // Define constants and global variables
    const ROUTES = {
        search: '{{ route("admin.promotions.search") }}',
    };

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;
    
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

    // ----- Bulk Actions -----
    function bulkAction(action) {
        const checkboxes = document.querySelectorAll('.program-checkbox:checked');
        if (checkboxes.length === 0) {
            dtmodalShowToast("error", {
                title: "Lỗi!",
                message: "Vui lòng chọn ít nhất một chương trình khuyến mãi"
            });
            return;
        }
        
        const ids = Array.from(checkboxes).map(checkbox => checkbox.value);
        
        // Hiển thị xác nhận bằng modal
        const actionText = action === 'activate' ? 'kích hoạt' : 'vô hiệu hóa';
        
        dtmodalConfirmIndex({
            title: `Xác nhận ${actionText} chương trình`,
            subtitle: `Bạn có chắc chắn muốn ${actionText} các chương trình đã chọn?`,
            message: "Hành động này sẽ thay đổi trạng thái của các chương trình khuyến mãi.",
            itemName: `${checkboxes.length} chương trình khuyến mãi`,
            onConfirm: () => {
                // Hiển thị loading spinner
                const actionButtons = document.querySelectorAll('.activate-selected, .deactivate-selected');
                actionButtons.forEach(btn => {
                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.dataset.originalText = originalText;
                    btn.innerHTML = `<div class="spinner mr-2"></div> Đang xử lý...`;
                });
                
                // Đóng dropdown
                document.getElementById('actionsMenu').classList.add('hidden');
                
                // Gửi request
                fetch('{{ route("admin.promotions.bulk-status-update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: ids,
                        action: action
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Có lỗi xảy ra khi xử lý yêu cầu');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Cập nhật trạng thái của các hàng đã chọn
                        if (data.programs && data.programs.length > 0) {
                            updateRowStatus(data.programs);
                        }
                        
                        // Hiển thị thông báo thành công bằng toast
                        dtmodalShowToast("success", {
                            title: "Thành công!",
                            message: data.message
                        });
                        
                        // Bỏ chọn tất cả các checkbox
                        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                        if (selectAllCheckbox) selectAllCheckbox.checked = false;
                        checkboxes.forEach(checkbox => checkbox.checked = false);
                    } else {
                        dtmodalShowToast("error", {
                            title: "Lỗi!",
                            message: data.message || 'Có lỗi xảy ra. Vui lòng thử lại sau.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    dtmodalShowToast("error", {
                        title: "Lỗi!",
                        message: error.message || 'Có lỗi xảy ra. Vui lòng thử lại sau.'
                    });
                })
                .finally(() => {
                    // Khôi phục trạng thái nút
                    actionButtons.forEach(btn => {
                        btn.disabled = false;
                        btn.innerHTML = btn.dataset.originalText;
                    });
                });
            }
        });
    }
    
    // Cập nhật trạng thái hiển thị của hàng
    function updateRowStatus(programs) {
        programs.forEach(program => {
            const row = document.querySelector(`tr[data-id="${program.id}"]`);
            if (!row) return;
            
            const statusCell = row.querySelector('td:nth-last-child(2)');
            if (!statusCell) return;
            
            const statusBadge = statusCell.querySelector('.status-badge');
            if (!statusBadge) return;
            
            // Xóa tất cả các class trạng thái
            statusBadge.classList.remove('active', 'inactive', 'scheduled', 'expired');
            
            if (program.is_active) {
                const now = new Date();
                const startDate = new Date(program.start_date);
                const endDate = new Date(program.end_date);
                
                if (now < startDate) {
                    statusBadge.classList.add('scheduled');
                    statusBadge.textContent = 'Sắp diễn ra';
                } else if (now > endDate) {
                    statusBadge.classList.add('expired');
                    statusBadge.textContent = 'Đã hết hạn';
                } else {
                    statusBadge.classList.add('active');
                    statusBadge.textContent = 'Đang hoạt động';
                }
            } else {
                statusBadge.classList.add('inactive');
                statusBadge.textContent = 'Không hoạt động';
            }
        });
        
        // Cập nhật số liệu thống kê
        updateStatistics();
    }
    
    // Cập nhật số liệu thống kê
    function updateStatistics() {
        // Đếm số lượng chương trình theo trạng thái
        const activeCount = document.querySelectorAll('.status-badge.active').length;
        const scheduledCount = document.querySelectorAll('.status-badge.scheduled').length;
        const expiredCount = document.querySelectorAll('.status-badge.expired').length;
        const inactiveCount = document.querySelectorAll('.status-badge.inactive').length;
        
        // Cập nhật hiển thị
        const statCards = document.querySelectorAll('.stat-card .text-2xl');
        if (statCards.length >= 4) {
            statCards[1].textContent = activeCount;
            statCards[2].textContent = scheduledCount;
            statCards[3].textContent = expiredCount + inactiveCount;
        }
    }

    // Perform AJAX search
    const performSearch = debounce(function(searchTerm) {
        const programTableBody = document.querySelector('#tableView tbody');
        if (!programTableBody) {
            console.error('Program table body not found');
            return;
        }

        if (!CSRF_TOKEN) {
            console.error('CSRF token not found');
            return;
        }

        // Show loading indicator
        programTableBody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="flex justify-center items-center py-4">
                        <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </td>
            </tr>
        `;

        // Get filter values
        const status = document.querySelector('input[name="status"]:checked')?.value || 'all';
        const dateFrom = document.querySelector('#date_from')?.value || '';
        const dateTo = document.querySelector('#date_to')?.value || '';
        const type = document.querySelector('#filter_type')?.value || '';

        fetch(ROUTES.search, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                search: searchTerm,
                status: status,
                date_from: dateFrom,
                date_to: dateTo,
                type: type
            })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            // Update stats first
            const statCards = document.querySelectorAll('.stat-card .text-2xl');
            if (statCards.length >= 4) {
                statCards[0].textContent = formatNumber(data.total_programs);
                statCards[1].textContent = formatNumber(data.active_programs);
                statCards[2].textContent = formatNumber(data.scheduled_programs);
                statCards[3].textContent = formatNumber(data.expired_programs);
            }

            // Clear the table body
            programTableBody.innerHTML = '';

            if (!data.programs || data.programs.length === 0) {
                programTableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="flex flex-col items-center justify-center text-muted-foreground py-6">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-2">
                                    <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                                    <path d="M12 8v13"></path>
                                    <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                                    <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                                </svg>
                                <h3 class="text-lg font-medium">Không có chương trình khuyến mãi nào</h3>
                                <p class="text-sm">Không tìm thấy chương trình khuyến mãi phù hợp với bộ lọc</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            // Generate the rows for each program
            data.programs.forEach(program => {
                const activeDiscountCodes = program.discount_codes.filter(code => code.is_active);
                const activeUsageCount = activeDiscountCodes.reduce((sum, code) => sum + code.current_usage_count, 0);
                
                const tooltipContent = program.discount_codes.length > 0
                    ? program.discount_codes.map(code => 
                        `${code.code} (${code.is_active ? 'Hoạt động' : 'Không hoạt động'}): ${code.current_usage_count}/${code.max_total_usage ?? 'Không giới hạn'}`
                      ).join(', ')
                    : 'No codes';
                
                // Calculate total max usage across discount codes
                const totalUsageCount = program.total_usage_count || 0;
                const discountCodes = program.discount_codes || [];
                const totalMaxUsage = discountCodes.reduce((sum, code) => sum + (code.max_total_usage || 0), 0);
                
                // Create usage progress HTML
                let usageHTML = '';
                if (totalMaxUsage > 0) {
                    const progressPercent = Math.min(100, Math.floor((totalUsageCount / totalMaxUsage) * 100));
                    usageHTML = `
                        <div class="w-full max-w-[100px]">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${progressPercent}%;"></div>
                            </div>
                        </div>
                        <div class="text-xs text-muted-foreground">
                            ${progressPercent}%
                        </div>
                    `;
                } else {
                    usageHTML = `<div class="text-xs text-muted-foreground">Không giới hạn</div>`;
                }

                const rowHtml = `
                    <tr class="border-b" data-id="${program.id}" data-start-date="${program.start_date}" data-end-date="${program.end_date}">
                        <td class="py-3 px-4">
                            <input type="checkbox" name="selected_programs[]" value="${program.id}" class="program-checkbox rounded border-gray-300">
                        </td>
                        <td class="py-3 px-4">
                            <div>
                                <div class="font-medium">${program.name}</div>
                                <div class="text-sm text-muted-foreground">${program.description}</div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="program-type ${program.type_class}">${program.type_text}</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="date-range">
                                <div class="start-date">${program.start_date}</div>
                                <div>đến ${program.end_date}</div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="value-display ${program.value_range === 'Chưa xác định' ? '' : 'percentage'}">
                                ${program.value_range}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <div class="font-medium" data-tooltip="true" data-tooltip-content="${tooltipContent}">
                                    ${formatNumber(totalUsageCount)}
                                    ${totalMaxUsage > 0 ? `<span class="text-xs text-muted-foreground">/ ${formatNumber(totalMaxUsage)}</span>` : ''}
                                </div>
                                ${usageHTML}
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="status-badge ${program.status}">${program.status_text}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex justify-center space-x-1">
                                <a href="{{ url('admin/promotions') }}/${program.id}"
                                    class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                    title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="{{ url('admin/promotions') }}/${program.id}/edit"
                                    class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                    title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ url('admin/promotions') }}/${program.id}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                        onclick="confirmDelete('${program.name}', this)"
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
                programTableBody.insertAdjacentHTML('beforeend', rowHtml);
            });

            // Re-add event listeners for checkboxes
            updateCheckboxes();
        })
        .catch(error => {
            console.error('Search error:', error);
            programTableBody.innerHTML = `
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

    // Update checkboxes when table changes
    function updateCheckboxes() {
        const programCheckboxes = document.querySelectorAll('.program-checkbox');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        
        programCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(programCheckboxes).every(cb => cb.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
    }

    // ----- Initialize on DOM Ready -----
    document.addEventListener('DOMContentLoaded', function() {
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown > div:not(.hidden)');
            dropdowns.forEach(dropdown => {
                const isClickInside = dropdown.contains(event.target) ||
                    dropdown.previousElementSibling.contains(event.target);

                if (!isClickInside) {
                    dropdown.classList.add('hidden');
                }
            });
        });

        // ----- Checkbox Functionality -----
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const programCheckboxes = document.querySelectorAll('.program-checkbox');
        const selectAllButton = document.getElementById('selectAllButton');

        // Handle select all checkbox
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                programCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });
        }

        // Handle select all button click
        if (selectAllButton) {
            selectAllButton.addEventListener('click', function() {
                const isAllChecked = Array.from(programCheckboxes).every(cb => cb.checked);
                programCheckboxes.forEach(checkbox => {
                    checkbox.checked = !isAllChecked;
                });
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = !isAllChecked;
                }
            });
        }

        // Handle individual checkboxes
        programCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(programCheckboxes).every(cb => cb.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
        
        // Bulk action buttons
        document.querySelector('.activate-selected')?.addEventListener('click', function(e) {
            e.preventDefault();
            bulkAction('activate');
        });
        
        document.querySelector('.deactivate-selected')?.addEventListener('click', function(e) {
            e.preventDefault();
            bulkAction('deactivate');
        });

        // Observe table changes for checkbox updates
        const tableView = document.getElementById('tableView');
        if (tableView) {
            new MutationObserver(updateCheckboxes).observe(tableView, {
                childList: true,
                subtree: true
            });
        }

        // Handle search input
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', () => performSearch(searchInput.value.trim()));
        }

        // Handle filter form submission with AJAX
        const filterForm = document.getElementById('filterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(filterForm);
                const searchTerm = document.getElementById('searchInput').value.trim();
                const status = formData.get('status') || 'all';
                const dateFrom = formData.get('date_from') || '';
                const dateTo = formData.get('date_to') || '';
                const type = formData.get('type') || '';
                
                performSearch(searchTerm);
                toggleModal('filterModal');
            });
        }
    });
</script>
@endsection

