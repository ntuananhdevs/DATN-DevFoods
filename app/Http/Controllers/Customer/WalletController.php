<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\WalletTransaction;
use Exception;

class WalletController extends Controller
{
    /**
     * Hiển thị trang nạp tiền
     */
    public function index()
    {
        $user = Auth::user();
        
        // Lấy lịch sử giao dịch gần nhất (10 giao dịch)
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

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

            // Tạo giao dịch pending
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'payment_method' => 'vnpay',
                'status' => 'pending',
                'description' => "Nạp tiền vào ví qua VNPay",
                'transaction_code' => 'WALLET_' . time() . '_' . $user->id
            ]);

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
                WalletTransaction::create([
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
        
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->status, function($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json($transactions);
        }

        return view('customer.wallet.transactions', compact('transactions'));
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
                    
                    DB::commit();
                    
                    return redirect()->route('customer.wallet.index')->with('success', 'Nạp tiền thành công!');
                } catch (Exception $e) {
                    DB::rollBack();
                    $transaction->update(['status' => 'failed']);
                    return redirect()->route('customer.wallet.index')->with('error', 'Có lỗi xảy ra khi xử lý giao dịch');
                }
            } else {
                // Thanh toán thất bại
                $transaction->update(['status' => 'failed']);
                return redirect()->route('customer.wallet.index')->with('error', 'Thanh toán không thành công');
            }
        } else {
            // Chữ ký không hợp lệ
            $transaction->update(['status' => 'failed']);
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
                                    $transaction->update([
                                        'status' => 'completed',
                                        'processed_at' => now()
                                    ]);
                                    $transaction->user->increment('balance', $transaction->amount);
                                    DB::commit();
                                    
                                    $returnData['RspCode'] = '00';
                                    $returnData['Message'] = 'Confirm Success';
                                } catch (Exception $e) {
                                    DB::rollBack();
                                    $returnData['RspCode'] = '99';
                                    $returnData['Message'] = 'Unknown error';
                                }
                            } else {
                                $transaction->update(['status' => 'failed']);
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
