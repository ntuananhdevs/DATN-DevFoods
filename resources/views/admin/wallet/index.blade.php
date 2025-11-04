@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản Lý Ví Tiền')
@section('description', 'Tổng quan về hoạt động ví tiền và giao dịch')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Quản Lý Ví Tiền</h1>
                <p class="text-gray-600 text-lg">Tổng quan về hoạt động ví tiền và giao dịch của khách hàng</p>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="refreshData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Làm mới
                </button>
                <div class="bg-white rounded-lg px-4 py-2 shadow-sm border">
                    <span class="text-sm text-gray-500">Cập nhật lần cuối:</span>
                    <span class="text-sm font-medium text-gray-900 ml-1" id="last-updated">{{ now()->format('H:i d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.wallet.withdrawals.pending') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Rút Tiền Chờ Duyệt</h3>
                        <p class="text-sm text-gray-600">{{ $withdrawalStats['pending_count'] }} yêu cầu</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.wallet.transactions.all') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-list text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Tất Cả Giao Dịch</h3>
                        <p class="text-sm text-gray-600">Xem chi tiết</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.wallet.users.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Quản Lý User</h3>
                        <p class="text-sm text-gray-600">{{ $generalStats['total_users_with_wallet'] }} users có ví</p>
                    </div>
                </div>
            </a>

            <button onclick="openHealthCheckModal()" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow duration-300 group">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-heartbeat text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold text-gray-900">Kiểm Tra Hệ Thống</h3>
                        <p class="text-sm text-gray-600">Health check</p>
                    </div>
                </div>
            </button>
        </div>
    </div>

    <!-- Stats Cards Row 1: Withdrawal Stats -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Thống Kê Rút Tiền</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pending Withdrawals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">Chờ duyệt</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1" data-wallet-counter="pending-withdrawals">{{ number_format($withdrawalStats['pending_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Yêu cầu chờ duyệt</p>
                <div class="text-lg font-semibold text-orange-600">{{ number_format($withdrawalStats['pending_amount'], 0, ',', '.') }} VND</div>
            </div>

            <!-- Today Withdrawals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Hôm nay</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($withdrawalStats['today_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Rút tiền hôm nay</p>
                <div class="text-lg font-semibold text-blue-600">{{ number_format($withdrawalStats['today_amount'], 0, ',', '.') }} VND</div>
            </div>

            <!-- This Month Withdrawals -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">Tháng này</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($withdrawalStats['this_month_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Rút tiền tháng này</p>
                <div class="text-lg font-semibold text-purple-600">{{ number_format($withdrawalStats['this_month_amount'], 0, ',', '.') }} VND</div>
            </div>

            <!-- Total Processed -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Hoàn thành</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($withdrawalStats['total_processed']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Tổng đã xử lý</p>
                <div class="text-lg font-semibold text-green-600">{{ number_format($withdrawalStats['total_processed_amount'], 0, ',', '.') }} VND</div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row 2: Deposit Stats -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Thống Kê Nạp Tiền</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Pending Deposits -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Chờ xử lý</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($depositStats['pending_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Nạp tiền chờ xử lý</p>
                <div class="text-lg font-semibold text-yellow-600">{{ number_format($depositStats['pending_amount'], 0, ',', '.') }} VND</div>
            </div>

            <!-- Today Deposits -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-cyan-100 rounded-lg">
                        <i class="fas fa-calendar-day text-cyan-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-cyan-100 text-cyan-800 px-2 py-1 rounded-full">Hôm nay</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($depositStats['today_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Nạp tiền hôm nay</p>
                <div class="text-lg font-semibold text-cyan-600">{{ number_format($depositStats['today_amount'], 0, ',', '.') }} VND</div>
            </div>

            <!-- This Month Deposits -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-indigo-100 rounded-lg">
                        <i class="fas fa-calendar-alt text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full">Tháng này</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($depositStats['this_month_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Nạp tiền tháng này</p>
                <div class="text-lg font-semibold text-indigo-600">{{ number_format($depositStats['this_month_amount'], 0, ',', '.') }} VND</div>
            </div>

            <!-- Total Completed -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-100 rounded-lg">
                        <i class="fas fa-check-double text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">Hoàn thành</span>
                </div>
                <div class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($depositStats['completed_count']) }}</div>
                <p class="text-sm text-gray-600 mb-2">Tổng đã hoàn thành</p>
                <div class="text-lg font-semibold text-emerald-600">{{ number_format($depositStats['completed_amount'], 0, ',', '.') }} VND</div>
            </div>
        </div>
    </div>

    <!-- General System Stats -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Thống Kê Hệ Thống</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total System Balance -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Tổng hệ thống</span>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($generalStats['total_wallet_balance'], 0, ',', '.') }}</div>
                <p class="text-sm text-blue-100">Tổng số dư hệ thống (VND)</p>
            </div>

            <!-- Users with Wallet -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Active users</span>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($generalStats['total_users_with_wallet']) }}</div>
                <p class="text-sm text-green-100">Users có ví tiền</p>
            </div>

            <!-- Average Balance -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white/20 rounded-lg">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Trung bình</span>
                </div>
                <div class="text-3xl font-bold mb-1">{{ number_format($generalStats['avg_wallet_balance'], 0, ',', '.') }}</div>
                <p class="text-sm text-purple-100">Số dư trung bình (VND)</p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Top Users -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Giao Dịch Gần Đây</h3>
                <a href="{{ route('admin.wallet.transactions.all') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg {{ $transaction->type == 'deposit' ? 'bg-green-100' : 'bg-red-100' }} mr-3">
                            <i class="fas {{ $transaction->type == 'deposit' ? 'fa-plus text-green-600' : 'fa-minus text-red-600' }}"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $transaction->transaction_code }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', '.') }} VND
                        </div>
                        <div class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m H:i') }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>Chưa có giao dịch nào</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Top Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Top Users Tháng Này</h3>
                <a href="{{ route('admin.wallet.users.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @forelse($topUsers as $index => $user)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm mr-3">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->transaction_count }} giao dịch</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-semibold text-blue-600">{{ number_format($user->total_amount, 0, ',', '.') }} VND</div>
                        <div class="text-xs text-gray-500">Tổng volume</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-users text-4xl mb-4"></i>
                    <p>Chưa có dữ liệu</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Health Check Modal -->
<div id="healthCheckModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Kiểm Tra Sức Khỏe Hệ Thống</h3>
            <button onclick="closeHealthCheckModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="healthCheckContent">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                <p>Đang kiểm tra...</p>
            </div>
        </div>
    </div>
</div>

<script>
function refreshData() {
    location.reload();
}

function openHealthCheckModal() {
    document.getElementById('healthCheckModal').classList.remove('hidden');
    document.getElementById('healthCheckModal').classList.add('flex');
    
    // Call health check API
    fetch('{{ route("admin.wallet.health-check") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayHealthCheck(data.health);
            } else {
                displayHealthCheckError(data.message);
            }
        })
        .catch(error => {
            displayHealthCheckError('Có lỗi xảy ra khi kiểm tra hệ thống');
        });
}

function closeHealthCheckModal() {
    document.getElementById('healthCheckModal').classList.add('hidden');
    document.getElementById('healthCheckModal').classList.remove('flex');
}

function displayHealthCheck(health) {
    const statusClass = health.health_status === 'healthy' ? 'text-green-600' : 
                       health.health_status === 'warning' ? 'text-yellow-600' : 'text-red-600';
    const statusIcon = health.health_status === 'healthy' ? 'fa-check-circle' : 
                      health.health_status === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle';
    
    document.getElementById('healthCheckContent').innerHTML = `
        <div class="text-center mb-6">
            <i class="fas ${statusIcon} text-6xl ${statusClass} mb-4"></i>
            <h4 class="text-2xl font-bold ${statusClass}">${health.health_score}/100</h4>
            <p class="text-gray-600">Health Score</p>
        </div>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-2">Tổng số dư hệ thống</h5>
                <p class="text-lg text-blue-600">${formatNumber(health.total_system_balance)} VND</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-2">Giao dịch pending</h5>
                <p class="text-lg text-orange-600">${health.pending_transactions_count}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-2">Lỗi hôm nay</h5>
                <p class="text-lg text-red-600">${health.failed_transactions_today}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h5 class="font-semibold text-gray-900 mb-2">Hết hạn hôm nay</h5>
                <p class="text-lg text-gray-600">${health.expired_transactions_today}</p>
            </div>
        </div>
        
        ${health.users_with_negative_balance > 0 ? `
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                <span class="text-red-800">Cảnh báo: ${health.users_with_negative_balance} users có số dư âm</span>
            </div>
        </div>
        ` : ''}
    `;
}

function displayHealthCheckError(message) {
    document.getElementById('healthCheckContent').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-exclamation-triangle text-4xl text-red-600 mb-4"></i>
            <p class="text-red-600">${message}</p>
        </div>
    `;
}

function formatNumber(num) {
    return new Intl.NumberFormat('vi-VN').format(num);
}

// Auto update last updated time
setInterval(() => {
    document.getElementById('last-updated').textContent = new Date().toLocaleString('vi-VN');
}, 60000);
</script>

<style>
.hover\:shadow-md:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.transition-shadow {
    transition-property: box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
@endsection
