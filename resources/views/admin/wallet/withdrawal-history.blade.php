@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Lịch Sử Rút Tiền')
@section('description', 'Xem lịch sử tất cả các giao dịch rút tiền')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Lịch Sử Rút Tiền</h1>
                <p class="text-gray-600 text-lg">Xem lịch sử tất cả các giao dịch rút tiền trong hệ thống</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.wallet.withdrawals.pending') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-clock mr-2"></i>
                    Chờ duyệt
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
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($withdrawals->total()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Đã duyệt</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($withdrawals->where('status', 'completed')->count()) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg mr-4">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Bị từ chối</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($withdrawals->where('status', 'failed')->count()) }}</p>
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
                    <p class="text-lg font-bold text-purple-600">{{ number_format($withdrawals->sum('amount'), 0, ',', '.') }} VND</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('admin.wallet.withdrawals.history') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Bị từ chối</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
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
            <div class="md:col-span-4 flex items-center space-x-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Áp dụng bộ lọc
                </button>
                <a href="{{ route('admin.wallet.withdrawals.history') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Xóa bộ lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Lịch Sử Rút Tiền</h3>
        </div>

        @if($withdrawals->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin ngân hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                            #{{ $withdrawal->id }}
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
                                        @if($withdrawal->user)
                                        <a href="{{ route('admin.users.show', $withdrawal->user->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 inline-flex items-center">
                                            {{ $withdrawal->user->name }}
                                            <i class="fas fa-external-link-alt ml-1 text-xs opacity-60"></i>
                                        </a>
                                        @else
                                        <span class="text-gray-900">N/A</span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $withdrawal->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-red-600">-{{ number_format($withdrawal->amount, 0, ',', '.') }} VND</div>
                            <div class="text-sm text-gray-500 font-mono">{{ $withdrawal->transaction_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $bankInfo = is_string($withdrawal->metadata) ? json_decode($withdrawal->metadata, true) : $withdrawal->metadata;
                            @endphp
                            <div class="text-sm text-gray-900">
                                <div><strong>{{ $bankInfo['bank_name'] ?? 'N/A' }}</strong></div>
                                <div>STK: {{ $bankInfo['bank_account'] ?? 'N/A' }}</div>
                                <div>{{ $bankInfo['account_holder'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'pending' => ['class' => 'bg-orange-100 text-orange-800', 'icon' => 'fa-clock', 'text' => 'Chờ duyệt'],
                                    'completed' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'fa-check', 'text' => 'Đã duyệt'],
                                    'failed' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'fa-times', 'text' => 'Bị từ chối'],
                                    'cancelled' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-ban', 'text' => 'Đã hủy']
                                ];
                                $config = $statusConfig[$withdrawal->status] ?? ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'fa-question', 'text' => 'Không xác định'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
                                <i class="fas {{ $config['icon'] }} mr-1"></i>
                                {{ $config['text'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div><strong>Tạo:</strong> {{ $withdrawal->created_at->format('d/m/Y H:i') }}</div>
                            @if($withdrawal->processed_at)
                            <div><strong>Xử lý:</strong> {{ $withdrawal->processed_at->format('d/m/Y H:i') }}</div>
                            @endif
                            <div class="text-xs text-gray-500">{{ $withdrawal->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewWithdrawal({{ $withdrawal->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết">
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
            {{ $withdrawals->appends(request()->all())->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có lịch sử rút tiền</h3>
            <p class="text-gray-500">Chưa có giao dịch rút tiền nào được xử lý.</p>
        </div>
        @endif
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Chi Tiết Giao Dịch Rút Tiền</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="withdrawal-details">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Hidden withdrawal data for JavaScript -->
<script type="application/json" id="withdrawal-data">
@php
try {
    $withdrawalData = [];
    foreach($withdrawals as $w) {
        $withdrawalData[$w->id] = [
            'id' => $w->id,
            'transaction_code' => $w->transaction_code,
            'amount' => floatval($w->amount),
            'status' => $w->status,
            'created_at' => $w->created_at->format('d/m/Y H:i:s'),
            'created_at_human' => $w->created_at->diffForHumans(),
            'processed_at' => $w->processed_at ? $w->processed_at->format('d/m/Y H:i:s') : null,
            'expires_at' => $w->expires_at ? $w->expires_at->format('d/m/Y H:i:s') : null,
            'description' => $w->description,
            'admin_notes' => $w->admin_notes,
            'metadata' => $w->metadata,
            'user' => $w->user ? [
                'id' => $w->user->id,
                'name' => $w->user->name,
                'email' => $w->user->email,
                'balance' => floatval($w->user->balance)
            ] : null
        ];
    }
    echo json_encode($withdrawalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
} catch (Exception $e) {
    echo '{}';
}
@endphp
</script>

<script>
// Get withdrawal data from embedded script
function getWithdrawalData() {
    try {
        const dataScript = document.getElementById('withdrawal-data');
        if (!dataScript) {
            console.error('Withdrawal data script not found');
            return {};
        }
        const data = JSON.parse(dataScript.textContent);
        return data;
    } catch (e) {
        console.error('Error parsing withdrawal data:', e);
        return {};
    }
}

function viewWithdrawal(id) {
    const viewModal = document.getElementById('viewModal');
    if (viewModal) {
        viewModal.classList.remove('hidden');
        viewModal.classList.add('flex');
    } else {
        console.error('View modal not found');
        return;
    }
    
    // Load withdrawal details from embedded data
    const withdrawalData = getWithdrawalData();
    const withdrawal = withdrawalData[id];
    
    if (!withdrawal) {
        document.getElementById('withdrawal-details').innerHTML = '<div class="text-center py-4 text-red-600">Không tìm thấy thông tin giao dịch</div>';
        return;
    }
    
    // Parse bank info
    let bankInfo = {};
    try {
        bankInfo = typeof withdrawal.metadata === 'string' ? JSON.parse(withdrawal.metadata) : (withdrawal.metadata || {});
    } catch (e) {
        console.error('Error parsing bank info:', e);
        bankInfo = {};
    }
    
    // Generate HTML content
    try {
        const html = generateWithdrawalDetailHTML(withdrawal, bankInfo);
        document.getElementById('withdrawal-details').innerHTML = html;
    } catch (e) {
        console.error('Error generating HTML:', e);
        document.getElementById('withdrawal-details').innerHTML = '<div class="text-center py-4 text-red-600">Lỗi hiển thị chi tiết</div>';
    }
}

function generateWithdrawalDetailHTML(withdrawal, bankInfo) {
    const balanceAfter = withdrawal.user ? withdrawal.user.balance + withdrawal.amount : 0;
    const isLargeAmount = withdrawal.amount >= 1000000;
    
    // Status display configuration
    const statusConfig = {
        'pending': { class: 'bg-orange-100 text-orange-800', icon: 'fa-clock', text: 'Chờ duyệt' },
        'completed': { class: 'bg-green-100 text-green-800', icon: 'fa-check-circle', text: 'Đã duyệt' },
        'failed': { class: 'bg-red-100 text-red-800', icon: 'fa-times-circle', text: 'Bị từ chối' },
        'cancelled': { class: 'bg-gray-100 text-gray-800', icon: 'fa-ban', text: 'Đã hủy' }
    };
    const config = statusConfig[withdrawal.status] || { class: 'bg-gray-100 text-gray-800', icon: 'fa-question', text: 'Không xác định' };
    
    return `
<div class="space-y-6">
    <!-- Withdrawal Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Info -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Giao Dịch</h4>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">ID:</span>
                    <span class="font-mono font-semibold">#${withdrawal.id}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mã giao dịch:</span>
                    <span class="font-mono text-sm">${withdrawal.transaction_code}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Số tiền:</span>
                    <span class="font-bold text-red-600">${formatNumber(withdrawal.amount)} VND</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Trạng thái:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${config.class}">
                        <i class="fas ${config.icon} mr-1"></i>
                        ${config.text}
                    </span>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Khách Hàng</h4>
            <div class="space-y-2">
                ${withdrawal.user ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Tên:</span>
                    <a href="{{ url('/admin/users/show') }}/${withdrawal.user.id}" 
                       class="font-medium text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 inline-flex items-center">
                        ${withdrawal.user.name}
                        <i class="fas fa-external-link-alt ml-1 text-xs opacity-60"></i>
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email:</span>
                    <span class="text-sm">${withdrawal.user.email}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Số dư hiện tại:</span>
                    <span class="font-bold ${withdrawal.user.balance >= 0 ? 'text-green-600' : 'text-red-600'}">
                        ${formatNumber(withdrawal.user.balance)} VND
                    </span>
                </div>
                ` : `
                <div class="text-center text-gray-500">
                    <i class="fas fa-user-slash text-2xl mb-2"></i>
                    <p>Thông tin người dùng không có sẵn</p>
                </div>
                `}
            </div>
        </div>
    </div>

    <!-- Bank Information -->
    <div class="bg-blue-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Ngân Hàng</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <span class="text-gray-600 text-sm">Ngân hàng:</span>
                <div class="font-medium">${bankInfo.bank_name || 'N/A'}</div>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Số tài khoản:</span>
                <div class="font-mono font-medium">${bankInfo.bank_account || 'N/A'}</div>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Chủ tài khoản:</span>
                <div class="font-medium">${bankInfo.account_holder || 'N/A'}</div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Thời Gian</h4>
        <div class="space-y-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-2 h-2 bg-blue-600 rounded-full mr-3"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Tạo yêu cầu</div>
                    <div class="text-sm text-gray-500">${withdrawal.created_at}</div>
                    <div class="text-xs text-gray-400">${withdrawal.created_at_human}</div>
                </div>
            </div>
            
            ${withdrawal.processed_at ? `
            <div class="flex items-center">
                <div class="flex-shrink-0 w-2 h-2 ${withdrawal.status === 'completed' ? 'bg-green-600' : 'bg-red-600'} rounded-full mr-3"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Đã xử lý</div>
                    <div class="text-sm text-gray-500">${withdrawal.processed_at}</div>
                </div>
            </div>
            ` : ''}

            ${withdrawal.expires_at ? `
            <div class="flex items-center">
                <div class="flex-shrink-0 w-2 h-2 bg-yellow-600 rounded-full mr-3"></div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">Hết hạn</div>
                    <div class="text-sm text-gray-500">${withdrawal.expires_at}</div>
                </div>
            </div>
            ` : ''}
        </div>
    </div>

    <!-- Admin Notes or Reject Reason -->
    ${withdrawal.status === 'pending' ? `
    <div class="bg-orange-50 p-4 rounded-lg">
        <h4 class="font-semibold text-orange-800 mb-2">Trạng Thái</h4>
        <p class="text-orange-700">Đang chờ admin xử lý yêu cầu rút tiền này</p>
    </div>
    ` : ''}
    
    ${withdrawal.status === 'completed' && withdrawal.metadata && withdrawal.metadata.admin_notes ? `
    <div class="bg-green-50 p-4 rounded-lg">
        <h4 class="font-semibold text-green-800 mb-2">Ghi Chú Admin</h4>
        <p class="text-green-700">${withdrawal.metadata.admin_notes}</p>
    </div>
    ` : ''}
    
    ${withdrawal.status === 'failed' && withdrawal.metadata && withdrawal.metadata.reject_reason ? `
    <div class="bg-red-50 p-4 rounded-lg">
        <h4 class="font-semibold text-red-800 mb-2">Lý Do Từ Chối</h4>
        <p class="text-red-700">${withdrawal.metadata.reject_reason}</p>
    </div>
    ` : ''}

    <!-- Risk Assessment -->
    <div class="bg-yellow-50 p-4 rounded-lg">
        <h4 class="font-semibold text-gray-900 mb-3">Đánh Giá Rủi Ro</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Số tiền:</span>
                    ${isLargeAmount ? `
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Số tiền lớn
                        </span>
                    ` : `
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">
                            <i class="fas fa-check mr-1"></i>
                            Bình thường
                        </span>
                    `}
                </div>
            </div>
            
            ${withdrawal.user ? `
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Số dư sau rút:</span>
                    <span class="font-medium ${balanceAfter >= 0 ? 'text-green-600' : 'text-red-600'}">
                        ${formatNumber(balanceAfter)} VND
                    </span>
                </div>
            </div>
            ` : ''}
        </div>
    </div>
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
</style>
@endsection
