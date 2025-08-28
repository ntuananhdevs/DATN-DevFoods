@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Yêu Cầu Rút Tiền Chờ Duyệt')
@section('description', 'Quản lý và duyệt các yêu cầu rút tiền từ khách hàng')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Yêu Cầu Rút Tiền Chờ Duyệt</h1>
                <p class="text-gray-600 text-lg">Duyệt và quản lý các yêu cầu rút tiền từ khách hàng</p>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="toggleBatchMode()" id="batch-toggle" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-tasks mr-2"></i>
                    Chế độ hàng loạt
                </button>
                <a href="{{ route('admin.wallet.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Quay lại Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng yêu cầu</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $withdrawals->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Số tiền lớn (>1M)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $withdrawals->where('amount', '>=', 1000000)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="fas fa-money-bill-wave text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng số tiền</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($withdrawals->sum('amount'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Khách hàng</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $withdrawals->pluck('user_id')->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Actions (Hidden by default) -->
    <div id="batch-actions" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-900">Chọn tất cả</span>
                </label>
                <span id="selected-count" class="text-sm text-gray-600">0 được chọn</span>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="batchApprove()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 disabled:opacity-50" disabled id="batch-approve-btn">
                    <i class="fas fa-check mr-2"></i>
                    Duyệt hàng loạt
                </button>
                <button onclick="batchReject()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 disabled:opacity-50" disabled id="batch-reject-btn">
                    <i class="fas fa-times mr-2"></i>
                    Từ chối hàng loạt
                </button>
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Danh Sách Yêu Cầu Rút Tiền</h3>
        </div>

        @if($withdrawals->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="batch-only hidden px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all-header" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số tiền</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thông tin ngân hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($withdrawals as $withdrawal)
                    <tr class="hover:bg-gray-50 transition-colors duration-200" data-withdrawal-id="{{ $withdrawal->id }}">
                        <td class="batch-only hidden px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="withdrawal-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $withdrawal->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium">
                                        <a href="{{ route('admin.users.show', $withdrawal->user->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 inline-flex items-center">
                                            {{ $withdrawal->user->name }}
                                            <i class="fas fa-external-link-alt ml-1 text-xs opacity-60"></i>
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $withdrawal->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-red-600">-{{ number_format($withdrawal->amount, 0, ',', '.') }} VND</div>
                            <div class="text-sm text-gray-500">{{ $withdrawal->transaction_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $bankInfo = is_string($withdrawal->metadata) ? json_decode($withdrawal->metadata, true) : $withdrawal->metadata;
                            @endphp
                            <div class="text-sm text-gray-900">
                                <div><strong>Ngân hàng:</strong> {{ $bankInfo['bank_name'] ?? 'N/A' }}</div>
                                <div><strong>STK:</strong> {{ $bankInfo['bank_account'] ?? 'N/A' }}</div>
                                <div><strong>Chủ TK:</strong> {{ $bankInfo['account_holder'] ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $withdrawal->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-sm text-gray-500">{{ $withdrawal->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                <i class="fas fa-clock mr-1"></i>
                                Chờ duyệt
                            </span>
                            @if($withdrawal->amount >= 1000000)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 ml-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Số tiền lớn
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button onclick="viewWithdrawal({{ $withdrawal->id }})" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="approveWithdrawal({{ $withdrawal->id }})" class="text-green-600 hover:text-green-900 transition-colors" title="Duyệt">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="rejectWithdrawal({{ $withdrawal->id }})" class="text-red-600 hover:text-red-900 transition-colors" title="Từ chối">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $withdrawals->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có yêu cầu rút tiền nào</h3>
            <p class="text-gray-500">Tất cả yêu cầu rút tiền đã được xử lý.</p>
        </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-green-600">
                <i class="fas fa-check-circle mr-2"></i>
                Duyệt Yêu Cầu Rút Tiền
            </h3>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú admin (tùy chọn)</label>
            <textarea id="approve-notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" rows="3" placeholder="Nhập ghi chú nếu cần..."></textarea>
        </div>
        <div class="flex space-x-3">
            <button onclick="closeApproveModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors">
                Hủy
            </button>
            <button onclick="confirmApprove()" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition-colors">
                Duyệt
            </button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-red-600">
                <i class="fas fa-times-circle mr-2"></i>
                Từ Chối Yêu Cầu Rút Tiền
            </h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Lý do từ chối <span class="text-red-500">*</span></label>
            <textarea id="reject-reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" rows="3" placeholder="Nhập lý do từ chối..." required></textarea>
        </div>
        <div class="flex space-x-3">
            <button onclick="closeRejectModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition-colors">
                Hủy
            </button>
            <button onclick="confirmReject()" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition-colors">
                Từ chối
            </button>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Chi Tiết Yêu Cầu Rút Tiền</h3>
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
            'user' => [
                'id' => $w->user->id,
                'name' => $w->user->name,
                'email' => $w->user->email,
                'balance' => floatval($w->user->balance)
            ]
        ];
    }
    echo json_encode($withdrawalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
} catch (Exception $e) {
    echo '{}';
}
@endphp
</script>

<script>
let currentWithdrawalId = null;
let batchMode = false;
let selectedWithdrawals = new Set();

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

// Batch mode toggle
function toggleBatchMode() {
    batchMode = !batchMode;
    const batchElements = document.querySelectorAll('.batch-only');
    const batchActions = document.getElementById('batch-actions');
    const toggleBtn = document.getElementById('batch-toggle');
    
    if (batchMode) {
        batchElements.forEach(el => el.classList.remove('hidden'));
        batchActions.classList.remove('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-times mr-2"></i>Thoát chế độ hàng loạt';
        toggleBtn.className = 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200';
    } else {
        batchElements.forEach(el => el.classList.add('hidden'));
        batchActions.classList.add('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-tasks mr-2"></i>Chế độ hàng loạt';
        toggleBtn.className = 'bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-200';
        selectedWithdrawals.clear();
        updateBatchButtons();
    }
}

// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        if (this.checked) {
            selectedWithdrawals.add(checkbox.value);
        } else {
            selectedWithdrawals.delete(checkbox.value);
        }
    });
    updateBatchButtons();
});

// Individual checkbox handling
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('withdrawal-checkbox')) {
        if (e.target.checked) {
            selectedWithdrawals.add(e.target.value);
        } else {
            selectedWithdrawals.delete(e.target.value);
        }
        updateBatchButtons();
    }
});

function updateBatchButtons() {
    const count = selectedWithdrawals.size;
    document.getElementById('selected-count').textContent = `${count} được chọn`;
    
    const approveBtn = document.getElementById('batch-approve-btn');
    const rejectBtn = document.getElementById('batch-reject-btn');
    
    if (count > 0) {
        approveBtn.disabled = false;
        rejectBtn.disabled = false;
    } else {
        approveBtn.disabled = true;
        rejectBtn.disabled = true;
    }
}

// Individual actions
function viewWithdrawal(id) {
    currentWithdrawalId = id;
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
    const balanceAfter = withdrawal.user.balance + withdrawal.amount;
    const isLargeAmount = withdrawal.amount >= 1000000;
    
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        <i class="fas fa-clock mr-1"></i>
                        Chờ duyệt
                    </span>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-3">Thông Tin Khách Hàng</h4>
            <div class="space-y-2">
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
                <div class="flex-shrink-0 w-2 h-2 bg-green-600 rounded-full mr-3"></div>
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
            
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 text-sm">Số dư sau rút:</span>
                    <span class="font-medium ${balanceAfter >= 0 ? 'text-green-600' : 'text-red-600'}">
                        ${formatNumber(balanceAfter)} VND
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3 pt-4 border-t">
        <button onclick="approveWithdrawal(${withdrawal.id})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-check mr-2"></i>
            Duyệt yêu cầu
        </button>
        <button onclick="rejectWithdrawal(${withdrawal.id})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-times mr-2"></i>
            Từ chối yêu cầu
        </button>
    </div>
</div>
    `;
}

function formatNumber(num) {
    return new Intl.NumberFormat('vi-VN').format(num);
}

function approveWithdrawal(id) {
    currentWithdrawalId = id;
    // Close view modal if open
    closeViewModal();
    const approveModal = document.getElementById('approveModal');
    if (approveModal) {
        approveModal.classList.remove('hidden');
        approveModal.classList.add('flex');
    }
}

function rejectWithdrawal(id) {
    currentWithdrawalId = id;
    // Close view modal if open
    closeViewModal();
    const rejectModal = document.getElementById('rejectModal');
    if (rejectModal) {
        rejectModal.classList.remove('hidden');
        rejectModal.classList.add('flex');
    }
}

// Modal close functions
function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approve-notes').value = '';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('reject-reason').value = '';
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    document.getElementById('viewModal').classList.remove('flex');
}

// Confirm actions
function confirmApprove() {
    const notes = document.getElementById('approve-notes').value;
    
    fetch(`{{ url('/admin/wallet/withdrawals') }}/${currentWithdrawalId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            admin_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã duyệt yêu cầu rút tiền thành công', 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        showNotification('Có lỗi xảy ra khi duyệt yêu cầu', 'error');
    });
    
    closeApproveModal();
}

function confirmReject() {
    const reason = document.getElementById('reject-reason').value.trim();
    
    if (!reason) {
        showNotification('Vui lòng nhập lý do từ chối', 'error');
        return;
    }
    
    fetch(`{{ url('/admin/wallet/withdrawals') }}/${currentWithdrawalId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reject_reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã từ chối yêu cầu rút tiền và hoàn tiền cho khách hàng', 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        showNotification('Có lỗi xảy ra khi từ chối yêu cầu', 'error');
    });
    
    closeRejectModal();
}

// Batch actions
function batchApprove() {
    if (selectedWithdrawals.size === 0) return;
    
    if (!confirm(`Bạn có chắc chắn muốn duyệt ${selectedWithdrawals.size} yêu cầu rút tiền?`)) return;
    
    fetch('{{ url('/admin/wallet/withdrawals/batch-process') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            withdrawal_ids: Array.from(selectedWithdrawals),
            action: 'approve',
            batch_notes: 'Batch approval'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Đã duyệt ${data.processed} yêu cầu rút tiền`, 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        showNotification('Có lỗi xảy ra khi xử lý hàng loạt', 'error');
    });
}

function batchReject() {
    if (selectedWithdrawals.size === 0) return;
    
    const reason = prompt(`Nhập lý do từ chối ${selectedWithdrawals.size} yêu cầu rút tiền:`);
    if (!reason || !reason.trim()) return;
    
    fetch('{{ url('/admin/wallet/withdrawals/batch-process') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            withdrawal_ids: Array.from(selectedWithdrawals),
            action: 'reject',
            batch_notes: reason.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Đã từ chối ${data.processed} yêu cầu rút tiền`, 'success');
            location.reload();
        } else {
            showNotification(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        showNotification('Có lỗi xảy ra khi xử lý hàng loạt', 'error');
    });
}

// Notification function
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

table {
    table-layout: fixed;
}

td, th {
    word-wrap: break-word;
}

/* Modal styles */
.z-50 {
    z-index: 50;
}

#viewModal .bg-white {
    max-height: 90vh;
    overflow-y: auto;
}
</style>
@endsection
