@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Chi Tiết Ví - ' . $user->name)
@section('description', 'Chi tiết ví và lịch sử giao dịch của khách hàng')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Chi Tiết Ví - {{ $user->name }}</h1>
                <p class="text-gray-600 text-lg">Thông tin chi tiết ví và lịch sử giao dịch của khách hàng</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.wallet.users.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Profile -->
            <div class="flex items-center">
                <div class="flex-shrink-0 h-20 w-20">
                    <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 text-2xl"></i>
                    </div>
                </div>
                <div class="ml-6">
                    <h3 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500">ID: {{ $user->id }} • Tham gia: {{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Current Balance -->
            <div class="text-center lg:text-left">
                <p class="text-sm text-gray-600 mb-2">Số dư hiện tại</p>
                <div class="text-4xl font-bold {{ $user->balance > 0 ? 'text-green-600' : ($user->balance < 0 ? 'text-red-600' : 'text-gray-500') }}">
                    {{ number_format($user->balance, 0, ',', '.') }} VND
                </div>
                @if($user->balance < 0)
                <div class="text-sm text-red-500 mt-1">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Cảnh báo: Số dư âm
                </div>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($stats['total_deposits'], 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-600">Tổng nạp (VND)</div>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">{{ number_format($stats['total_withdrawals'], 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-600">Tổng rút (VND)</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng giao dịch</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_transactions']) }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Chờ xử lý</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($stats['pending_transactions']) }}</p>
                </div>
            </div>
        </div>

        <!-- Failed Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg mr-4">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Thất bại</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats['failed_transactions']) }}</p>
                </div>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-percentage text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tỷ lệ thành công</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $stats['total_transactions'] > 0 ? round((($stats['total_transactions'] - $stats['failed_transactions']) / $stats['total_transactions']) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Lịch Sử Giao Dịch</h3>
                <div class="flex items-center space-x-2">
                    <select id="transaction-filter" onchange="filterTransactions()" class="px-3 py-1 border border-gray-300 rounded text-sm">
                        <option value="">Tất cả</option>
                        <option value="deposit">Nạp tiền</option>
                        <option value="withdraw">Rút tiền</option>
                        <option value="payment">Thanh toán</option>
                        <option value="refund">Hoàn tiền</option>
                    </select>
                    <select id="status-filter" onchange="filterTransactions()" class="px-3 py-1 border border-gray-300 rounded text-sm">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="failed">Thất bại</option>
                        <option value="cancelled">Đã hủy</option>
                        <option value="expired">Hết hạn</option>
                    </select>
                </div>
            </div>
        </div>

        @if($transactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mô tả</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition-colors duration-200 transaction-row" 
                        data-type="{{ $transaction->type }}" 
                        data-status="{{ $transaction->status }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            #{{ $transaction->id }}
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
                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $transaction->description }}">
                            {{ $transaction->description }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-xs text-gray-500">{{ $transaction->created_at->diffForHumans() }}</div>
                            @if($transaction->processed_at)
                            <div class="text-xs text-blue-500">Xử lý: {{ $transaction->processed_at->format('d/m H:i') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewTransactionDetail({{ $transaction->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết">
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
            {{ $transactions->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có giao dịch nào</h3>
            <p class="text-gray-500">Khách hàng chưa thực hiện giao dịch nào.</p>
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

<!-- Hidden transaction data for JavaScript -->
<script type="application/json" id="transaction-data">
@php
try {
    $transactionData = [];
    foreach($transactions as $t) {
        $transactionData[$t->id] = [
            'id' => $t->id,
            'transaction_code' => $t->transaction_code,
            'type' => $t->type,
            'amount' => floatval($t->amount),
            'status' => $t->status,
            'payment_method' => $t->payment_method,
            'created_at' => $t->created_at->format('d/m/Y H:i:s'),
            'created_at_human' => $t->created_at->diffForHumans(),
            'processed_at' => $t->processed_at ? $t->processed_at->format('d/m/Y H:i:s') : null,
            'expires_at' => $t->expires_at ? $t->expires_at->format('d/m/Y H:i:s') : null,
            'description' => $t->description,
            'metadata' => $t->metadata,
            'admin_notes' => $t->admin_notes ?? null,
            'reject_reason' => $t->reject_reason ?? null
        ];
    }
    echo json_encode($transactionData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
} catch (Exception $e) {
    echo '{}';
}
@endphp
</script>

<script>


function filterTransactions() {
    const typeFilter = document.getElementById('transaction-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    const rows = document.querySelectorAll('.transaction-row');
    
    rows.forEach(row => {
        const type = row.dataset.type;
        const status = row.dataset.status;
        
        const typeMatch = !typeFilter || type === typeFilter;
        const statusMatch = !statusFilter || status === statusFilter;
        
        if (typeMatch && statusMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Get transaction data from embedded script
function getTransactionData() {
    try {
        const dataScript = document.getElementById('transaction-data');
        if (!dataScript) {
            console.error('Transaction data script not found');
            return {};
        }
        const data = JSON.parse(dataScript.textContent);
        return data;
    } catch (e) {
        console.error('Error parsing transaction data:', e);
        return {};
    }
}

function viewTransactionDetail(id) {
    const modal = document.getElementById('transactionModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    document.getElementById('transaction-details').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i><p class="mt-2 text-gray-600">Đang tải thông tin giao dịch...</p></div>';
    
    // Load transaction details from embedded data
    const transactionData = getTransactionData();
    const transaction = transactionData[id];
    
    if (!transaction) {
        document.getElementById('transaction-details').innerHTML = '<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Không tìm thấy thông tin giao dịch</p></div>';
        return;
    }
    
    // Generate HTML content
    setTimeout(() => {
        try {
            const html = generateTransactionDetailHTML(transaction);
            document.getElementById('transaction-details').innerHTML = html;
        } catch (e) {
            console.error('Error generating HTML:', e);
            document.getElementById('transaction-details').innerHTML = '<div class="text-center py-8 text-red-600"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i><p>Lỗi hiển thị chi tiết</p></div>';
        }
    }, 800);
}

function generateTransactionDetailHTML(transaction) {
    // Status display configuration
    const statusConfig = {
        'pending': { class: 'bg-orange-100 text-orange-800 border-orange-200', icon: 'fa-clock', text: 'Chờ xử lý' },
        'completed': { class: 'bg-green-100 text-green-800 border-green-200', icon: 'fa-check-circle', text: 'Hoàn thành' },
        'failed': { class: 'bg-red-100 text-red-800 border-red-200', icon: 'fa-times-circle', text: 'Thất bại' },
        'cancelled': { class: 'bg-gray-100 text-gray-800 border-gray-200', icon: 'fa-ban', text: 'Đã hủy' },
        'expired': { class: 'bg-yellow-100 text-yellow-800 border-yellow-200', icon: 'fa-hourglass-end', text: 'Hết hạn' }
    };
    const statusConf = statusConfig[transaction.status] || { class: 'bg-gray-100 text-gray-800 border-gray-200', icon: 'fa-question', text: 'Không xác định' };
    
    // Type configuration
    const typeConfig = {
        'deposit': { class: 'bg-green-100 text-green-800 border-green-200', icon: 'fa-plus', text: 'Nạp tiền', color: 'text-green-600', sign: '+' },
        'withdraw': { class: 'bg-red-100 text-red-800 border-red-200', icon: 'fa-minus', text: 'Rút tiền', color: 'text-red-600', sign: '-' },
        'payment': { class: 'bg-blue-100 text-blue-800 border-blue-200', icon: 'fa-credit-card', text: 'Thanh toán', color: 'text-blue-600', sign: '-' },
        'refund': { class: 'bg-purple-100 text-purple-800 border-purple-200', icon: 'fa-undo', text: 'Hoàn tiền', color: 'text-purple-600', sign: '+' }
    };
    const typeConf = typeConfig[transaction.type] || { class: 'bg-gray-100 text-gray-800 border-gray-200', icon: 'fa-question', text: 'Khác', color: 'text-gray-600', sign: '' };
    
    // Parse metadata
    let metadata = {};
    try {
        metadata = typeof transaction.metadata === 'string' ? JSON.parse(transaction.metadata) : (transaction.metadata || {});
    } catch (e) {
        console.error('Error parsing metadata:', e);
        metadata = {};
    }
    
    return `
<div class="space-y-6">
    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full mr-4">
                    <i class="fas ${typeConf.icon} text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold ${typeConf.color}">${typeConf.sign}${formatNumber(transaction.amount)} VND</h3>
                    <p class="text-blue-600">Giao dịch #${transaction.id}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border ${statusConf.class}">
                    <i class="fas ${statusConf.icon} mr-2"></i>
                    ${statusConf.text}
                </span>
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
                    <span class="text-gray-600 font-medium">Mã giao dịch:</span>
                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">${transaction.transaction_code}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Loại giao dịch:</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border ${typeConf.class}">
                        <i class="fas ${typeConf.icon} mr-2"></i>
                        ${typeConf.text}
                    </span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-gray-600 font-medium">Mô tả:</span>
                    <span class="text-sm text-gray-800 text-right max-w-xs">${transaction.description || 'N/A'}</span>
                </div>
            </div>
        </div>

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
                        <div class="font-medium text-gray-900">Tạo giao dịch</div>
                        <div class="text-sm text-gray-600">${transaction.created_at}</div>
                        <div class="text-xs text-gray-400 mt-1">${transaction.created_at_human}</div>
                    </div>
                </div>
                
                ${transaction.processed_at ? `
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-3 h-3 ${transaction.status === 'completed' ? 'bg-green-500' : 'bg-red-500'} rounded-full mt-1 mr-4"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">${transaction.status === 'completed' ? 'Xử lý thành công' : 'Xử lý thất bại'}</div>
                        <div class="text-sm text-gray-600">${transaction.processed_at}</div>
                    </div>
                </div>
                ` : ''}

                ${transaction.expires_at ? `
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-3 h-3 bg-yellow-500 rounded-full mt-1 mr-4"></div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Thời gian hết hạn</div>
                        <div class="text-sm text-gray-600">${transaction.expires_at}</div>
                    </div>
                </div>
                ` : ''}
            </div>
        </div>
    </div>

    ${transaction.type === 'withdraw' && metadata && Object.keys(metadata).length > 0 ? `
    <!-- Bank Information for Withdrawals -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                <i class="fas fa-university text-indigo-600"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-900">Thông Tin Ngân Hàng</h4>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            ${metadata.bank_name ? `
            <div class="bg-gray-50 p-3 rounded-lg">
                <label class="text-sm text-gray-600">Ngân hàng</label>
                <div class="font-medium">${metadata.bank_name}</div>
            </div>
            ` : ''}
            ${metadata.bank_account ? `
            <div class="bg-gray-50 p-3 rounded-lg">
                <label class="text-sm text-gray-600">Số tài khoản</label>
                <div class="font-mono font-medium">${metadata.bank_account}</div>
            </div>
            ` : ''}
            ${metadata.account_holder ? `
            <div class="bg-gray-50 p-3 rounded-lg">
                <label class="text-sm text-gray-600">Chủ tài khoản</label>
                <div class="font-medium">${metadata.account_holder}</div>
            </div>
            ` : ''}
        </div>
    </div>
    ` : ''}

    ${transaction.admin_notes || transaction.reject_reason ? `
    <!-- Admin Notes -->
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                <i class="fas fa-sticky-note text-purple-600"></i>
            </div>
            <h4 class="text-lg font-semibold text-gray-900">Ghi Chú Admin</h4>
        </div>
        ${transaction.admin_notes ? `
        <div class="bg-blue-50 p-4 rounded-lg mb-3">
            <label class="text-sm font-medium text-blue-600">Ghi chú:</label>
            <div class="text-blue-800 mt-1">${transaction.admin_notes}</div>
        </div>
        ` : ''}
        ${transaction.reject_reason ? `
        <div class="bg-red-50 p-4 rounded-lg">
            <label class="text-sm font-medium text-red-600">Lý do từ chối:</label>
            <div class="text-red-800 mt-1">${transaction.reject_reason}</div>
        </div>
        ` : ''}
    </div>
    ` : ''}

    <!-- Information Notice -->
    <div class="bg-gray-50 rounded-xl p-6">
        <div class="text-center text-gray-600">
            <i class="fas fa-info-circle text-2xl mb-2"></i>
            <p class="text-sm">Đây là trang xem chi tiết giao dịch của khách hàng ${transaction.user ? transaction.user.name : 'N/A'}.</p>
            <p class="text-sm mt-1">Để thực hiện thao tác duyệt/từ chối, vui lòng truy cập trang quản lý riêng biệt.</p>
        </div>
    </div>
</div>
    `;
}

function formatNumber(num) {
    return new Intl.NumberFormat('vi-VN').format(num);
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



.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
