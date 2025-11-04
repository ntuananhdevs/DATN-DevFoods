@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Lịch Sử Nạp Tiền')
@section('description', 'Xem lịch sử tất cả các giao dịch nạp tiền')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Lịch Sử Nạp Tiền</h1>
                <p class="text-gray-600 text-lg">Xem lịch sử tất cả các giao dịch nạp tiền trong hệ thống</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.wallet.transactions.all') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-list mr-2"></i>
                    Tất cả giao dịch
                </a>
                <a href="{{ route('admin.wallet.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng giao dịch</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($deposits->total()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Thành công</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($deposits->where('status', 'completed')->count()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Chờ xử lý</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($deposits->where('status', 'pending')->count()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng số tiền</p>
                    <p class="text-lg font-bold text-purple-600">{{ number_format($deposits->sum('amount'), 0, ',', '.') }} VND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('admin.wallet.deposits.history') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Thành công</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                </select>
            </div>

            <!-- Payment Method Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phương thức</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả</option>
                    <option value="vnpay" {{ request('payment_method') == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                    <option value="momo" {{ request('payment_method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                    <option value="banking" {{ request('payment_method') == 'banking' ? 'selected' : '' }}>Banking</option>
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

            <!-- Amount Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số tiền tối thiểu</label>
                <input type="number" name="amount_from" value="{{ request('amount_from') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Filter Actions -->
            <div class="md:col-span-5 flex items-center space-x-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Áp dụng bộ lọc
                </button>
                <a href="{{ route('admin.wallet.deposits.history') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Xóa bộ lọc
                </a>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Quick:</label>
                    <button type="button" onclick="setQuickFilter('today')" class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded">Hôm nay</button>
                    <button type="button" onclick="setQuickFilter('week')" class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded">Tuần này</button>
                    <button type="button" onclick="setQuickFilter('month')" class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded">Tháng này</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Deposits Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Lịch Sử Nạp Tiền</h3>
        </div>

        @if($deposits->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phương thức</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($deposits as $deposit)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            #{{ $deposit->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium">
                                        @if($deposit->user)
                                        <a href="{{ route('admin.users.show', $deposit->user->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 inline-flex items-center">
                                            {{ $deposit->user->name }}
                                            <i class="fas fa-external-link-alt ml-1 text-xs opacity-60"></i>
                                        </a>
                                        @else
                                        <span class="text-gray-900">N/A</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $deposit->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-green-600">+{{ number_format($deposit->amount, 0, ',', '.') }} VND</div>
                            <div class="text-sm text-gray-500 font-mono">{{ $deposit->transaction_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $methodConfig = [
                                    'vnpay' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'fa-credit-card', 'text' => 'VNPay'],
                                    'momo' => ['class' => 'bg-pink-100 text-pink-800', 'icon' => 'fa-mobile-alt', 'text' => 'MoMo'],
                                    'banking' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-university', 'text' => 'Banking']
                                ];
                                $method = $methodConfig[$deposit->payment_method] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question', 'text' => $deposit->payment_method ?: 'N/A'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $method['class'] }}">
                                <i class="fas {{ $method['icon'] }} mr-1"></i>
                                {{ $method['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check', 'text' => 'Thành công'],
                                    'pending' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fa-clock', 'text' => 'Chờ xử lý'],
                                    'failed' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times', 'text' => 'Thất bại'],
                                    'cancelled' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-ban', 'text' => 'Đã hủy'],
                                    'expired' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'fa-hourglass-end', 'text' => 'Hết hạn']
                                ];
                                $status = $statusConfig[$deposit->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question', 'text' => 'Không xác định'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                <i class="fas {{ $status['icon'] }} mr-1"></i>
                                {{ $status['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div><strong>Tạo:</strong> {{ $deposit->created_at->format('d/m/Y H:i') }}</div>
                            @if($deposit->processed_at)
                            <div><strong>Xử lý:</strong> {{ $deposit->processed_at->format('d/m/Y H:i') }}</div>
                            @endif
                            @if($deposit->expires_at)
                            <div class="text-xs {{ $deposit->expires_at < now() ? 'text-red-500' : 'text-gray-500' }}">
                                Hết hạn: {{ $deposit->expires_at->format('d/m/Y H:i') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewDeposit({{ $deposit->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết">
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
            {{ $deposits->appends(request()->all())->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có lịch sử nạp tiền</h3>
            <p class="text-gray-500">Chưa có giao dịch nạp tiền nào trong hệ thống.</p>
        </div>
        @endif
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-plus-circle text-blue-600 text-lg"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">Chi Tiết Giao Dịch Nạp Tiền</h3>
            </div>
            <button onclick="closeViewModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-all duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
            <div id="deposit-details" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeViewModal()" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Đóng
            </button>
        </div>
    </div>
</div>

<!-- Hidden deposit data for JavaScript -->
<script type="application/json" id="deposit-data">
@php
try {
    $depositData = [];
    foreach($deposits as $d) {
        $depositData[$d->id] = [
            'id' => $d->id,
            'transaction_code' => $d->transaction_code,
            'amount' => floatval($d->amount),
            'status' => $d->status,
            'payment_method' => $d->payment_method,
            'created_at' => $d->created_at->format('d/m/Y H:i:s'),
            'created_at_human' => $d->created_at->diffForHumans(),
            'processed_at' => $d->processed_at ? $d->processed_at->format('d/m/Y H:i:s') : null,
            'expires_at' => $d->expires_at ? $d->expires_at->format('d/m/Y H:i:s') : null,
            'description' => $d->description,
            'metadata' => $d->metadata,
            'user' => $d->user ? [
                'id' => $d->user->id,
                'name' => $d->user->name,
                'email' => $d->user->email,
                'balance' => floatval($d->user->balance)
            ] : null
        ];
    }
    echo json_encode($depositData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
} catch (Exception $e) {
    echo '{}';
}
@endphp
</script>

<script>
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

// Get deposit data from embedded script
function getDepositData() {
    try {
        const dataScript = document.getElementById('deposit-data');
        if (!dataScript) {
            console.error('Deposit data script not found');
            return {};
        }
        const data = JSON.parse(dataScript.textContent);
        return data;
    } catch (e) {
        console.error('Error parsing deposit data:', e);
        return {};
    }
}

function viewDeposit(id) {
    const viewModal = document.getElementById('viewModal');
    if (viewModal) {
        viewModal.classList.remove('hidden');
        viewModal.classList.add('flex');
    } else {
        console.error('View modal not found');
        return;
    }
    
    // Load deposit details from embedded data
    const depositData = getDepositData();
    const deposit = depositData[id];
    
    if (!deposit) {
        document.getElementById('deposit-details').innerHTML = '<div class="text-center py-4 text-red-600">Không tìm thấy thông tin giao dịch</div>';
        return;
    }
    
    // Parse payment method info
    let paymentInfo = {};
    try {
        paymentInfo = typeof deposit.metadata === 'string' ? JSON.parse(deposit.metadata) : (deposit.metadata || {});
    } catch (e) {
        console.error('Error parsing payment info:', e);
        paymentInfo = {};
    }
    
    // Generate HTML content
    try {
        const html = generateDepositDetailHTML(deposit, paymentInfo);
        document.getElementById('deposit-details').innerHTML = html;
    } catch (e) {
        console.error('Error generating HTML:', e);
        document.getElementById('deposit-details').innerHTML = '<div class="text-center py-4 text-red-600">Lỗi hiển thị chi tiết</div>';
    }
}

function generateDepositDetailHTML(deposit, paymentInfo) {
    // Status display configuration
    const statusConfig = {
        'pending': { class: 'bg-orange-100 text-orange-800 border-orange-200', icon: 'fa-clock', text: 'Chờ xử lý' },
        'completed': { class: 'bg-green-100 text-green-800 border-green-200', icon: 'fa-check-circle', text: 'Thành công' },
        'failed': { class: 'bg-red-100 text-red-800 border-red-200', icon: 'fa-times-circle', text: 'Thất bại' },
        'cancelled': { class: 'bg-gray-100 text-gray-800 border-gray-200', icon: 'fa-ban', text: 'Đã hủy' },
        'expired': { class: 'bg-yellow-100 text-yellow-800 border-yellow-200', icon: 'fa-hourglass-end', text: 'Hết hạn' }
    };
    const config = statusConfig[deposit.status] || { class: 'bg-gray-100 text-gray-800 border-gray-200', icon: 'fa-question', text: 'Không xác định' };
    
    // Payment method configuration
    const methodConfig = {
        'vnpay': { class: 'bg-blue-100 text-blue-800 border-blue-200', icon: 'fa-credit-card', text: 'VNPay' },
        'momo': { class: 'bg-pink-100 text-pink-800 border-pink-200', icon: 'fa-mobile-alt', text: 'MoMo' },
        'banking': { class: 'bg-green-100 text-green-800 border-green-200', icon: 'fa-university', text: 'Banking' }
    };
    const methodConf = methodConfig[deposit.payment_method] || { class: 'bg-gray-100 text-gray-800 border-gray-200', icon: 'fa-question', text: deposit.payment_method || 'N/A' };
    
    return `
<div class="space-y-8">
    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full mr-4">
                    <i class="fas fa-plus-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-green-800">+${formatNumber(deposit.amount)} VND</h3>
                    <p class="text-green-600">Giao dịch nạp tiền #${deposit.id}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border ${config.class}">
                    <i class="fas ${config.icon} mr-2"></i>
                    ${config.text}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Transaction Details -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Chi Tiết Giao Dịch</h4>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Mã giao dịch:</span>
                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">${deposit.transaction_code}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Phương thức:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border ${methodConf.class}">
                        <i class="fas ${methodConf.icon} mr-2"></i>
                        ${methodConf.text}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Mô tả:</span>
                    <span class="text-sm text-gray-800 text-right max-w-xs">${deposit.description || 'N/A'}</span>
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
            ${deposit.user ? `
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-lg">${deposit.user.name.charAt(0).toUpperCase()}</span>
                    </div>
                    <div class="flex-1">
                        <a href="{{ url('/admin/users/show') }}/${deposit.user.id}" 
                           class="text-lg font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 inline-flex items-center">
                            ${deposit.user.name}
                            <i class="fas fa-external-link-alt ml-2 text-sm opacity-60"></i>
                        </a>
                        <p class="text-gray-600 text-sm">${deposit.user.email}</p>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 font-medium">Số dư hiện tại:</span>
                        <span class="text-xl font-bold ${deposit.user.balance >= 0 ? 'text-green-600' : 'text-red-600'}">
                            ${formatNumber(deposit.user.balance)} VND
                        </span>
                    </div>
                </div>
            </div>
            ` : `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-user-slash text-4xl mb-4"></i>
                <p class="text-lg">Thông tin người dùng không có sẵn</p>
            </div>
            `}
        </div>
    </div>

    <!-- Payment Information & Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Payment Information -->
        ${paymentInfo && Object.keys(paymentInfo).length > 0 ? `
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                    <i class="fas fa-credit-card text-indigo-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Thông Tin Thanh Toán</h4>
            </div>
            <div class="space-y-4">
                ${paymentInfo.vnp_TransactionNo ? `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Mã GD VNPay:</span>
                    <span class="font-mono text-sm bg-indigo-50 px-2 py-1 rounded">${paymentInfo.vnp_TransactionNo}</span>
                </div>
                ` : ''}
                ${paymentInfo.vnp_BankCode ? `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Ngân hàng:</span>
                    <span class="font-medium text-indigo-600">${paymentInfo.vnp_BankCode}</span>
                </div>
                ` : ''}
                ${paymentInfo.vnp_PayDate ? `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Thời gian thanh toán:</span>
                    <span class="font-medium">${paymentInfo.vnp_PayDate}</span>
                </div>
                ` : ''}
            </div>
        </div>
        ` : '<div></div>'}

        <!-- Timeline -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex items-center mb-4">
                <div class="p-2 bg-amber-100 rounded-lg mr-3">
                    <i class="fas fa-history text-amber-600"></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900">Lịch Sử Giao Dịch</h4>
            </div>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full mt-1 mr-4"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Tạo yêu cầu nạp tiền</div>
                        <div class="text-sm text-gray-600">${deposit.created_at}</div>
                        <div class="text-xs text-gray-400 mt-1">${deposit.created_at_human}</div>
                    </div>
                </div>
                
                ${deposit.processed_at ? `
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-3 h-3 ${deposit.status === 'completed' ? 'bg-green-500' : 'bg-red-500'} rounded-full mt-1 mr-4"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${deposit.status === 'completed' ? 'Xử lý thành công' : 'Xử lý thất bại'}</div>
                        <div class="text-sm text-gray-600">${deposit.processed_at}</div>
                    </div>
                </div>
                ` : ''}

                ${deposit.expires_at ? `
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-3 h-3 bg-yellow-500 rounded-full mt-1 mr-4"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Thời gian hết hạn</div>
                        <div class="text-sm text-gray-600">${deposit.expires_at}</div>
                    </div>
                </div>
                ` : ''}
            </div>
        </div>
    </div>

    <!-- Status Message -->
    ${deposit.status === 'completed' ? `
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-full mr-4">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-green-800 text-lg">Giao Dịch Thành Công</h4>
                <p class="text-green-700">Số tiền đã được nạp vào ví của khách hàng thành công</p>
            </div>
        </div>
    </div>
    ` : ''}
    
    ${deposit.status === 'failed' ? `
    <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 rounded-full mr-4">
                <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-red-800 text-lg">Giao Dịch Thất Bại</h4>
                <p class="text-red-700">Giao dịch nạp tiền không thể hoàn thành</p>
            </div>
        </div>
    </div>
    ` : ''}

    ${deposit.status === 'pending' ? `
    <div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="p-2 bg-orange-100 rounded-full mr-4">
                <i class="fas fa-clock text-orange-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-orange-800 text-lg">Đang Chờ Xử Lý</h4>
                <p class="text-orange-700">Giao dịch đang chờ xử lý từ cổng thanh toán</p>
            </div>
        </div>
    </div>
    ` : ''}

    ${deposit.status === 'cancelled' ? `
    <div class="bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="p-2 bg-gray-100 rounded-full mr-4">
                <i class="fas fa-ban text-gray-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 text-lg">Giao Dịch Đã Hủy</h4>
                <p class="text-gray-700">Giao dịch đã được hủy bởi người dùng hoặc hệ thống</p>
            </div>
        </div>
    </div>
    ` : ''}

    ${deposit.status === 'expired' ? `
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-6">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-full mr-4">
                <i class="fas fa-hourglass-end text-yellow-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-yellow-800 text-lg">Giao Dịch Hết Hạn</h4>
                <p class="text-yellow-700">Giao dịch đã hết thời gian xử lý và không thể hoàn thành</p>
            </div>
        </div>
    </div>
    ` : ''}
</div>
    `;
}

function formatNumber(num) {
    return new Intl.NumberFormat('vi-VN').format(num);
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('viewModal').classList.remove('flex');
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('viewModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeViewModal();
            }
        });
    }
});
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

.font-mono {
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
}

/* Modal animations */
#viewModal {
    transition: opacity 0.3s ease-out;
}

#viewModal.hidden {
    opacity: 0;
    pointer-events: none;
}

#viewModal.flex {
    opacity: 1;
    pointer-events: auto;
}

#viewModal .bg-white {
    transition: transform 0.3s ease-out, opacity 0.3s ease-out;
}

#viewModal.hidden .bg-white {
    transform: scale(0.95) translateY(-10px);
    opacity: 0;
}

#viewModal.flex .bg-white {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Loading state */
.loading-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Smooth hover effects */
.hover-scale:hover {
    transform: scale(1.02);
    transition: transform 0.2s ease-out;
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
