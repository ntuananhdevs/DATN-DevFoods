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
    
    .filter-tab {
        transition: all 0.3s eazse;
        cursor: pointer;
    }
    
    .filter-tab.active {
        background-color: #f97316;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
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

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex space-x-2">
                <button class="filter-tab px-4 py-2 rounded-lg border active" data-type="">Tất Cả</button>
                <button class="filter-tab px-4 py-2 rounded-lg border" data-type="deposit">Nạp Tiền</button>
                <button class="filter-tab px-4 py-2 rounded-lg border" data-type="withdraw">Rút Tiền</button>
            </div>
            
            <div class="flex space-x-2">
                <button class="filter-tab px-4 py-2 rounded-lg border" data-status="">Tất Cả Trạng Thái</button>
                <button class="filter-tab px-4 py-2 rounded-lg border" data-status="completed">Hoàn Thành</button>
                <button class="filter-tab px-4 py-2 rounded-lg border" data-status="pending">Đang Xử Lý</button>
                <button class="filter-tab px-4 py-2 rounded-lg border" data-status="failed">Thất Bại</button>
            </div>
        </div>
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
                            <p class="text-lg font-bold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                            </p>
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                {{ $transaction->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($transaction->status == 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                <i class="fas {{ $transaction->status == 'completed' ? 'fa-check' : 
                                               ($transaction->status == 'pending' ? 'fa-clock' : 'fa-times') }} mr-1"></i>
                                {{ $transaction->status_text }}
                            </span>
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
    const filterTabs = document.querySelectorAll('.filter-tab');
    const transactionCards = document.querySelectorAll('.transaction-card');
    
    let currentTypeFilter = '';
    let currentStatusFilter = '';
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from siblings
            if (this.dataset.type !== undefined) {
                // Type filter
                document.querySelectorAll('[data-type]').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentTypeFilter = this.dataset.type;
            } else if (this.dataset.status !== undefined) {
                // Status filter
                document.querySelectorAll('[data-status]').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentStatusFilter = this.dataset.status;
            }
            
            // Apply filters
            filterTransactions();
        });
    });
    
    function filterTransactions() {
        transactionCards.forEach(card => {
            const cardType = card.dataset.type;
            const cardStatus = card.dataset.status;
            
            const typeMatch = !currentTypeFilter || cardType === currentTypeFilter;
            const statusMatch = !currentStatusFilter || cardStatus === currentStatusFilter;
            
            if (typeMatch && statusMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
});
</script>
</x-customer-container>
@endsection
