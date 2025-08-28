@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Lịch Sử Giao Dịch')

@section('content')
<x-customer-container>
<style>
    .transaction-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .transaction-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-left-color: #f97316;
    }
    
    .filter-tab, .status-tab {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .filter-tab.active, .status-tab.active {
        background-color: #f97316;
        color: white;
        border-color: #f97316;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
    }
    
    .quick-date:hover {
        background-color: #fff7ed;
        border-color: #f97316;
    }
    
    .transaction-message {
        animation: fadeInUp 0.5s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .transaction-message:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Lịch Sử Giao Dịch</h1>
                <p class="text-gray-600">Theo dõi tất cả giao dịch trong ví của bạn</p>
            </div>
            <a href="{{ route('customer.wallet.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i> Quay Lại Ví
            </a>
        </div>
    </div>

    <!-- Wallet Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-400 to-green-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Số Dư Hiện Tại</p>
                    <p class="text-2xl font-bold">{{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }} VND</p>
                </div>
                <i class="fas fa-wallet text-3xl text-green-200"></i>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Tổng Nạp Tiền</p>
                    <p class="text-2xl font-bold">
                        {{ number_format(auth()->user()->walletTransactions()->where('type', 'deposit')->where('status', 'completed')->sum('amount') ?? 0, 0, ',', '.') }} VND
                    </p>
                </div>
                <i class="fas fa-plus-circle text-3xl text-blue-200"></i>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-400 to-purple-600 text-white rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Tổng Rút Tiền</p>
                    <p class="text-2xl font-bold">
                        {{ number_format(auth()->user()->walletTransactions()->where('type', 'withdraw')->where('status', 'completed')->sum('amount') ?? 0, 0, ',', '.') }} VND
                    </p>
                </div>
                <i class="fas fa-minus-circle text-3xl text-purple-200"></i>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-filter mr-2 text-orange-500"></i>
                Bộ Lọc Giao Dịch
            </h3>
            <button id="reset-filters" class="text-sm text-gray-500 hover:text-orange-500 transition duration-300">
                <i class="fas fa-undo mr-1"></i>
                Đặt Lại
            </button>
        </div>
        
        <form id="filter-form" class="space-y-4">
            <!-- Type Filters -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Loại Giao Dịch</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="filter-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-orange-300 transition duration-300 active" data-type="">
                        <i class="fas fa-list mr-2"></i>Tất Cả
                    </button>
                    <button type="button" class="filter-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-green-300 transition duration-300" data-type="deposit">
                        <i class="fas fa-plus-circle mr-2 text-green-500"></i>Nạp Tiền
                    </button>
                    <button type="button" class="filter-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-red-300 transition duration-300" data-type="withdraw">
                        <i class="fas fa-minus-circle mr-2 text-red-500"></i>Rút Tiền
                    </button>
                    <button type="button" class="filter-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-blue-300 transition duration-300" data-type="payment">
                        <i class="fas fa-shopping-cart mr-2 text-blue-500"></i>Thanh Toán
                    </button>
                    <button type="button" class="filter-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-purple-300 transition duration-300" data-type="refund">
                        <i class="fas fa-undo mr-2 text-purple-500"></i>Hoàn Tiền
                    </button>
                </div>
            </div>
            
            <!-- Status Filters -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Trạng Thái</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="status-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-orange-300 transition duration-300 active" data-status="">
                        <i class="fas fa-list mr-2"></i>Tất Cả
                        @if(isset($stats['total_transactions']))
                            <span class="ml-1 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $stats['total_transactions'] }}</span>
                        @endif
                    </button>
                    <button type="button" class="status-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-green-300 transition duration-300" data-status="completed">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>Hoàn Thành
                        <span class="ml-1 text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">{{ ($stats['total_deposits'] ?? 0) + ($stats['total_withdrawals'] ?? 0) }}</span>
                    </button>
                    <button type="button" class="status-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-yellow-300 transition duration-300" data-status="pending">
                        <i class="fas fa-clock mr-2 text-yellow-500"></i>Đang Xử Lý
                        @if(isset($stats['pending_count']) && $stats['pending_count'] > 0)
                            <span class="ml-1 text-xs bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full">{{ $stats['pending_count'] }}</span>
                        @endif
                    </button>
                    <button type="button" class="status-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-red-300 transition duration-300" data-status="failed">
                        <i class="fas fa-times-circle mr-2 text-red-500"></i>Thất Bại
                        @if(isset($stats['failed_count']) && $stats['failed_count'] > 0)
                            <span class="ml-1 text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">{{ $stats['failed_count'] }}</span>
                        @endif
                    </button>
                    <button type="button" class="status-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-gray-400 transition duration-300" data-status="cancelled">
                        <i class="fas fa-ban mr-2 text-gray-500"></i>Đã Hủy
                    </button>
                    <button type="button" class="status-tab px-4 py-2 rounded-lg border-2 border-gray-200 bg-white hover:border-gray-600 transition duration-300" data-status="expired">
                        <i class="fas fa-hourglass-end mr-2 text-gray-600"></i>Hết Hạn
                    </button>
                </div>
            </div>
            
            <!-- Date and Amount Filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng Thời Gian</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="date" id="date_from" name="date_from" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Từ ngày">
                        <input type="date" id="date_to" name="date_to" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Đến ngày">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng Số Tiền (VND)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" id="amount_from" name="amount_from" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Từ">
                        <input type="number" id="amount_to" name="amount_to" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent" placeholder="Đến">
                    </div>
                </div>
            </div>
            
            <!-- Quick Date Filters -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lọc Nhanh</label>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="quick-date px-3 py-1 text-sm rounded-lg border border-gray-300 hover:border-orange-300 hover:bg-orange-50 transition duration-300" data-days="1">Hôm Nay</button>
                    <button type="button" class="quick-date px-3 py-1 text-sm rounded-lg border border-gray-300 hover:border-orange-300 hover:bg-orange-50 transition duration-300" data-days="7">7 Ngày</button>
                    <button type="button" class="quick-date px-3 py-1 text-sm rounded-lg border border-gray-300 hover:border-orange-300 hover:bg-orange-50 transition duration-300" data-days="30">30 Ngày</button>
                    <button type="button" class="quick-date px-3 py-1 text-sm rounded-lg border border-gray-300 hover:border-orange-300 hover:bg-orange-50 transition duration-300" data-days="90">3 Tháng</button>
                </div>
            </div>
            
            <!-- Apply Filters Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg transition duration-300">
                    <i class="fas fa-search mr-2"></i>
                    Áp Dụng Bộ Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-list mr-2 text-orange-500"></i>
                Danh Sách Giao Dịch
            </h3>
        </div>
        
        <div id="transactions-container">
            @if($transactions->count() > 0)
                @foreach($transactions as $transaction)
                <div class="transaction-card p-6 border-b border-gray-100 last:border-b-0" 
                     data-type="{{ $transaction->type }}" 
                     data-status="{{ $transaction->status }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Transaction Icon -->
                            <div class="w-12 h-12 rounded-full flex items-center justify-center
                                {{ $transaction->type == 'deposit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <i class="fas {{ $transaction->type == 'deposit' ? 'fa-plus' : 'fa-minus' }} text-lg"></i>
                            </div>
                            
                            <!-- Transaction Details -->
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $transaction->type_text }}</h4>
                                <p class="text-sm text-gray-600">{{ $transaction->description }}</p>
                                <div class="flex items-center space-x-4 mt-1">
                                    <span class="text-xs text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        {{ $transaction->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    @if($transaction->transaction_code)
                                    <span class="text-xs text-gray-500">
                                        <i class="fas fa-hashtag mr-1"></i>
                                        {{ $transaction->transaction_code }}
                                    </span>
                                    @endif
                                    @if($transaction->payment_method)
                                    <span class="text-xs text-gray-500 capitalize">
                                        <i class="fas fa-credit-card mr-1"></i>
                                        {{ str_replace('_', ' ', $transaction->payment_method) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amount and Status -->
                        <div class="text-right">
                            <div class="mb-2">
                                <p class="text-lg font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                                </p>
                                
                                @if($transaction->type == 'withdraw' && isset($transaction->metadata['processing_fee']))
                                    <p class="text-xs text-gray-500">
                                        Phí xử lý: {{ number_format($transaction->metadata['processing_fee']) }} VND
                                    </p>
                                    <p class="text-xs text-gray-600 font-medium">
                                        Thực nhận: {{ number_format($transaction->metadata['net_amount'] ?? $transaction->amount) }} VND
                                    </p>
                                @endif
                            </div>
                            
                            <div class="space-y-1">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                    {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                       ($transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($transaction->status == 'failed' ? 'bg-red-100 text-red-800' : 
                                       ($transaction->status == 'cancelled' ? 'bg-gray-100 text-gray-800' :
                                       ($transaction->status == 'expired' ? 'bg-gray-200 text-gray-600' : 'bg-blue-100 text-blue-800')))) }}">
                                    <i class="fas {{ $transaction->status == 'completed' ? 'fa-check' : 
                                                   ($transaction->status == 'pending' ? 'fa-clock' : 
                                                   ($transaction->status == 'failed' ? 'fa-times' : 
                                                   ($transaction->status == 'cancelled' ? 'fa-ban' : 
                                                   ($transaction->status == 'expired' ? 'fa-hourglass-end' : 'fa-info')))) }} mr-1"></i>
                                    {{ $transaction->status_text }}
                                </span>
                                
                                @if($transaction->type == 'withdraw' && $transaction->status == 'pending')
                                    <div class="transaction-message text-xs text-yellow-600 mt-1 bg-yellow-50 px-2 py-1 rounded-md border border-yellow-200 transition-all duration-300">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <span class="font-medium">Hãy bình tĩnh chờ admin xử lý nhé</span>
                                    </div>
                                @endif
                                
                                @if($transaction->type == 'withdraw' && $transaction->status == 'completed')
                                    <div class="transaction-message text-xs text-green-600 mt-1 bg-green-50 px-2 py-1 rounded-md border border-green-200 transition-all duration-300">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        <span class="font-medium">Vui lòng check số dư tài khoản ngân hàng</span>
                                        @if(isset($transaction->metadata['admin_notes']) && $transaction->metadata['admin_notes'])
                                            <div class="mt-1 text-green-500 font-normal">
                                                <i class="fas fa-sticky-note mr-1"></i>
                                                Ghi chú: {{ $transaction->metadata['admin_notes'] }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($transaction->type == 'withdraw' && $transaction->status == 'failed')
                                    <div class="transaction-message text-sm text-red-600 mt-2 bg-red-50 px-3 py-2 rounded-lg border border-red-200 transition-all duration-300">
                                        @if(isset($transaction->metadata['reject_reason']) && $transaction->metadata['reject_reason'])
                                            <div class="text-red-600 font-semibold">
                                                <i class="fas fa-info-circle mr-2 text-base"></i>
                                                <span class="text-sm">Lý do:</span> <span class="text-base">{{ $transaction->metadata['reject_reason'] }}</span>
                                            </div>
                                        @else
                                            <div class="text-red-600 font-semibold">
                                                <i class="fas fa-times-circle mr-2 text-base"></i>
                                                <span class="text-base">Giao dịch bị từ chối</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($transaction->type == 'withdraw' && $transaction->status == 'cancelled')
                                    <div class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-ban mr-1"></i>
                                        Giao dịch đã được hủy
                                    </div>
                                @endif
                                
                                @if($transaction->status == 'expired' && $transaction->type == 'deposit')
                                    <div class="text-xs text-gray-600 mt-1">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Đã hết hạn thanh toán
                                    </div>
                                @endif
                                
                                @if($transaction->type == 'deposit' && $transaction->status == 'completed')
                                    <div class="text-xs text-green-600 mt-1">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Nạp tiền thành công
                                    </div>
                                @endif
                                
                                @if($transaction->type == 'deposit' && $transaction->status == 'pending')
                                    <div class="text-xs text-yellow-600 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        Đang xử lý thanh toán
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Info for Bank Transfer -->
                    @if($transaction->metadata && isset($transaction->metadata['bank_name']))
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <h5 class="text-sm font-medium text-gray-700 mb-2">Thông Tin Ngân Hàng:</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-gray-600">
                            <div><strong>Ngân hàng:</strong> {{ $transaction->metadata['bank_name'] }}</div>
                            <div><strong>Số TK:</strong> {{ $transaction->metadata['bank_account'] }}</div>
                            <div><strong>Chủ TK:</strong> {{ $transaction->metadata['account_holder'] }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
                
                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="p-6 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Chưa Có Giao Dịch</h3>
                    <p class="text-gray-500 mb-6">Bạn chưa thực hiện giao dịch nào trong ví</p>
                    <a href="{{ route('customer.wallet.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg transition duration-300">
                        <i class="fas fa-plus mr-2"></i> Thực Hiện Giao Dịch Đầu Tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterForm = document.getElementById('filter-form');
    const resetFiltersBtn = document.getElementById('reset-filters');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const statusTabs = document.querySelectorAll('.status-tab');
    const quickDateBtns = document.querySelectorAll('.quick-date');
    
    let currentFilters = {
        type: '',
        status: '',
        date_from: '',
        date_to: '',
        amount_from: '',
        amount_to: ''
    };
    
    // Type filter tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            this.classList.add('border-orange-500', 'bg-orange-50', 'text-orange-600');
            currentFilters.type = this.dataset.type;
            applyFilters();
        });
    });
    
    // Status filter tabs
    statusTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            statusTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            this.classList.add('border-orange-500', 'bg-orange-50', 'text-orange-600');
            currentFilters.status = this.dataset.status;
            applyFilters();
        });
    });
    
    // Quick date filters
    quickDateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            const today = new Date();
            const fromDate = new Date(today);
            
            if (days === 1) {
                fromDate.setDate(today.getDate());
            } else {
                fromDate.setDate(today.getDate() - days + 1);
            }
            
            document.getElementById('date_from').value = fromDate.toISOString().split('T')[0];
            document.getElementById('date_to').value = today.toISOString().split('T')[0];
            
            // Highlight selected button
            quickDateBtns.forEach(b => {
                b.classList.remove('border-orange-300', 'bg-orange-50');
                b.classList.add('border-gray-300');
            });
            this.classList.remove('border-gray-300');
            this.classList.add('border-orange-300', 'bg-orange-50');
        });
    });
    
    // Form submission
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        // Add current filters
        if (currentFilters.type) params.append('type', currentFilters.type);
        if (currentFilters.status) params.append('status', currentFilters.status);
        
        // Add form fields
        for (let [key, value] of formData.entries()) {
            if (value) {
                params.append(key, value);
            }
        }
        
        // Redirect with filters
        window.location.href = '{{ route("customer.wallet.transactions") }}?' + params.toString();
    });
    
    // Reset filters
    resetFiltersBtn.addEventListener('click', function() {
        // Reset form
        filterForm.reset();
        
        // Reset tabs
        filterTabs.forEach(tab => {
            tab.classList.remove('active', 'border-orange-500', 'bg-orange-50', 'text-orange-600');
        });
        statusTabs.forEach(tab => {
            tab.classList.remove('active', 'border-orange-500', 'bg-orange-50', 'text-orange-600');
        });
        
        // Reset first tabs as active
        if (filterTabs.length > 0) {
            filterTabs[0].classList.add('active', 'border-orange-500', 'bg-orange-50', 'text-orange-600');
        }
        if (statusTabs.length > 0) {
            statusTabs[0].classList.add('active', 'border-orange-500', 'bg-orange-50', 'text-orange-600');
        }
        
        // Reset quick date buttons
        quickDateBtns.forEach(b => {
            b.classList.remove('border-orange-300', 'bg-orange-50');
            b.classList.add('border-gray-300');
        });
        
        // Reset filters
        currentFilters = {
            type: '',
            status: '',
            date_from: '',
            date_to: '',
            amount_from: '',
            amount_to: ''
        };
        
        // Redirect to clean page
        window.location.href = '{{ route("customer.wallet.transactions") }}';
    });
    
    function applyFilters() {
        // This function can be used for real-time filtering if needed
        // For now, we'll use form submission for server-side filtering
    }
    
    // Initialize filters from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    
    // Set active states based on URL parameters
    const typeParam = urlParams.get('type');
    const statusParam = urlParams.get('status');
    
    if (typeParam) {
        filterTabs.forEach(tab => {
            if (tab.dataset.type === typeParam) {
                tab.classList.add('active', 'border-orange-500', 'bg-orange-50', 'text-orange-600');
                currentFilters.type = typeParam;
            }
        });
    }
    
    if (statusParam) {
        statusTabs.forEach(tab => {
            if (tab.dataset.status === statusParam) {
                tab.classList.add('active', 'border-orange-500', 'bg-orange-50', 'text-orange-600');
                currentFilters.status = statusParam;
            }
        });
    }
    
    // Set form values from URL parameters
    ['date_from', 'date_to', 'amount_from', 'amount_to'].forEach(field => {
        const value = urlParams.get(field);
        if (value) {
            const input = document.getElementById(field);
            if (input) {
                input.value = value;
            }
        }
    });
});
</script>
</x-customer-container>
@endsection
