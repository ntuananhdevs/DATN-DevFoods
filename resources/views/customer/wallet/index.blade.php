@extends('layouts.customer.fullLayoutMaster')

@section('title', 'FastFood - Ví Tiền')

@section('content')
<x-customer-container>
<style>
    .wallet-card {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(249, 115, 22, 0.3);
    }
    
    .transaction-item {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .transaction-item:hover {
        transform: translateX(5px);
        border-left-color: #f97316;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .deposit-form {
        background: rgba(249, 115, 22, 0.05);
        border: 2px solid rgba(249, 115, 22, 0.1);
        border-radius: 15px;
    }
    
    .amount-btn {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }
    
    .amount-btn:hover, .amount-btn.active {
        border-color: #f97316;
        background-color: #f97316;
        color: white;
        transform: translateY(-2px);
    }
    
    .payment-method {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid #e5e7eb;
    }
    
    .payment-method:hover, .payment-method.selected {
        border-color: #f97316;
        background-color: rgba(249, 115, 22, 0.1);
    }
</style>

<div class="container mx-auto px-4 py-8">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times cursor-pointer"></i>
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove()">
                <i class="fas fa-times cursor-pointer"></i>
            </span>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Ví Tiền</h1>
        <p class="text-gray-600">Quản lý số dư và lịch sử giao dịch của bạn</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Wallet Balance Card -->
        <div class="lg:col-span-1">
            <div class="wallet-card text-white p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Số Dư Hiện Tại</h3>
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
                <div class="text-3xl font-bold mb-2" id="current-balance">
                    {{ number_format($user->balance ?? 0, 0, ',', '.') }} VND
                </div>
                <p class="text-orange-200 text-sm">Cập nhật lần cuối: {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Thao Tác Nhanh</h4>
                <div class="space-y-3">
                    <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-4 rounded-lg transition duration-300" onclick="showDepositModal()">
                        <i class="fas fa-plus mr-2"></i> Nạp Tiền
                    </button>
                    <button class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-lg transition duration-300" onclick="showWithdrawModal()">
                        <i class="fas fa-minus mr-2"></i> Rút Tiền
                    </button>
                    <a href="{{ route('customer.wallet.transactions') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg transition duration-300 text-center">
                        <i class="fas fa-history mr-2"></i> Lịch Sử Giao Dịch
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Transactions with Countdown -->
        @if(isset($pendingTransactions) && $pendingTransactions->count() > 0)
        <div class="lg:col-span-2">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-yellow-800">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        Giao Dịch Chờ Thanh Toán
                    </h3>
                    <span class="text-sm text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full">
                        <i class="fas fa-clock mr-1"></i>
                        Hết hạn sau 15 phút
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($pendingTransactions as $pending)
                    <div class="bg-white border border-yellow-300 rounded-lg p-4" id="pending-transaction-{{ $pending['id'] }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center mr-4">
                                    <i class="fas fa-credit-card text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">Nạp tiền: {{ number_format($pending['amount'], 0, ',', '.') }} VND</p>
                                    <p class="text-sm text-gray-500">Mã GD: {{ $pending['transaction_code'] }}</p>
                                    <p class="text-sm text-gray-500">Tạo lúc: {{ \Carbon\Carbon::parse($pending['created_at'])->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="mb-2">
                                    <span class="text-lg font-bold text-red-600" id="countdown-{{ $pending['id'] }}">
                                        {{ gmdate('i:s', $pending['remaining_seconds']) }}
                                    </span>
                                </div>
                                <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition duration-300 retry-payment-btn" 
                                        data-transaction-id="{{ $pending['id'] }}" 
                                        onclick="retryPayment({{ $pending['id'] }})">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Thanh Toán Ngay
                                </button>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-3">
                            <div class="flex items-center text-yellow-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span class="text-sm">
                                    <strong>Lưu ý:</strong> Giao dịch này sẽ hết hạn sau 
                                    <span class="font-bold" id="countdown-text-{{ $pending['id'] }}">
                                        {{ gmdate('i:s', $pending['remaining_seconds']) }}
                                    </span>. 
                                    Vui lòng hoàn tất thanh toán trước khi hết hạn.
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Deposit/Withdraw Forms -->
        <div class="lg:col-span-2">
            <!-- Deposit Form -->
            <div class="deposit-form p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-plus-circle text-orange-500 mr-2"></i>
                    Nạp Tiền Vào Ví
                </h3>
                
                <form id="depositForm">
                    @csrf
                    <!-- Amount Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Chọn số tiền</label>
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <button type="button" class="amount-btn py-3 px-4 rounded-lg text-center font-medium" data-amount="50000">50,000 VND</button>
                            <button type="button" class="amount-btn py-3 px-4 rounded-lg text-center font-medium" data-amount="100000">100,000 VND</button>
                            <button type="button" class="amount-btn py-3 px-4 rounded-lg text-center font-medium" data-amount="200000">200,000 VND</button>
                            <button type="button" class="amount-btn py-3 px-4 rounded-lg text-center font-medium" data-amount="500000">500,000 VND</button>
                            <button type="button" class="amount-btn py-3 px-4 rounded-lg text-center font-medium" data-amount="1000000">1,000,000 VND</button>
                            <button type="button" class="amount-btn py-3 px-4 rounded-lg text-center font-medium" data-amount="2000000">2,000,000 VND</button>
                        </div>
                        
                        <div class="relative">
                            <input type="number" id="deposit-amount" name="amount" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   placeholder="Hoặc nhập số tiền khác" min="10000" max="10000000">
                            <span class="absolute right-3 top-3 text-gray-500">VND</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Tối thiểu: 10,000 VND - Tối đa: 10,000,000 VND</p>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Phương thức thanh toán</label>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="payment-method p-4 rounded-lg selected bg-orange-50 border-orange-500" data-method="vnpay">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418196384.png" alt="VNPay" class="w-10 h-10 mr-3">
                                        <div>
                                            <span class="font-medium text-lg">VNPay</span>
                                            <p class="text-sm text-gray-600">Thanh toán qua VNPay - An toàn & Nhanh chóng</p>
                                        </div>
                                    </div>
                                    <div class="text-green-500">
                                        <i class="fas fa-check-circle text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="payment-method" name="payment_method" value="vnpay">
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Hiện tại chỉ hỗ trợ thanh toán qua VNPay. Hỗ trợ tất cả ngân hàng trong nước.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 px-6 rounded-lg font-semibold transition duration-300 disabled:opacity-50" id="deposit-btn">
                        <i class="fas fa-credit-card mr-2"></i>
                        Nạp Tiền Ngay
                    </button>
                </form>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-clock text-blue-500 mr-2"></i>
                        Giao Dịch Gần Đây
                    </h3>
                    <a href="{{ route('customer.wallet.transactions') }}" class="text-orange-500 hover:text-orange-600 font-medium">
                        Xem tất cả <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                @if($transactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $transaction)
                        <div class="transaction-item bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center mr-4
                                        {{ $transaction->type == 'deposit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        <i class="fas {{ $transaction->type == 'deposit' ? 'fa-plus' : 'fa-minus' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $transaction->type_text }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold {{ $transaction->type == 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type == 'deposit' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                                    </p>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $transaction->status_badge_class == 'badge-success' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->status_badge_class == 'badge-warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $transaction->status_text }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">Chưa có giao dịch nào</p>
                        <p class="text-sm text-gray-400">Thực hiện giao dịch đầu tiên của bạn!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Rút Tiền</h3>
            <button onclick="hideWithdrawModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="withdrawForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Số tiền rút</label>
                <input type="number" name="amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" 
                       min="50000" max="5000000" placeholder="Nhập số tiền">
                <p class="text-xs text-gray-500 mt-1">Tối thiểu: 50,000 VND - Tối đa: 5,000,000 VND</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên ngân hàng</label>
                <input type="text" name="bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" 
                       placeholder="Ví dụ: Vietcombank">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Số tài khoản</label>
                <input type="text" name="bank_account" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" 
                       placeholder="Nhập số tài khoản">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tên chủ tài khoản</label>
                <input type="text" name="account_holder" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" 
                       placeholder="Tên chủ tài khoản">
            </div>
            
            <div class="flex space-x-3">
                <button type="button" onclick="hideWithdrawModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg transition duration-300">
                    Hủy
                </button>
                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg transition duration-300">
                    Rút Tiền
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo countdown cho pending transactions
    @if(isset($pendingTransactions) && $pendingTransactions->count() > 0)
        @foreach($pendingTransactions as $pending)
            startCountdown({{ $pending['id'] }}, {{ $pending['remaining_seconds'] }});
        @endforeach
    @endif
    
    // Amount button selection
    document.querySelectorAll('.amount-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('deposit-amount').value = this.dataset.amount;
        });
    });

    // Deposit form submission
    document.getElementById('depositForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = document.getElementById('deposit-amount').value;
        
        if (!amount || amount < 10000) {
            alert('Vui lòng nhập số tiền hợp lệ (tối thiểu 10,000 VND)');
            return;
        }
        
        if (amount > 10000000) {
            alert('Số tiền tối đa là 10,000,000 VND');
            return;
        }
        
        const formData = new FormData(this);
        document.getElementById('deposit-btn').disabled = true;
        document.getElementById('deposit-btn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang chuyển hướng...';
        
        fetch('{{ route("customer.wallet.deposit") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.redirect_url) {
                // Chuyển hướng đến VNPay
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Có lỗi xảy ra');
                document.getElementById('deposit-btn').disabled = false;
                document.getElementById('deposit-btn').innerHTML = '<i class="fas fa-credit-card mr-2"></i>Nạp Tiền Ngay';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại');
            document.getElementById('deposit-btn').disabled = false;
            document.getElementById('deposit-btn').innerHTML = '<i class="fas fa-credit-card mr-2"></i>Nạp Tiền Ngay';
        });
    });

    // Withdraw form submission
    document.getElementById('withdrawForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("customer.wallet.withdraw") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                hideWithdrawModal();
                if (data.new_balance) {
                    document.getElementById('current-balance').textContent = data.new_balance + ' VND';
                }
                location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại');
        });
    });
});

function showWithdrawModal() {
    document.getElementById('withdrawModal').classList.remove('hidden');
    document.getElementById('withdrawModal').classList.add('flex');
}

function hideWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('hidden');
    document.getElementById('withdrawModal').classList.remove('flex');
}

// Countdown timer cho pending transactions
function startCountdown(transactionId, remainingSeconds) {
    const countdownElement = document.getElementById(`countdown-${transactionId}`);
    const countdownTextElement = document.getElementById(`countdown-text-${transactionId}`);
    const transactionCard = document.getElementById(`pending-transaction-${transactionId}`);
    
    if (!countdownElement || !countdownTextElement || !transactionCard) return;
    
    let seconds = remainingSeconds;
    
    const timer = setInterval(() => {
        if (seconds <= 0) {
            // Giao dịch hết hạn
            clearInterval(timer);
            countdownElement.textContent = '00:00';
            countdownTextElement.textContent = '00:00';
            
            // Ẩn nút thanh toán và hiển thị thông báo hết hạn
            const retryBtn = transactionCard.querySelector('.retry-payment-btn');
            if (retryBtn) {
                retryBtn.disabled = true;
                retryBtn.innerHTML = '<i class="fas fa-clock mr-2"></i>Đã hết hạn';
                retryBtn.classList.remove('bg-orange-500', 'hover:bg-orange-600');
                retryBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
            }
            
            // Thay đổi màu sắc card
            transactionCard.classList.remove('border-yellow-300');
            transactionCard.classList.add('border-red-300', 'bg-red-50');
            
            // Hiển thị thông báo hết hạn
            const infoBox = transactionCard.querySelector('.bg-yellow-100');
            if (infoBox) {
                infoBox.classList.remove('bg-yellow-100', 'border-yellow-300', 'text-yellow-800');
                infoBox.classList.add('bg-red-100', 'border-red-300', 'text-red-800');
                infoBox.innerHTML = `
                    <div class="flex items-center text-red-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span class="text-sm">
                            <strong>Giao dịch đã hết hạn!</strong> Vui lòng tạo giao dịch mới để tiếp tục.
                        </span>
                    </div>
                `;
            }
            
            return;
        }
        
        const minutes = Math.floor(seconds / 60);
        const remainingSecs = seconds % 60;
        const timeString = `${minutes.toString().padStart(2, '0')}:${remainingSecs.toString().padStart(2, '0')}`;
        
        countdownElement.textContent = timeString;
        countdownTextElement.textContent = timeString;
        
        seconds--;
    }, 1000);
}

// Xử lý retry payment
function retryPayment(transactionId) {
    const button = document.querySelector(`[onclick="retryPayment(${transactionId})"]`);
    if (!button) return;
    
    // Disable button và hiển thị loading
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
    
    // Gọi API retry payment
    fetch('{{ route("customer.wallet.retry-payment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            transaction_id: transactionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect_url) {
            // Chuyển hướng đến VNPay
            window.location.href = data.redirect_url;
        } else {
            alert(data.message || 'Có lỗi xảy ra');
            // Reset button
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Thanh Toán Ngay';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra, vui lòng thử lại');
        // Reset button
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Thanh Toán Ngay';
    });
}
</script>
</x-customer-container>
@endsection
