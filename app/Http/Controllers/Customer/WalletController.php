<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\WithdrawalRequestNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Events\Wallet\NewWalletTransactionReceived;
use App\Events\Wallet\WalletTransactionStatusUpdated;
use Exception;

class WalletController extends Controller
{

    /**
     * Hiển thị trang nạp tiền
     */
    public function index()
    {
        $user = Auth::user();
        
        // Tự động cập nhật expired transactions
        $this->autoExpireTransactions();
        
        // Lấy lịch sử giao dịch gần nhất (10 giao dịch)
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Model accessors sẽ tự động tính toán các thuộc tính cần thiết
        // Chỉ cần auto-expire transactions nếu cần
        $transactions->each(function ($transaction) {
            $transaction->autoExpireIfNeeded();
        });

        return view('customer.wallet.index', compact('user', 'transactions'));
    }

    /**
     * Xử lý nạp tiền chỉ qua VNPay
     */
    public function deposit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => [
                    'required',
                    'numeric',
                    'min:10000', // Tối thiểu 10,000 VND
                    'max:10000000' // Tối đa 10,000,000 VND
                ]
            ], [
                'amount.required' => 'Vui lòng nhập số tiền cần nạp',
                'amount.numeric' => 'Số tiền phải là một số hợp lệ',
                'amount.min' => 'Số tiền tối thiểu là 10,000 VND',
                'amount.max' => 'Số tiền tối đa là 10,000,000 VND'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $amount = $request->amount;

            // Tạo giao dịch pending với timeout 15 phút
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'payment_method' => 'vnpay',
                'status' => 'pending',
                'description' => "Nạp tiền vào ví qua VNPay",
                'transaction_code' => 'WALLET_' . time() . '_' . $user->id,
                'expires_at' => now()->addMinutes(15) // Hết hạn sau 15 phút
            ]);

            // Trigger event for new wallet transaction
            event(new NewWalletTransactionReceived($transaction));

            // Tích hợp VNPay
            return $this->createVnpayPayment($transaction);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình nạp tiền: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý rút tiền
     */
    public function withdraw(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => [
                    'required',
                    'numeric',
                    'min:50000', // Tối thiểu 50,000 VND
                    'max:5000000' // Tối đa 5,000,000 VND
                ],
                'bank_name' => 'required|string|max:255',
                'bank_account' => 'required|string|max:50',
                'account_holder' => 'required|string|max:255'
            ], [
                'amount.required' => 'Vui lòng nhập số tiền cần rút',
                'amount.numeric' => 'Số tiền phải là một số hợp lệ',
                'amount.min' => 'Số tiền tối thiểu là 50,000 VND',
                'amount.max' => 'Số tiền tối đa là 5,000,000 VND',
                'bank_name.required' => 'Vui lòng nhập tên ngân hàng',
                'bank_account.required' => 'Vui lòng nhập số tài khoản',
                'account_holder.required' => 'Vui lòng nhập tên chủ tài khoản'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $amount = $request->amount;

            // Kiểm tra số dư
            if ($user->balance < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số dư không đủ để thực hiện giao dịch'
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Trừ tiền từ tài khoản
                $user->decrement('balance', $amount);

                // Tạo giao dịch rút tiền
                $transaction = WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'withdraw',
                    'amount' => $amount,
                    'status' => 'pending',
                    'description' => "Rút tiền về {$request->bank_name} - {$request->bank_account}",
                    'transaction_code' => 'WDR_' . time() . '_' . $user->id,
                    'metadata' => json_encode([
                        'bank_name' => $request->bank_name,
                        'bank_account' => $request->bank_account,
                        'account_holder' => $request->account_holder
                    ])
                ]);

                // Trigger event for new wallet transaction
                event(new NewWalletTransactionReceived($transaction));

                // Gửi notification cho admin nếu amount lớn
                if ($amount >= 1000000) { // 1M VND
                    $this->notifyAdminWithdrawal($transaction);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu rút tiền đã được gửi. Chúng tôi sẽ xử lý trong vòng 24h.',
                    'new_balance' => number_format($user->fresh()->balance, 0, ',', '.')
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình rút tiền: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy lịch sử giao dịch
     */
    public function transactions(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 15);
        
        // Tự động cập nhật expired transactions trước khi hiển thị
        $this->autoExpireTransactions();
        
        $query = WalletTransaction::where('user_id', $user->id);
        
        // Filter by type
        if ($request->type && in_array($request->type, ['deposit', 'withdraw', 'payment', 'refund'])) {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->status && in_array($request->status, ['pending', 'completed', 'failed', 'cancelled', 'expired'])) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by amount range
        if ($request->amount_from) {
            $query->where('amount', '>=', $request->amount_from);
        }
        
        if ($request->amount_to) {
            $query->where('amount', '<=', $request->amount_to);
        }
        
        $transactions = $query->latest()->paginate($perPage);

        // Model accessors sẽ tự động tính toán các thuộc tính cần thiết
        $transactions->getCollection()->each(function ($transaction) {
            $transaction->autoExpireIfNeeded();
        });

        // Get statistics for the current user
        $stats = [
            'total_transactions' => WalletTransaction::where('user_id', $user->id)->count(),
            'total_deposits' => WalletTransaction::where('user_id', $user->id)->deposits()->where('status', 'completed')->count(),
            'total_withdrawals' => WalletTransaction::where('user_id', $user->id)->withdrawals()->where('status', 'completed')->count(),
            'pending_count' => WalletTransaction::where('user_id', $user->id)->where('status', 'pending')->count(),
            'failed_count' => WalletTransaction::where('user_id', $user->id)->where('status', 'failed')->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'transactions' => $transactions,
                'stats' => $stats
            ]);
        }

        return view('customer.wallet.transactions', compact('transactions', 'stats'));
    }

    /**
     * Tạo thanh toán VNPay
     */
    private function createVnpayPayment($transaction)
    {
        // VNPay Configuration
        $vnp_TmnCode = env('VNPAY_TMN_CODE', ''); // Mã định danh merchant
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', ''); // Secret key
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route('customer.wallet.vnpay.return');
        
        $vnp_TxnRef = $transaction->transaction_code; // Mã giao dịch
        $vnp_Amount = $transaction->amount; // Số tiền thanh toán
        $vnp_Locale = 'vn'; // Ngôn ngữ
        $vnp_IpAddr = $this->getClientIp();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount * 100, // VNPay yêu cầu nhân 100
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => "Nap tien vi: " . $transaction->transaction_code,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes'))
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json([
            'success' => true,
            'message' => 'Chuyển hướng đến VNPay để thanh toán',
            'redirect_url' => $vnp_Url
        ]);
    }

    /**
     * Xử lý callback return từ VNPay
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', '');
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = array();
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // Tìm giao dịch
        $transaction = WalletTransaction::where('transaction_code', $request->vnp_TxnRef)->first();
        
        if (!$transaction) {
            return redirect()->route('customer.wallet.index')->with('error', 'Không tìm thấy giao dịch');
        }

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                // Thanh toán thành công
                DB::beginTransaction();
                try {
                    $oldStatus = $transaction->status;
                    $transaction->update([
                        'status' => 'completed',
                        'processed_at' => now(),
                        'metadata' => json_encode([
                            'vnp_TransactionNo' => $request->vnp_TransactionNo,
                            'vnp_BankCode' => $request->vnp_BankCode,
                            'vnp_PayDate' => $request->vnp_PayDate
                        ])
                    ]);

                    // Cộng tiền vào tài khoản
                    $transaction->user->increment('balance', $transaction->amount);
                    
                    // Trigger event for status update
                    event(new WalletTransactionStatusUpdated($transaction, $oldStatus));
                    
                    DB::commit();
                    
                    return redirect()->route('customer.wallet.index')->with('success', 'Nạp tiền thành công!');
                } catch (Exception $e) {
                    DB::rollBack();
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('customer.wallet.index')->with('error', 'Có lỗi xảy ra khi xử lý giao dịch');
                }
            } else {
                // Thanh toán thất bại
                $oldStatus = $transaction->status;
                $transaction->update(['status' => 'failed']);
                event(new WalletTransactionStatusUpdated($transaction, $oldStatus));
                return redirect()->route('customer.wallet.index')->with('error', 'Thanh toán không thành công');
            }
        } else {
            // Chữ ký không hợp lệ
            $oldStatus = $transaction->status;
            $transaction->update(['status' => 'failed']);
            event(new WalletTransactionStatusUpdated($transaction, $oldStatus));
            return redirect()->route('customer.wallet.index')->with('error', 'Chữ ký không hợp lệ');
        }
    }

    /**
     * Xử lý IPN từ VNPay
     */
    public function vnpayIpn(Request $request)
    {
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', '');
        $inputData = array();
        $returnData = array();
        
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_Amount = $inputData['vnp_Amount'] / 100;
        $orderId = $inputData['vnp_TxnRef'];

        try {
            $transaction = WalletTransaction::where('transaction_code', $orderId)->first();
            
            if ($secureHash == $vnp_SecureHash) {
                if ($transaction != NULL) {
                    if ($transaction->amount == $vnp_Amount) {
                        if ($transaction->status == 'pending') {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                // Cập nhật thành công
                                DB::beginTransaction();
                                try {
                                    $oldStatus = $transaction->status;
                                    $transaction->update([
                                        'status' => 'completed',
                                        'processed_at' => now()
                                    ]);
                                    $transaction->user->increment('balance', $transaction->amount);
                                    
                                    // Trigger event for status update
                                    event(new WalletTransactionStatusUpdated($transaction, $oldStatus));
                                    
                                    DB::commit();
                                    
                                    $returnData['RspCode'] = '00';
                                    $returnData['Message'] = 'Confirm Success';
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    $returnData['RspCode'] = '99';
                                    $returnData['Message'] = 'Unknown error';
                                }
                            } else {
                                $oldStatus = $transaction->status;
                                $transaction->update(['status' => 'failed']);
                                event(new WalletTransactionStatusUpdated($transaction, $oldStatus));
                                $returnData['RspCode'] = '00';
                                $returnData['Message'] = 'Confirm Success';
                            }
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }
                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'Invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknown error';
        }

        return response()->json($returnData);
    }

    /**
     * Thanh toán lại giao dịch pending chưa hết hạn
     */
    public function retryPayment(Request $request, $transactionId)
    {
        try {
            $user = Auth::user();
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('user_id', $user->id)
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch hoặc giao dịch đã được xử lý'
                ], 404);
            }

            // Kiểm tra xem giao dịch có hết hạn chưa
            if ($transaction->expires_at && $transaction->expires_at < now()) {
                $transaction->update(['status' => 'expired']);
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch đã hết hạn. Vui lòng tạo giao dịch mới.'
                ], 422);
            }

            // Tạo lại VNPay payment với transaction hiện tại
            return $this->createVnpayPayment($transaction);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tự động đánh dấu expired cho các giao dịch hết hạn
     */
    public function expireTransactions()
    {
        try {
            $expiredCount = WalletTransaction::where('status', 'pending')
                ->where('expires_at', '<', now())
                ->whereNotNull('expires_at')
                ->update(['status' => 'expired']);

            $message = $expiredCount > 0 
                ? "Đã cập nhật {$expiredCount} giao dịch hết hạn" 
                : "Không có giao dịch hết hạn";
                
            return response()->json([
                'success' => true,
                'message' => $message,
                'expired_count' => $expiredCount
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin giao dịch với thời gian còn lại
     */
    public function getTransactionWithCountdown($transactionId)
    {
        try {
            $user = Auth::user();
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            // Tính thời gian còn lại
            $timeRemaining = null;
            $canRetry = false;

            if ($transaction->status === 'pending' && $transaction->expires_at) {
                $now = now();
                if ($transaction->expires_at > $now) {
                    $timeRemaining = $transaction->expires_at->diffInSeconds($now);
                    $canRetry = true;
                } else {
                    // Tự động cập nhật expired nếu chưa được cập nhật
                    $transaction->update(['status' => 'expired']);
                    $transaction->refresh();
                }
            }

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'formatted_amount' => number_format($transaction->amount, 0, ',', '.') . ' VND',
                    'status' => $transaction->status,
                    'status_text' => $this->getStatusText($transaction->status),
                    'description' => $transaction->description,
                    'transaction_code' => $transaction->transaction_code,
                    'created_at' => $transaction->created_at->format('d/m/Y H:i'),
                    'expires_at' => $transaction->expires_at ? $transaction->expires_at->format('d/m/Y H:i') : null,
                    'time_remaining_seconds' => $timeRemaining,
                    'can_retry' => $canRetry
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tiếp tục thanh toán - Tạo VNPay link mới với cùng transaction
     */
    public function continuePayment(Request $request, $transactionId)
    {
        try {
            $user = Auth::user();
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('user_id', $user->id)
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch hoặc giao dịch đã được xử lý'
                ], 404);
            }

            // Kiểm tra xem giao dịch có hết hạn chưa
            if ($transaction->expires_at && $transaction->expires_at < now()) {
                $transaction->update(['status' => 'expired']);
                return response()->json([
                    'success' => false,
                    'message' => 'Giao dịch đã hết hạn. Vui lòng tạo giao dịch mới.'
                ], 422);
            }

            // Gia hạn thêm 15 phút
            $transaction->update(['expires_at' => now()->addMinutes(15)]);

            // Tạo lại VNPay payment với transaction hiện tại
            return $this->createVnpayPayment($transaction);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hủy giao dịch - Chuyển trạng thái thành CANCELLED
     */
    public function cancelTransaction(Request $request, $transactionId)
    {
        try {
            $user = Auth::user();
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch hoặc giao dịch đã được xử lý'
                ], 404);
            }

            // Cập nhật trạng thái thành cancelled
            $transaction->update([
                'status' => 'cancelled',
                'processed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy giao dịch thành công',
                'transaction' => [
                    'id' => $transaction->id,
                    'status' => 'cancelled',
                    'status_text' => 'Đã Hủy'
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái giao dịch từ VNPay
     */
    public function checkTransactionStatus(Request $request, $transactionId)
    {
        try {
            $user = Auth::user();
            $transaction = WalletTransaction::where('id', $transactionId)
                ->where('user_id', $user->id)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            // Nếu giao dịch không phải VNPay hoặc đã hoàn thành/thất bại thì không cần check
            if ($transaction->payment_method !== 'vnpay' || 
                in_array($transaction->status, ['completed', 'failed', 'cancelled', 'expired'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trạng thái hiện tại của giao dịch',
                    'transaction' => [
                        'id' => $transaction->id,
                        'status' => $transaction->status,
                        'status_text' => $this->getStatusText($transaction->status),
                        'needs_refresh' => false
                    ]
                ]);
            }

            // Gọi API VNPay để kiểm tra trạng thái
            $vnpayStatus = $this->queryVNPayTransaction($transaction);

            if ($vnpayStatus['success']) {
                // Cập nhật trạng thái nếu có thay đổi từ VNPay
                if ($vnpayStatus['transaction_status'] === '00') {
                    // Giao dịch thành công
                    DB::beginTransaction();
                    try {
                        $transaction->update([
                            'status' => 'completed',
                            'processed_at' => now(),
                            'metadata' => json_encode(array_merge(
                                json_decode($transaction->metadata ?? '{}', true),
                                $vnpayStatus['metadata'] ?? []
                            ))
                        ]);
                        
                        // Cộng tiền vào tài khoản
                        $transaction->user->increment('balance', $transaction->amount);
                        
                        DB::commit();
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Giao dịch đã được hoàn thành!',
                            'transaction' => [
                                'id' => $transaction->id,
                                'status' => 'completed',
                                'status_text' => 'Hoàn Thành',
                                'needs_refresh' => true
                            ]
                        ]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        throw $e;
                    }
                } elseif (in_array($vnpayStatus['transaction_status'], ['01', '02', '03'])) {
                    // Giao dịch thất bại
                    $transaction->update(['status' => 'failed']);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Giao dịch đã thất bại',
                        'transaction' => [
                            'id' => $transaction->id,
                            'status' => 'failed',
                            'status_text' => 'Thất Bại',
                            'needs_refresh' => true
                        ]
                    ]);
                }
            }

            // Trạng thái không thay đổi
            return response()->json([
                'success' => true,
                'message' => 'Giao dịch vẫn đang chờ xử lý',
                'transaction' => [
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                    'status_text' => $this->getStatusText($transaction->status),
                    'needs_refresh' => false
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi kiểm tra trạng thái: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Query transaction status từ VNPay
     */
    private function queryVNPayTransaction($transaction)
    {
        try {
            $vnp_TmnCode = env('VNPAY_TMN_CODE', '');
            $vnp_HashSecret = env('VNPAY_HASH_SECRET', '');
            $vnp_Url = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";
            
            $vnp_RequestId = time() . rand(100, 999);
            $vnp_Version = "2.1.0";
            $vnp_Command = "querydr";
            $vnp_TxnRef = $transaction->transaction_code;
            $vnp_OrderInfo = "Query transaction: " . $vnp_TxnRef;
            $vnp_TransactionNo = "";
            $vnp_TransDate = $transaction->created_at->format('YmdHis');
            $vnp_CreateDate = date('YmdHis');
            $vnp_IpAddr = $this->getClientIp();

            $data = array(
                "vnp_RequestId" => $vnp_RequestId,
                "vnp_Version" => $vnp_Version,
                "vnp_Command" => $vnp_Command,
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_TransactionNo" => $vnp_TransactionNo,
                "vnp_TransDate" => $vnp_TransDate,
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_IpAddr" => $vnp_IpAddr
            );

            ksort($data);
            $hashData = "";
            $i = 0;
            foreach ($data as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $data['vnp_SecureHash'] = $vnpSecureHash;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $vnp_Url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $result = json_decode($response, true);
                
                return [
                    'success' => true,
                    'transaction_status' => $result['vnp_TransactionStatus'] ?? null,
                    'response_code' => $result['vnp_ResponseCode'] ?? null,
                    'metadata' => [
                        'vnp_TransactionNo' => $result['vnp_TransactionNo'] ?? null,
                        'vnp_BankCode' => $result['vnp_BankCode'] ?? null,
                        'vnp_PayDate' => $result['vnp_PayDate'] ?? null
                    ]
                ];
            }

            return ['success' => false, 'message' => 'Không thể kết nối tới VNPay'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Tự động đánh dấu expired cho các giao dịch hết hạn (helper method)
     */
    private function autoExpireTransactions()
    {
        $expiredCount = WalletTransaction::shouldBeExpired()->update(['status' => 'expired']);
        
        if ($expiredCount > 0) {
            \Log::info("Auto-expired {$expiredCount} wallet transactions");
        }
        
        return $expiredCount;
    }
    


    /**
     * Lấy text hiển thị cho status
     */
    private function getStatusText($status)
    {
        $statusTexts = [
            'pending' => 'Đang Xử Lý',
            'completed' => 'Hoàn Thành',
            'failed' => 'Thất Bại',
            'cancelled' => 'Đã Hủy',
            'expired' => 'Hết Hạn'
        ];

        return $statusTexts[$status] ?? 'Không Xác Định';
    }



    /**
     * Notify admin about withdrawal request
     */
    private function notifyAdminWithdrawal($transaction)
    {
        try {
            // Log the withdrawal request
            \Log::info('Admin notification: Large withdrawal request', [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'amount' => $transaction->amount,
            ]);
            
            // Send email notification to admin (simplified)
            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            if ($adminEmail) {
                Notification::route('mail', $adminEmail)
                    ->notify(new WithdrawalRequestNotification($transaction));
            }
            
        } catch (Exception $e) {
            \Log::error('Failed to notify admin about withdrawal', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
        }
    }



    /**
     * Lấy IP client
     */
    private function getClientIp()
    {
        $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return request()->ip();
    }
}
