@extends('layouts.admin.contentLayoutMaster')
@section('title', 'Quản lý danh mục')
@section('content')
    {{-- @push('scripts')
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            const channel = pusher.subscribe('category-channel');
            channel.bind('category-created', function(data) { location.reload(); });
            channel.bind('category-updated', function(data) { location.reload(); });
            channel.bind('category-deleted', function(data) { location.reload(); });
        </script>
    @endpush --}}

    <div class="min-h-screen bg-gradient-to-br">
        <div class="fade-in flex flex-col gap-4 pb-4 animate-slideInUp delay-200 duration-700 ease-in-out">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex aspect-square w-10 h-10 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                        <i class="fas fa-list text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Quản lý danh mục</h2>
                        <p class="text-muted-foreground">Quản lý danh sách danh mục của bạn</p>
                    </div>
                </div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary flex items-center">
                    <i class="fas fa-plus mr-2"></i> Thêm mới
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-500 hover:shadow-2xl animate-slideInUp delay-200 duration-700 ease-in-out">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Danh sách danh mục</h2>
                </div>

                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" placeholder="Tìm kiếm theo tên danh mục..." id="dataTableSearch"
                                value="{{ request('search') }}" onkeyup="handleSearch(event)"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                        </div>

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
                                <div class="dropdown-menu absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <a href="#" onclick="updateSelectedCategoriestatus(1)"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-200">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        Kích hoạt đã chọn
                                    </a>
                                    <a href="#" onclick="updateSelectedCategoriestatus(0)"
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
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Ảnh</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Tên danh mục</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Trạng thái</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-4 py-3 w-1/12">
                                        <input type="checkbox" class="row-checkbox" value="{{ $category->id }}">
                                    </td>
                                    <td class="px-4 py-3 w-1/12">{{ $category->id }}</td>
                                    <td class="px-4 py-3 w-2/12">
                                        <div class="flex items-center justify-center">
                                            <div class="w-[100px] h-[60px] overflow-hidden">
                                            @php
                                            $imagePath = $category->image ?? 'categories/default-logo.avif';
                                            @endphp
                                            <img src="{{ Storage::disk('s3')->url($imagePath) }}" alt="{{ $category->name }}">
                                    </div>
                                        </div>
                                        </td>
                                    <td class="px-4 py-3 w-3/12 font-medium text-gray-900">{{ $category->name }}</td>
                                    <td class="px-4 py-3 w-2/12">
                                        <form action="{{ route('admin.categories.toggle-status', $category->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transform transition-all duration-200 hover:scale-105 {{ $category->status ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                                @if ($category->status)
                                                    <i class="fas fa-check mr-1"></i> Hiển thị
                                                @else
                                                    <i class="fas fa-times mr-1"></i> Ẩn
                                                @endif
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 w-3/12">
                                        <div class="flex justify-start space-x-2">
                                            <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-sm btn-secondary" title="Xem">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-info" title="Sửa">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" title="Xoá"
                                                    onclick="dtmodalConfirmDelete({
                                                        title: 'Xác nhận xóa danh mục',
                                                        subtitle: 'Bạn có chắc chắn muốn xóa danh mục này?',
                                                        message: 'Hành động này không thể hoàn tác.',
                                                        itemName: '{{ $category->name }}',
                                                        onConfirm: () => this.closest('form').submit()
                                                    })">
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
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">Không có danh mục nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Hiển thị
                            <span
                                class="font-medium text-gray-900">{{ ($categories->currentPage() - 1) * $categories->perPage() + 1 }}</span>
                            đến
                            <span
                                class="font-medium text-gray-900">{{ min($categories->currentPage() * $categories->perPage(), $categories->total()) }}</span>
                            của
                            <span class="font-medium text-gray-900">{{ $categories->total() }}</span>
                            mục
                        </div>
                        @if ($categories->lastPage() > 1)
                            <div class="flex items-center space-x-2">
                                @if (!$categories->onFirstPage())
                                    <a href="{{ $categories->previousPageUrl() }}&search={{ request('search') }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">
                                        <i class="fas fa-chevron-left mr-1"></i>
                                        Trước
                                    </a>
                                @endif

                                @php
                                    $start = max(1, $categories->currentPage() - 2);
                                    $end = min($categories->lastPage(), $categories->currentPage() + 2);

                                    if ($start > 1) {
                                        echo '<a href="' .
                                            $categories->url(1) .
                                            '&search=' .
                                            request('search') .
                                            '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">1</a>';
                                        if ($start > 2) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                        }
                                    }
                                @endphp

                                @for ($i = $start; $i <= $end; $i++)
                                    <a href="{{ $categories->url($i) }}&search={{ request('search') }}"
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transform transition-all duration-200 hover:scale-105 {{ $categories->currentPage() == $i ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50 hover:text-gray-700' }}">
                                        {{ $i }}
                                    </a>
                                @endfor

                                @php
                                    if ($end < $categories->lastPage()) {
                                        if ($end < $categories->lastPage() - 1) {
                                            echo '<span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                                        }
                                        echo '<a href="' .
                                            $categories->url($categories->lastPage()) .
                                            '&search=' .
                                            request('search') .
                                            '" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-700 transform transition-all duration-200 hover:scale-105">' .
                                            $categories->lastPage() .
                                            '</a>';
                                    }
                                @endphp

                                @if ($categories->hasMorePages())
                                    <a href="{{ $categories->nextPageUrl() }}&search={{ request('search') }}"
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

        <form id="bulkStatusForm" action="{{ route('admin.categories.bulk-status-update') }}" method="POST" class="hidden">
            @csrf
            @method('PATCH')
            <input type="hidden" name="category_ids" id="ids">
            <input type="hidden" name="status" id="status">
        </form>
    </div>
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

            function updateSelectedCategoriestatus(status) {
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