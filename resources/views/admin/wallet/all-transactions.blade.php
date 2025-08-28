@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Tất Cả Giao Dịch Ví')
@section('description', 'Xem và quản lý tất cả giao dịch ví tiền trong hệ thống')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Tất Cả Giao Dịch Ví</h1>
                <p class="text-gray-600 text-lg">Xem và quản lý tất cả giao dịch ví tiền trong hệ thống</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.wallet.transactions.export', request()->all()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Xuất CSV
                </a>
                <a href="{{ route('admin.wallet.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Tổng giao dịch</p>
                    <p class="text-lg font-bold text-gray-900">{{ number_format($stats['total_count']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Hoàn thành</p>
                    <p class="text-lg font-bold text-green-600">{{ number_format($stats['completed_count']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                    <i class="fas fa-clock text-orange-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Chờ xử lý</p>
                    <p class="text-lg font-bold text-orange-600">{{ number_format($stats['pending_count']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg mr-3">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Thất bại</p>
                    <p class="text-lg font-bold text-red-600">{{ number_format($stats['failed_count']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                    <i class="fas fa-money-bill-wave text-purple-600"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Tổng giá trị</p>
                    <p class="text-sm font-bold text-purple-600">{{ number_format($stats['total_amount'], 0, ',', '.') }} VND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Bộ Lọc Nâng Cao</h3>
            <button onclick="toggleFilters()" id="filter-toggle" class="text-blue-600 hover:text-blue-700">
                <i class="fas fa-filter mr-1"></i>
                <span id="filter-text">Hiện bộ lọc</span>
            </button>
        </div>

        <div id="filters-panel" class="hidden">
            <form method="GET" action="{{ route('admin.wallet.transactions.all') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loại giao dịch</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Nạp tiền</option>
                        <option value="withdraw" {{ request('type') == 'withdraw' ? 'selected' : '' }}>Rút tiền</option>
                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Thanh toán</option>
                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Hoàn tiền</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Amount From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số tiền từ</label>
                    <input type="number" name="amount_from" value="{{ request('amount_from') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Amount To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số tiền đến</label>
                    <input type="number" name="amount_to" value="{{ request('amount_to') }}" placeholder="10000000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã GD, tên, email..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- User ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User ID</label>
                    <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="ID user cụ thể" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filter Actions -->
                <div class="md:col-span-2 lg:col-span-4 flex items-center space-x-4 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Áp dụng bộ lọc
                    </button>
                    <a href="{{ route('admin.wallet.transactions.all') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Xóa bộ lọc
                    </a>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600">Quick filters:</label>
                        <button type="button" onclick="setQuickFilter('today')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">Hôm nay</button>
                        <button type="button" onclick="setQuickFilter('week')" class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">Tuần này</button>
                        <button type="button" onclick="setQuickFilter('month')" class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded">Tháng này</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Danh Sách Giao Dịch</h3>
        </div>

        @if($transactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            #{{ $transaction->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeConfig = [
                                    'deposit' => ['icon' => 'fa-plus', 'class' => 'bg-green-100 text-green-800', 'text' => 'Nạp tiền'],
                                    'withdraw' => ['icon' => 'fa-minus', 'class' => 'bg-red-100 text-red-800', 'text' => 'Rút tiền'],
                                    'payment' => ['icon' => 'fa-credit-card', 'class' => 'bg-blue-100 text-blue-800', 'text' => 'Thanh toán'],
                                    'refund' => ['icon' => 'fa-undo', 'class' => 'bg-purple-100 text-purple-800', 'text' => 'Hoàn tiền']
                                ];
                                $config = $typeConfig[$transaction->type] ?? ['icon' => 'fa-question', 'class' => 'bg-gray-100 text-gray-800', 'text' => 'Khác'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1"></i>
                                {{ $config['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold {{ $transaction->type == 'deposit' || $transaction->type == 'refund' ? 'text-green-600' : 'text-red-600' }}">
                                {{ in_array($transaction->type, ['deposit', 'refund']) ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }} VND
                            </div>
                            <div class="text-xs text-gray-500 font-mono">{{ $transaction->transaction_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'pending' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fa-clock', 'text' => 'Chờ xử lý'],
                                    'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check', 'text' => 'Hoàn thành'],
                                    'failed' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times', 'text' => 'Thất bại'],
                                    'cancelled' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-ban', 'text' => 'Đã hủy'],
                                    'expired' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-hourglass-end', 'text' => 'Hết hạn']
                                ];
                                $statusConf = $statusConfig[$transaction->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question', 'text' => 'Không xác định'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConf['class'] }}">
                                <i class="fas {{ $statusConf['icon'] }} mr-1"></i>
                                {{ $statusConf['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewTransaction({{ $transaction->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transactions->appends(request()->all())->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy giao dịch nào</h3>
            <p class="text-gray-500">Thử điều chỉnh bộ lọc để tìm kiếm giao dịch.</p>
        </div>
        @endif
    </div>
</div>

<!-- Transaction Detail Modal -->
<div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-receipt text-blue-600 text-lg"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Chi Tiết Giao Dịch</h3>
            </div>
            <button onclick="closeTransactionModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
            <div id="transaction-details" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeTransactionModal()" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
let filtersVisible = false;

function toggleFilters() {
    const panel = document.getElementById('filters-panel');
    const toggleText = document.getElementById('filter-text');
    
    filtersVisible = !filtersVisible;
    
    if (filtersVisible) {
        panel.classList.remove('hidden');
        toggleText.textContent = 'Ẩn bộ lọc';
    } else {
        panel.classList.add('hidden');
        toggleText.textContent = 'Hiện bộ lọc';
    }
}

function setQuickFilter(period) {
    const today = new Date();
    let dateFrom = new Date();
    
    switch(period) {
        case 'today':
            dateFrom = new Date();
            break;
        case 'week':
            dateFrom.setDate(today.getDate() - 7);
            break;
        case 'month':
            dateFrom.setDate(today.getDate() - 30);
            break;
    }
    
    document.querySelector('input[name="date_from"]').value = dateFrom.toISOString().split('T')[0];
    document.querySelector('input[name="date_to"]').value = today.toISOString().split('T')[0];
}

function viewTransaction(id) {
    const modal = document.getElementById('transactionModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    document.getElementById('transaction-details').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i><p class="mt-2 text-gray-600">Đang tải thông tin giao dịch...</p></div>';
    
    // Find transaction data from the table
    const rows = document.querySelectorAll('tbody tr');
    let transactionRow = null;
    
    for (let row of rows) {
        const idCell = row.querySelector('td:first-child');
        if (idCell && idCell.textContent.trim() === `#${id}`) {
            transactionRow = row;
            break;
        }
    }
    
    if (!transactionRow) {
        document.getElementById('transaction-details').innerHTML = '<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Không tìm thấy thông tin giao dịch</p></div>';
        return;
    }
    
    // Extract data from the row
    const cells = transactionRow.querySelectorAll('td');
    const transactionId = cells[0].textContent.trim();
    const userName = cells[1].querySelector('.text-sm.font-medium').textContent.trim();
    const userEmail = cells[1].querySelector('.text-sm.text-gray-500').textContent.trim();
    const typeElement = cells[2].querySelector('span');
    const amountElement = cells[3].querySelector('.text-sm.font-semibold');
    const transactionCode = cells[3].querySelector('.text-xs.font-mono').textContent.trim();
    const statusElement = cells[4].querySelector('span');
    const timeElement = cells[5].querySelector('div:first-child').textContent.trim();
    const timeAgo = cells[5].querySelector('.text-xs.text-gray-500').textContent.trim();
    
    // Generate detailed view
    setTimeout(() => {
        document.getElementById('transaction-details').innerHTML = `
            <div class="space-y-6">
                <!-- Summary Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full mr-4">
                                <i class="fas fa-receipt text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-blue-800">${transactionId}</h3>
                                <p class="text-blue-600">Mã giao dịch: ${transactionCode}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            ${statusElement.outerHTML}
                        </div>
                    </div>
                </div>

                <!-- Main Info Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Transaction Details -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <i class="fas fa-info-circle text-green-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Thông Tin Giao Dịch</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600 font-medium">Loại giao dịch:</span>
                                ${typeElement.outerHTML}
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600 font-medium">Số tiền:</span>
                                <span class="text-lg font-bold ${amountElement.className}">${amountElement.textContent}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600 font-medium">Thời gian tạo:</span>
                                <div class="text-right">
                                    <div class="font-medium">${timeElement}</div>
                                    <div class="text-sm text-gray-500">${timeAgo}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900">Thông Tin Khách Hàng</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">${userName.charAt(0).toUpperCase()}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="text-lg font-semibold text-gray-900">${userName}</div>
                                    <div class="text-gray-600 text-sm">${userEmail}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="bg-gray-50 rounded-xl p-6">
                    <div class="text-center text-gray-600">
                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                        <p class="text-sm">Đây là trang tổng quan chỉ hiển thị thông tin cơ bản của giao dịch.</p>
                        <p class="text-sm mt-1">Để xem chi tiết đầy đủ và thực hiện thao tác, vui lòng truy cập trang quản lý riêng biệt.</p>
                    </div>
                </div>
            </div>
        `;
    }, 800);
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
    document.getElementById('transactionModal').classList.remove('flex');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('transactionModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeTransactionModal();
            }
        });
    }
});



function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white transition-all duration-300 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 
        'bg-blue-600'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info'} mr-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Auto-show filters if any filter is applied
@if(request()->hasAny(['type', 'status', 'date_from', 'date_to', 'amount_from', 'amount_to', 'search', 'user_id']))
document.addEventListener('DOMContentLoaded', function() {
    toggleFilters();
});
@endif
</script>

<style>
.transition-colors {
    transition-property: color, background-color, border-color;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

table {
    table-layout: auto;
}

.font-mono {
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
}

/* Modal animations */
#transactionModal {
    transition: opacity 0.3s ease-out;
}

#transactionModal.hidden {
    opacity: 0;
    pointer-events: none;
}

#transactionModal.flex {
    opacity: 1;
    pointer-events: auto;
}

#transactionModal .bg-white {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

#transactionModal.hidden .bg-white {
    transform: scale(0.95) translateY(-10px);
    opacity: 0;
}

#transactionModal.flex .bg-white {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Custom scrollbar for modal content */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection
