@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Quản lý banner')
@section('content')
    @push('scripts')
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            // Subscribe to channel
            const channel = pusher.subscribe('banner-channel');

            // Handle new banner event
            channel.bind('banner-created', function(data) {
                location.reload();
            });

            // Handle updated banner event
            channel.bind('banner-updated', function(data) {
                location.reload();
            });

            // Handle deleted banner event
            channel.bind('banner-deleted', function(data) {
                location.reload();
            });
        </script>
    @endpush

    <div class="min-h-screen bg-gradient-to-br ">
        <div class="fade-in flex flex-col gap-4 pb-4  animate-slideInUp delay-200 duration-700 ease-in-out">
            <!-- Main Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <i class="fas fa-image text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Quản lý banner</h2>
                        <p class="text-muted-foreground">Quản lý danh sách banner của bạn</p>
                    </div>
                </div>
                <a href="{{ route('admin.banners.create') }}" class="btn btn-primary flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="mr-2">
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>
                    Thêm mới
                </a>
            </div>

            <!-- Data Table Card with enhanced animations -->
            <div
                class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-500 hover:shadow-2xl animate-slideInUp delay-200 duration-700 ease-in-out">
                <!-- Table Header -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Danh sách banner</h2>
                </div>

                <!-- Controls with hover animations -->
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <!-- Search Box -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" placeholder="Tìm kiếm theo tiêu đề, mô tả ..." id="dataTableSearch"
                                value="{{ request('search') }}" onkeyup="handleSearch(event)"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-3">
                            <button onclick="toggleSelectAll()"
                                class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 hover:border-gray-400 transform transition-all duration-200 hover:scale-105 flex items-center space-x-2">
                                <i class="fas fa-check-square"></i>
                                <span>Chọn tất cả</span>
                            </button>

                            <div class="relative inline-block text-left">
                                <button type="button"
                                    class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 hover:border-gray-400 transform transition-all duration-200 hover:scale-105 flex items-center space-x-2"
                                    data-toggle="dropdown">
                                    <i class="fas fa-tasks"></i>
                                    <span>Thao tác</span>
                                    <i class="fas fa-chevron-down ml-1"></i>
                                </button>
                                <div
                                    class="dropdown-menu absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <a href="#" onclick="updateSelectedBannerStatus(1)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-200">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        Kích hoạt đã chọn
                                    </a>
                                    <a href="#" onclick="updateSelectedBannerStatus(0)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-200">
                                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                        Vô hiệu hóa đã chọn
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="dataTable">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </th>
                                <th data-sort="id"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-200 hover:text-gray-700 transition-all duration-300 ease-in-out active-sort">
                                    <div class="flex items-center space-x-1">
                                        <span>ID</span>
                                        <i class="fas fa-arrow-up text-blue-500"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hình ảnh
                                </th>
                                <th data-sort="title"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-200 hover:text-gray-700 transition-all duration-300 ease-in-out">
                                    <div class="flex items-center space-x-1">
                                        <span>Tiêu đề</span>
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Trạng thái
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thứ tự
                                </th>
                                <th data-sort="position"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-200 hover:text-gray-700 transition-all duration-300 ease-in-out">
                                    <div class="flex items-center space-x-1">
                                        <span>Vị trí</span>
                                        <i class="fas fa-sort text-gray-400"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày bắt đầu
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ngày kết thúc
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Thao tác
                                </th>
                            </tr>
                        </thead>
                        <tbody id="dataTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($banners as $banner)
                                <tr class="border-b">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox"
                                            class="row-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            value="{{ $banner->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900 bg-blue-50 px-3 py-1 rounded-full inline-block">
                                            {{ $banner->id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center">
                                            <div class="w-[100px] h-[60px] overflow-hidden">
                                                @php
                                                    $defaultImage = asset('images/default-banner.png');
                                                    $imageSrc = $defaultImage;

                                                    if ($banner->image_path) {
                                                        if (filter_var($banner->image_path, FILTER_VALIDATE_URL)) {
                                                            // Nếu image_path là URL hợp lệ (ví dụ lưu trên S3)
                                                            $imageSrc = $banner->image_path;
                                                        } else {
                                                            // Nếu image_path không phải URL thì giả định file trong storage
                                                            $imageSrc = Storage::disk('s3')->url($banner->image_path);
                                                        }
                                                    }
                                                @endphp
                                                <img src="{{ $imageSrc }}" alt="{{ $banner->title }}"
                                                    class="w-full h-full object-cover rounded" style="border-radius:5px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $banner->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('admin.banners.toggle-status', $banner->id) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transform transition-all duration-200 hover:scale-105 {{ $banner->is_active ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                @if ($banner->is_active)
                                                    <i class="fas fa-check mr-1"></i>
                                                    Hoạt động
                                                @else
                                                    <i class="fas fa-times mr-1"></i>
                                                    Vô hiệu hóa
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if (!is_null($banner->order))
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transform transition-all duration-200 hover:scale-105
                                                    {{ $banner->order === 0 ? 'bg-blue-100 text-blue-800' : ($banner->order === 1 ? 'bg-cyan-100 text-cyan-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $banner->order }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 transform transition-all duration-200 hover:scale-105">
                                                Không có thứ tự
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">
                                        @if ($banner->position)
                                            @php
                                                $positionText = str_replace('_', ' ', $banner->position);
                                                $bgColor = 'bg-gray-100';
                                                $textColor = 'text-gray-800';
                                                if ($banner->position === 'homepage') {
                                                    $bgColor = 'bg-indigo-100';
                                                    $textColor = 'text-indigo-800';
                                                } elseif ($banner->position === 'footers') {
                                                    $bgColor = 'bg-pink-100';
                                                    $textColor = 'text-pink-800';
                                                } elseif ($banner->position === 'promotions') {
                                                    $bgColor = 'bg-yellow-100';
                                                    $textColor = 'text-yellow-800';
                                                }
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transform transition-all duration-200 hover:scale-105 {{ $bgColor }} {{ $textColor }}">
                                                {{ $positionText }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 transform transition-all duration-200 hover:scale-105">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $banner->start_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $banner->end_at->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex justify-center space-x-1">
                                            <a href="{{ route('admin.banners.edit', $banner->id) }}"
                                                class="flex items-center justify-center rounded-md hover:bg-accent p-2"
                                                title="Chỉnh sửa">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                    </path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.banners.destroy', $banner->id) }}"
                                                method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="h-8 w-8 p-0 flex items-center justify-center rounded-md hover:bg-accent"
                                                    title="Xóa"
                                                    onclick="dtmodalConfirmDelete({ 
                                                        title: 'Xác nhận xóa banner',
                                                        subtitle: 'Bạn có chắc chắn muốn xóa banner này?',
                                                        message: 'Hành động này không thể hoàn tác.',
                                                        itemName: '{{ $banner->title }}',
                                                        onConfirm: () => this.closest('form').submit()
                                                    })">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                    <td colspan="9" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-4 animate-pulse">
                                            <div class="bg-gray-100 p-6 rounded-full">
                                                <i class="fas fa-image text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-500">Không có banner nào</h3>
                                            <p class="text-sm text-gray-400">Hãy thêm banner đầu tiên của bạn</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination with enhanced styling -->
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Hiển thị
                            <span
                                class="font-medium text-gray-900">{{ ($banners->currentPage() - 1) * $banners->perPage() + 1 }}</span>
                            đến
                            <span
                                class="font-medium text-gray-900">{{ min($banners->currentPage() * $banners->perPage(), $banners->total()) }}</span>
                            của
                            <span class="font-medium text-gray-900">{{ $banners->total() }}</span>
                            mục
                        </div>
                        @if ($banners->lastPage() > 1)
                            <div class="flex items-center space-x-2">
                                @if (!$banners->onFirstPage())
                                    <a href="{{ $banners->previousPageUrl() }}&search={{ request('search') }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                        <i class="fas fa-chevron-left mr-1"></i>
                                        Trước
                                    </a>
                                @endif

                                @php
                                    $start = max(1, $banners->currentPage() - 2);
                                    $end = min($banners->lastPage(), $banners->currentPage() + 2);

                                    if ($start > 1) {
                                        echo '<a href="' .
                                            $banners->url(1) .
                                            '&search=' .
                                            request('search') .
                                            '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">1</a>';
                                        if ($start > 2) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                        }
                                    }
                                @endphp

                                @for ($i = $start; $i <= $end; $i++)
                                    <a href="{{ $banners->url($i) }}&search={{ request('search') }}"
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transform transition-all duration-200 hover:scale-105 {{ $banners->currentPage() == $i ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' }}">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @php
                                    if ($end < $banners->lastPage()) {
                                        if ($end < $banners->lastPage() - 1) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                        }
                                        echo '<a href="' .
                                            $banners->url($banners->lastPage()) .
                                            '&search=' .
                                            request('search') .
                                            '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">' .
                                            $banners->lastPage() .
                                            '</a>';
                                    }
                                @endphp

                                @if ($banners->hasMorePages())
                                    <a href="{{ $banners->nextPageUrl() }}&search={{ request('search') }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                        Tiếp
                                        <i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden form for bulk operations -->
        <form id="bulkStatusForm" action="{{ route('admin.banners.bulk-status-update') }}" method="POST"
            class="hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="ids" id="ids">
            <input type="hidden" name="status" id="status">
        </form>

        <!-- Custom CSS for animations -->
        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideInDown {
                from {
                    transform: translateY(-30px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes slideInUp {
                from {
                    transform: translateY(30px);
                    opacity: 0;
                }

                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.6s ease-out;
            }

            .animate-slideInDown {
                animation: slideInDown 0.6s ease-out;
            }

            .animate-slideInUp {
                animation: slideInUp 0.6s ease-out 0.2s both;
            }

            .dropdown-menu {
                display: none;
            }

            .dropdown-menu.show {
                display: block;
            }
        </style>

        <script>
            // Pusher already initialized above

            function handleSearch(event) {
                const searchValue = event.target.value.trim();
                const currentUrl = new URL(window.location.href);

                if (searchValue) {
                    currentUrl.searchParams.set('search', searchValue);
                } else {
                    currentUrl.searchParams.delete('search');
                }

                if (event.key === 'Enter') {
                    window.location.href = currentUrl.toString();
                }
            }

            function toggleSelectAll() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const rowCheckboxes = document.getElementsByClassName('row-checkbox');

                selectAllCheckbox.checked = !selectAllCheckbox.checked;
                for (let checkbox of rowCheckboxes) {
                    checkbox.checked = selectAllCheckbox.checked;
                }
            }

            document.getElementById('selectAll').addEventListener('change', function() {
                const rowCheckboxes = document.getElementsByClassName('row-checkbox');
                for (let checkbox of rowCheckboxes) {
                    checkbox.checked = this.checked;
                }
            });

            // Dropdown functionality
            document.addEventListener('click', function(event) {
                const dropdownButton = event.target.closest('[data-toggle="dropdown"]');
                const dropdownMenu = document.querySelector('.dropdown-menu');

                if (dropdownButton) {
                    event.preventDefault();
                    dropdownMenu.classList.toggle('show');
                } else {
                    dropdownMenu.classList.remove('show');
                }
            });

            function updateSelectedBannerStatus(status) {
                const selectedIds = [];
                const checkboxes = document.querySelectorAll('.row-checkbox:checked');

                checkboxes.forEach(checkbox => {
                    selectedIds.push(checkbox.value);
                });

                if (selectedIds.length === 0) {
                    alert('Vui lòng chọn ít nhất một banner');
                    return;
                }

                document.getElementById('ids').value = selectedIds.join(',');
                document.getElementById('status').value = status;
                document.getElementById('bulkStatusForm').submit();
            }
        </script>
    @endsection
