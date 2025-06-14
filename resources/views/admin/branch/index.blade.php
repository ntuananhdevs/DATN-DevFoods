@extends('layouts.admin.contentLayoutMaster')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/branchs/branch-index.css') }}">
@endsection

@section('content')
    <div class="fade-in flex flex-col gap-4 pb-4">
        <!-- Main Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                    <i class="fas fa-code-branch"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Quản lý chi nhánh</h2>
                    <p class="text-muted-foreground">Danh sách và thông tin các chi nhánh</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.branches.create') }}" class="btn btn-primary flex items-center">
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

        <!-- Card containing content -->
        <div class="card border rounded-lg overflow-hidden">
            <!-- Header with view toggle -->
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium">Danh sách chi nhánh</h3>
                <div class="view-toggle">
                    <button id="tableViewBtn" class="active">
                        <i class="fas fa-table"></i>
                        Bảng
                    </button>
                    <button id="gridViewBtn">
                        <i class="fas fa-th"></i>
                        Lưới
                    </button>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="p-4 border-b flex flex-col sm:flex-row justify-between gap-4">
                <div class="relative w-full sm:w-auto sm:min-w-[300px]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="text" placeholder="Tìm kiếm theo tên, địa chỉ..."
                        class="border rounded-md px-3 py-2 bg-background text-sm w-full pl-9" id="searchInput"
                        value="{{ request('search') }}" autocomplete="off">
                </div>
                <div class="flex items-center gap-2">
                    <button class="btn btn-outline flex items-center" id="selectAllButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="mr-2">
                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        <span>Chọn tất cả</span>
                    </button>
                    <div class="dropdown relative">
                        <button class="btn btn-outline flex items-center" id="actionsDropdown"
                            onclick="toggleDropdown('actionsMenu')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="mr-2">
                                <circle cx="12" cy="12" r="2"></circle>
                                <circle cx="12" cy="5" r="2"></circle>
                                <circle cx="12" cy="19" r="2"></circle>
                            </svg>
                            Thao tác
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="ml-2">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>
                        <div id="actionsMenu"
                            class="hidden absolute right-0 mt-2 w-48 rounded-md border bg-popover text-popover-foreground shadow-md z-10">
                            <div class="p-2">
                                <a href="#"
                                    class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-green-50 hover:text-green-700 transition-colors duration-200"
                                    onclick="updateSelectedStatus(1)">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-green-700">Kích hoạt đã chọn</span>
                                </a>
                                <a href="#"
                                    class="flex items-center rounded-md px-2 py-1.5 text-sm hover:bg-red-50 hover:text-red-700 transition-colors duration-200"
                                    onclick="updateSelectedStatus(0)">
                                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                    <span class="text-red-700">Vô hiệu hóa đã chọn</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="tableView" class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-muted/50">
                            <th class="py-3 px-4 text-left">
                                <div class="flex items-center">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300">
                                </div>
                            </th>
                            <th class="py-3 px-4 text-left font-medium">ID</th>
                            <th class="py-3 px-4 text-left font-medium">Tên</th>
                            <th class="py-3 px-4 text-left font-medium">Địa chỉ</th>
                            <th class="py-3 px-4 text-left font-medium">Liên hệ</th>
                            <th class="py-3 px-4 text-left font-medium">Giờ làm việc</th>
                            <th class="py-3 px-4 text-left font-medium">Trạng thái</th>
                            <th class="py-3 px-4 text-left font-medium">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($branches as $branch)
                            <tr class="border-b">
                                <td class="py-3 px-4">
                                    <input type="checkbox" class="branch-checkbox" value="{{ $branch->id }}">
                                </td>
                                <td class="py-3 px-4">{{ $branch->id }}</td>
                                <td class="py-3 px-4">{{ $branch->name }}</td>
                                <td class="py-3 px-4">{{ Str::limit($branch->address, 40) }}</td>
                                <td class="py-3 px-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-phone text-sm text-muted-foreground"></i>
                                            <span>{{ $branch->phone }}</span>
                                        </div>
                                        @if ($branch->email)
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-envelope text-sm text-muted-foreground"></i>
                                                <span>{{ $branch->email }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">{{ date('H:i', strtotime($branch->opening_hour)) }} -
                                    {{ date('H:i', strtotime($branch->closing_hour)) }}</td>
                                <td class="py-3 px-4">
                                    <button type="button"
                                        class="px-3 py-1.5 rounded-full text-xs {{ $branch->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} hover:opacity-80 w-24 transition-opacity duration-200"
                                        data-branch-id="{{ $branch->id }}" data-branch-name="{{ $branch->name }}"
                                        data-branch-active="{{ $branch->active ? 'true' : 'false' }}">
                                        @if ($branch->active)
                                            <i class="fas fa-check mr-1"></i> Hoạt động
                                        @else
                                            <i class="fas fa-times mr-1"></i> Vô hiệu hóa
                                        @endif
                                    </button>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="{{ route('admin.branches.show', $branch->id) }}"
                                        class="btn btn-ghost btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-muted-foreground">
                                    <i class="fas fa-store-slash mr-2"></i>
                                    Không có chi nhánh nào
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Grid View -->
            <div id="gridView" class="grid-view" style="display: none;">
                <div id="gridContainer">
                    @forelse($branches as $branch)
                        <div class="branch-card">
                            <input type="checkbox" class="branch-checkbox branch-card-checkbox"
                                value="{{ $branch->id }}">
                            <div class="branch-card-header">
                                <div>
                                    <div class="branch-card-title">{{ $branch->name }}</div>
                                    <div class="branch-card-id">ID: {{ $branch->id }}</div>
                                </div>
                            </div>
                            <div class="branch-card-content">
                                <div class="branch-info-item">
                                    <i class="fas fa-map-marker-alt branch-info-icon"></i>
                                    <span>{{ $branch->address }}</span>
                                </div>
                                <div class="branch-info-item">
                                    <i class="fas fa-phone branch-info-icon"></i>
                                    <span>{{ $branch->phone }}</span>
                                </div>
                                @if ($branch->email)
                                    <div class="branch-info-item">
                                        <i class="fas fa-envelope branch-info-icon"></i>
                                        <span>{{ $branch->email }}</span>
                                    </div>
                                @endif
                                <div class="branch-info-item">
                                    <i class="fas fa-clock branch-info-icon"></i>
                                    <span>{{ date('H:i', strtotime($branch->opening_hour)) }} -
                                        {{ date('H:i', strtotime($branch->closing_hour)) }}</span>
                                </div>
                            </div>
                            <div class="branch-card-actions">
                                <button type="button"
                                    class="px-3 py-1.5 rounded-full text-xs {{ $branch->active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} hover:opacity-80 transition-opacity duration-200"
                                    data-branch-id="{{ $branch->id }}" data-branch-name="{{ $branch->name }}"
                                    data-branch-active="{{ $branch->active ? 'true' : 'false' }}">
                                    @if ($branch->active)
                                        <i class="fas fa-check mr-1"></i> Hoạt động
                                    @else
                                        <i class="fas fa-times mr-1"></i> Vô hiệu hóa
                                    @endif
                                </button>
                                <a href="{{ route('admin.branches.show', $branch->id) }}" class="btn btn-ghost btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-muted-foreground">
                            <i class="fas fa-store-slash mr-2 text-2xl"></i>
                            <p class="mt-2">Không có chi nhánh nào</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Loading spinner -->
            <div class="loading-spinner"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin/branchs/branch-index.js') }}"></script>
@endsection