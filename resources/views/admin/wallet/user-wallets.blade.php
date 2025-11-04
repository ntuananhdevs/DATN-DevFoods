@extends('layouts.admin.contentLayoutMaster')

@section('title', 'Quản Lý Ví Khách Hàng')
@section('description', 'Xem và quản lý ví tiền của tất cả khách hàng')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Quản Lý Ví Khách Hàng</h1>
                <p class="text-gray-600 text-lg">Xem và quản lý ví tiền của tất cả khách hàng trong hệ thống</p>
            </div>
            <div class="flex items-center space-x-4">
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
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng khách hàng</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-wallet text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Có ví tiền</p>
                    <p class="text-2xl font-bold text-green-600">{{ $users->where('balance', '>', 0)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tổng số dư</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($users->sum('balance'), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Số dư trung bình</p>
                    <p class="text-2xl font-bold text-orange-600">{{ number_format($users->where('balance', '>', 0)->avg('balance') ?: 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('admin.wallet.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, email..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Min Balance -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số dư tối thiểu</label>
                <input type="number" name="min_balance" value="{{ request('min_balance') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Max Balance -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Số dư tối đa</label>
                <input type="number" name="max_balance" value="{{ request('max_balance') }}" placeholder="10000000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <!-- Has Transactions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Có giao dịch</label>
                <select name="has_transactions" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('has_transactions') == '1' ? 'selected' : '' }}>Có giao dịch</option>
                    <option value="0" {{ request('has_transactions') == '0' ? 'selected' : '' }}>Chưa có giao dịch</option>
                </select>
            </div>

            <!-- Filter Actions -->
            <div class="md:col-span-4 flex items-center space-x-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Tìm kiếm
                </button>
                <a href="{{ route('admin.wallet.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Xóa bộ lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Danh Sách Khách Hàng</h3>
        </div>

        @if($users->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số dư ví</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thống kê giao dịch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tham gia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-400">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold {{ $user->balance > 0 ? 'text-green-600' : ($user->balance < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                {{ number_format($user->balance, 0, ',', '.') }} VND
                            </div>
                            @if($user->balance < 0)
                            <div class="text-xs text-red-500">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Số dư âm
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div><strong>Tổng:</strong> {{ number_format($user->total_transactions) }} giao dịch</div>
                                <div class="text-green-600"><strong>Nạp:</strong> {{ number_format($user->total_deposits ?: 0, 0, ',', '.') }} VND</div>
                                <div class="text-red-600"><strong>Rút:</strong> {{ number_format($user->total_withdrawals ?: 0, 0, ',', '.') }} VND</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->pending_transactions > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="fas fa-clock mr-1"></i>
                                {{ $user->pending_transactions }} pending
                            </span>
                            @else
                            <span class="text-gray-400 text-sm">Không có</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div>{{ $user->created_at->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.wallet.users.detail', $user->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Xem chi tiết ví">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->appends(request()->all())->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không tìm thấy khách hàng nào</h3>
            <p class="text-gray-500">Thử điều chỉnh bộ lọc để tìm kiếm khách hàng.</p>
        </div>
        @endif
    </div>
</div>



<script>
// Basic page functionality - no balance adjustment features
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
</style>
@endsection
