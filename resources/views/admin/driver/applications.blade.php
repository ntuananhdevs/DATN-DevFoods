@extends('layouts/admin/contentLayoutMaster')
@section('content')
    <div class="data-table-wrapper">
        <!-- Header chính -->
        <div class="data-table-main-header">
            <div class="data-table-brand">
                <div class="data-table-logo">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h1 class="data-table-title">Quản lý đơn đăng ký tài xế</h1>
            </div>
            <div class="data-table-header-actions">
                <div class="dropdown d-inline">
                    <button class="data-table-btn data-table-btn-outline dropdown-toggle" type="button" id="exportDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download"></i> Xuất
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="{{ route('admin.drivers.applications.export', ['type' => 'excel']) }}">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.drivers.applications.export', ['type' => 'pdf']) }}">
                            <i class="fas fa-file-pdf"></i> Xuất PDF
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.drivers.applications.export', ['type' => 'csv']) }}">
                            <i class="fas fa-file-csv"></i> Xuất CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card chứa bảng -->
        <div class="data-table-card">
            <!-- Tiêu đề bảng -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Đơn đăng ký đang chờ xử lý</h2>
            </div>

            <!-- Thanh công cụ -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" name="pending_search" placeholder="Tìm kiếm theo tên, số điện thoại, biển số xe..." 
                           value="{{ $pendingSearch ?? '' }}" id="pendingTableSearch">
                </div>
            </div>

            <!-- Container bảng -->
            <div class="data-table-container">
                <table class="data-table" id="dataTable">
                    <thead>
                        <tr>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th data-sort="name">
                                Họ và tên <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="phone">
                                Số điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="license">
                                Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Ngày nộp đơn <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="date">
                                Ngày cập nhật <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTableBody">
                        @forelse($pendingApplications as $application)
                            <tr>
                                <td>
                                    <div class="data-table-id">
                                        {{ $application->id }}
                                    </div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $application->full_name }}</div>
                                </td>
                                <td>{{ $application->phone_number }}</td>
                                <td>{{ $application->license_plate }}</td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.drivers.applications.show', ['application' => $application->id]) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="data-table-empty" id="pendingTableEmpty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <h3>Không có đơn đăng ký nào đang chờ xử lý</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang và thông tin -->
            <div class="data-table-footer" id="pendingPagination">
                <div class="data-table-pagination-info">
                    Hiển thị <span
                        id="pendingStartRecord">{{ ($pendingApplications->currentPage() - 1) * $pendingApplications->perPage() + 1 }}</span>
                    đến <span
                        id="pendingEndRecord">{{ min($pendingApplications->currentPage() * $pendingApplications->perPage(), $pendingApplications->total()) }}</span>
                    của <span id="pendingTotalRecords">{{ $pendingApplications->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$pendingApplications->onFirstPage())
                        <a href="{{ $pendingApplications->previousPageUrl() }}" class="data-table-pagination-btn"
                            id="pendingPrevBtn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @for ($i = 1; $i <= $pendingApplications->lastPage(); $i++)
                        <a href="{{ $pendingApplications->url($i) }}"
                            class="data-table-pagination-btn {{ $pendingApplications->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($pendingApplications->hasMorePages())
                        <a href="{{ $pendingApplications->nextPageUrl() }}" class="data-table-pagination-btn"
                            id="pendingNextBtn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card chứa bảng đã xử lý -->
        <div class="data-table-card mt-4">
            <!-- Tiêu đề bảng -->
            <div class="data-table-header">
                <h2 class="data-table-card-title">Đơn đăng ký đã xử lý</h2>
            </div>

            <!-- Thanh công cụ cho bảng đã xử lý -->
            <div class="data-table-controls">
                <div class="data-table-search">
                    <i class="fas fa-search data-table-search-icon"></i>
                    <input type="text" name="processed_search" placeholder="Tìm kiếm theo tên, số điện thoại, biển số xe..." 
                           value="{{ $processedSearch ?? '' }}" id="processedTableSearch">
                </div>
            </div>

            <!-- Container bảng -->
            <div class="data-table-container">
                <table class="data-table" id="processedTable">
                    <thead>
                        <tr>
                            <th data-sort="id" class="active-sort">
                                ID <i class="fas fa-arrow-up data-table-sort-icon"></i>
                            </th>
                            <th data-sort="name">
                                Họ và tên <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="phone">
                                Số điện thoại <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="license">
                                Biển số xe <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="status">
                                Trạng thái <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="created_date">
                                Ngày nộp đơn <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th data-sort="updated_date">
                                Ngày cập nhật <i class="fas fa-sort data-table-sort-icon"></i>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="processedTableBody">
                        @forelse($processedApplications as $application)
                            <tr>
                                <td>
                                    <div class="data-table-id">{{ $application->id }}</div>
                                </td>
                                <td>
                                    <div class="data-table-product-name">{{ $application->full_name }}</div>
                                </td>
                                <td>{{ $application->phone_number }}</td>
                                <td>{{ $application->license_plate }}</td>
                                <td>
                                    <span
                                        class="data-table-status {{ $application->status === 'approved' ? 'data-table-status-success' : 'data-table-status-failed' }}">
                                        <i
                                            class="fas fa-{{ $application->status === 'approved' ? 'check' : 'times' }}"></i>
                                        {{ $application->status === 'approved' ? 'Đã duyệt' : 'Đã từ chối' }}
                                    </span>
                                </td>
                                <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $application->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="data-table-action-buttons">
                                        <a href="{{ route('admin.drivers.applications.show', $application) }}"
                                            class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="data-table-empty" id="processedTableEmpty">
                                        <div class="data-table-empty-icon">
                                            <i class="fas fa-inbox"></i>
                                        </div>
                                        <h3>Không có đơn đăng ký nào đã xử lý</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang và thông tin -->
            <div class="data-table-footer" id="processedPagination">
                <div class="data-table-pagination-info">
                    Hiển thị
                    <span id="processedStartRecord">{{ ($processedApplications->currentPage() - 1) * $processedApplications->perPage() + 1 }}</span>
                    đến
                    <span id="processedEndRecord">{{ min($processedApplications->currentPage() * $processedApplications->perPage(), $processedApplications->total()) }}</span>
                    của <span id="processedTotalRecords">{{ $processedApplications->total() }}</span> mục
                </div>
                <div class="data-table-pagination-controls">
                    @if (!$processedApplications->onFirstPage())
                        <a href="{{ $processedApplications->previousPageUrl() }}" class="data-table-pagination-btn">
                            <i class="fas fa-chevron-left"></i> Trước
                        </a>
                    @endif

                    @for ($i = 1; $i <= $processedApplications->lastPage(); $i++)
                        <a href="{{ $processedApplications->url($i) }}"
                            class="data-table-pagination-btn {{ $processedApplications->currentPage() == $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($processedApplications->hasMorePages())
                        <a href="{{ $processedApplications->nextPageUrl() }}" class="data-table-pagination-btn">
                            Tiếp <i class="fas fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Khai báo các biến
    const pendingSearchInput = document.getElementById('pendingTableSearch');
    const processedSearchInput = document.getElementById('processedTableSearch');
    let pendingSearchTimeout, processedSearchTimeout;
    const baseUrl = "{{ route('admin.drivers.applications.index') }}";

    // Xử lý tìm kiếm cho bảng đơn chờ xử lý
    pendingSearchInput.addEventListener('input', function() {
        clearTimeout(pendingSearchTimeout);
        pendingSearchTimeout = setTimeout(() => {
            const searchTerm = pendingSearchInput.value.trim();
            updateUrlAndFetchData('pending_search', searchTerm);
        }, 500);
    });

    // Xử lý tìm kiếm cho bảng đơn đã xử lý
    processedSearchInput.addEventListener('input', function() {
        clearTimeout(processedSearchTimeout);
        processedSearchTimeout = setTimeout(() => {
            const searchTerm = processedSearchInput.value.trim();
            updateUrlAndFetchData('processed_search', searchTerm);
        }, 500);
    });

    // Hàm cập nhật URL và lấy dữ liệu
    function updateUrlAndFetchData(searchParam, searchValue) {
        // Lấy đường dẫn URL hiện tại
        const url = new URL(window.location.href);
        
        // Cập nhật hoặc thêm tham số tìm kiếm
        if (searchValue) {
            url.searchParams.set(searchParam, searchValue);
        } else {
            url.searchParams.delete(searchParam);
        }
        
        // Cập nhật URL trong trình duyệt mà không tải lại trang
        window.history.pushState({}, '', url);
        
        // Gọi AJAX để lấy dữ liệu
        fetchData(url.toString());
    }

    // Hàm gọi AJAX để lấy dữ liệu
    function fetchData(url) {
        // Thêm header X-Requested-With để Laravel nhận biết đây là request AJAX
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Cập nhật cả hai bảng với dữ liệu mới
            updatePendingTable(data.pendingApplications);
            updateProcessedTable(data.processedApplications);
        })
        .catch(error => {
            console.error('Lỗi khi tải dữ liệu:', error);
        });
    }

    // Hàm cập nhật bảng đơn chờ xử lý
    function updatePendingTable(data) {
        const tableBody = document.getElementById('pendingTableBody');
        
        // Xóa dữ liệu hiện tại
        tableBody.innerHTML = '';
        
        if (data.data.length === 0) {
            // Hiển thị thông báo không có dữ liệu
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="data-table-empty" id="pendingTableEmpty">
                            <div class="data-table-empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h3>Không có đơn đăng ký nào đang chờ xử lý</h3>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            // Thêm dữ liệu mới
            data.data.forEach(application => {
                const createdDate = formatDate(application.created_at);
                const updatedDate = formatDate(application.updated_at);
                
                tableBody.innerHTML += `
                    <tr>
                        <td>
                            <div class="data-table-id">${application.id}</div>
                        </td>
                        <td>
                            <div class="data-table-product-name">${application.full_name}</div>
                        </td>
                        <td>${application.phone_number}</td>
                        <td>${application.license_plate}</td>
                        <td>${createdDate}</td>
                        <td>${updatedDate}</td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="${baseUrl}/show/${application.id}"
                                    class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        // Cập nhật thông tin phân trang
        document.getElementById('pendingStartRecord').innerText = data.from || 0;
        document.getElementById('pendingEndRecord').innerText = data.to || 0;
        document.getElementById('pendingTotalRecords').innerText = data.total;
        
        // Cập nhật các nút phân trang
        updatePagination(data, 'pending');
    }

    // Hàm cập nhật bảng đơn đã xử lý
    function updateProcessedTable(data) {
        const tableBody = document.getElementById('processedTableBody');
        
        // Xóa dữ liệu hiện tại
        tableBody.innerHTML = '';
        
        if (data.data.length === 0) {
            // Hiển thị thông báo không có dữ liệu
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="data-table-empty" id="processedTableEmpty">
                            <div class="data-table-empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h3>Không có đơn đăng ký nào đã xử lý</h3>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            // Thêm dữ liệu mới
            data.data.forEach(application => {
                const statusClass = application.status === 'approved' ? 'data-table-status-success' : 'data-table-status-failed';
                const statusIcon = application.status === 'approved' ? 'check' : 'times';
                const statusText = application.status === 'approved' ? 'Đã duyệt' : 'Đã từ chối';
                const createdDate = formatDate(application.created_at);
                const updatedDate = formatDate(application.updated_at);
                
                tableBody.innerHTML += `
                    <tr>
                        <td>
                            <div class="data-table-id">${application.id}</div>
                        </td>
                        <td>
                            <div class="data-table-product-name">${application.full_name}</div>
                        </td>
                        <td>${application.phone_number}</td>
                        <td>${application.license_plate}</td>
                        <td>
                            <span class="data-table-status ${statusClass}">
                                <i class="fas fa-${statusIcon}"></i>
                                ${statusText}
                            </span>
                        </td>
                        <td>${createdDate}</td>
                        <td>${updatedDate}</td>
                        <td>
                            <div class="data-table-action-buttons">
                                <a href="${baseUrl}/show/${application.id}"
                                    class="data-table-action-btn data-table-tooltip" data-tooltip="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        // Cập nhật thông tin phân trang
        document.getElementById('processedStartRecord').innerText = data.from || 0;
        document.getElementById('processedEndRecord').innerText = data.to || 0;
        document.getElementById('processedTotalRecords').innerText = data.total;
        
        // Cập nhật các nút phân trang
        updatePagination(data, 'processed');
    }

    // Hàm cập nhật phân trang
    function updatePagination(data, type) {
        const paginationControls = document.querySelector(`#${type}Pagination .data-table-pagination-controls`);
        if (!paginationControls) return;
        
        let paginationHTML = '';
        
        // Nút Trước
        if (data.current_page > 1) {
            const prevPageUrl = new URL(window.location.href);
            prevPageUrl.searchParams.set(`${type}_page`, data.current_page - 1);
            paginationHTML += `
                <a href="${prevPageUrl.toString()}" class="data-table-pagination-btn" data-page="${data.current_page - 1}">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
            `;
        }
        
        // Các nút số trang
        for (let i = 1; i <= data.last_page; i++) {
            const pageUrl = new URL(window.location.href);
            pageUrl.searchParams.set(`${type}_page`, i);
            const activeClass = i === data.current_page ? 'active' : '';
            
            paginationHTML += `
                <a href="${pageUrl.toString()}" class="data-table-pagination-btn ${activeClass}" data-page="${i}">
                    ${i}
                </a>
            `;
        }
        
        // Nút Tiếp
        if (data.current_page < data.last_page) {
            const nextPageUrl = new URL(window.location.href);
            nextPageUrl.searchParams.set(`${type}_page`, data.current_page + 1);
            paginationHTML += `
                <a href="${nextPageUrl.toString()}" class="data-table-pagination-btn" data-page="${data.current_page + 1}">
                    Tiếp <i class="fas fa-chevron-right"></i>
                </a>
            `;
        }
        
        paginationControls.innerHTML = paginationHTML;
        
        // Thêm sự kiện click cho các nút phân trang
        paginationControls.querySelectorAll('.data-table-pagination-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                const pageUrl = new URL(window.location.href);
                pageUrl.searchParams.set(`${type}_page`, page);
                window.history.pushState({}, '', pageUrl);
                fetchData(pageUrl.toString());
            });
        });
    }

    // Hàm định dạng ngày
    function formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }
});
</script>
    @endpush
@endsection
