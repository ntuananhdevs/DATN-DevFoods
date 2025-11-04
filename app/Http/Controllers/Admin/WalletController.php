<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\WalletTransaction;
use App\Models\User;
use Carbon\Carbon;
use App\Events\Wallet\WalletTransactionStatusUpdated;
use Exception;

class WalletController extends Controller
{
    /**
     * Display wallet overview with comprehensive statistics
     */
    public function index()
    {
        // Withdrawal Statistics
        $withdrawalStats = [
            'pending_count' => WalletTransaction::pendingWithdrawals()->count(),
            'pending_amount' => WalletTransaction::pendingWithdrawals()->sum('amount'),
            'today_count' => WalletTransaction::withdrawals()
                ->whereDate('created_at', today())
                ->count(),
            'today_amount' => WalletTransaction::withdrawals()
                ->whereDate('created_at', today())
                ->sum('amount'),
            'total_processed' => WalletTransaction::completedWithdrawals()->count(),
            'total_processed_amount' => WalletTransaction::completedWithdrawals()->sum('amount'),
            'this_month_count' => WalletTransaction::withdrawals()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'this_month_amount' => WalletTransaction::withdrawals()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Deposit Statistics
        $depositStats = [
            'pending_count' => WalletTransaction::where('type', 'deposit')
                ->where('status', 'pending')
                ->count(),
            'pending_amount' => WalletTransaction::where('type', 'deposit')
                ->where('status', 'pending')
                ->sum('amount'),
            'today_count' => WalletTransaction::where('type', 'deposit')
                ->whereDate('created_at', today())
                ->count(),
            'today_amount' => WalletTransaction::where('type', 'deposit')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'completed_count' => WalletTransaction::where('type', 'deposit')
                ->where('status', 'completed')
                ->count(),
            'completed_amount' => WalletTransaction::where('type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'this_month_count' => WalletTransaction::where('type', 'deposit')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'this_month_amount' => WalletTransaction::where('type', 'deposit')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // General Statistics
        $generalStats = [
            'total_users_with_wallet' => User::where('balance', '>', 0)->count(),
            'total_wallet_balance' => User::sum('balance'),
            'avg_wallet_balance' => User::where('balance', '>', 0)->avg('balance'),
            'failed_transactions_today' => WalletTransaction::where('status', 'failed')
                ->whereDate('created_at', today())
                ->count(),
            'expired_transactions_today' => WalletTransaction::where('status', 'expired')
                ->whereDate('created_at', today())
                ->count(),
        ];

        // Recent Transactions (last 10)
        $recentTransactions = WalletTransaction::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Top Users by Transaction Volume (this month)
        $topUsers = User::select('users.*')
            ->selectRaw('SUM(wallet_transactions.amount) as total_amount')
            ->selectRaw('COUNT(wallet_transactions.id) as transaction_count')
            ->join('wallet_transactions', 'users.id', '=', 'wallet_transactions.user_id')
            ->whereMonth('wallet_transactions.created_at', now()->month)
            ->whereYear('wallet_transactions.created_at', now()->year)
            ->groupBy('users.id')
            ->orderBy('total_amount', 'desc')
            ->take(10)
            ->get();

        return view('admin.wallet.index', compact(
            'withdrawalStats', 
            'depositStats', 
            'generalStats', 
            'recentTransactions',
            'topUsers'
        ));
    }

    /**
     * Display pending withdrawal requests
     */
    public function pendingWithdrawals()
    {
        $withdrawals = WalletTransaction::pendingWithdrawals()
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.wallet.pending-withdrawals', compact('withdrawals'));
    }

    /**
     * Display withdrawal history
     */
    public function withdrawalHistory(Request $request)
    {
        $query = WalletTransaction::withdrawals()->with('user');

        // Filter by status
        if ($request->status) {
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

        $withdrawals = $query->latest()->paginate(20);

        return view('admin.wallet.withdrawal-history', compact('withdrawals'));
    }

    /**
     * Display all deposit transactions
     */
    public function depositHistory(Request $request)
    {
        $query = WalletTransaction::where('type', 'deposit')->with('user');

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
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

        $deposits = $query->latest()->paginate(20);

        return view('admin.wallet.deposit-history', compact('deposits'));
    }

    /**
     * Display all wallet transactions (deposits + withdrawals)
     */
    public function allTransactions(Request $request)
    {
        $query = WalletTransaction::with('user');

        // Filter by type
        if ($request->type && in_array($request->type, ['deposit', 'withdraw', 'payment', 'refund'])) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->status && in_array($request->status, ['pending', 'completed', 'failed', 'cancelled', 'expired'])) {
            $query->where('status', $request->status);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
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

        // Search by transaction code
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_code', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $transactions = $query->latest()->paginate(20);

        // Get statistics for current filter
        $stats = [
            'total_count' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'pending_count' => (clone $query)->where('status', 'pending')->count(),
            'completed_count' => (clone $query)->where('status', 'completed')->count(),
            'failed_count' => (clone $query)->where('status', 'failed')->count(),
        ];

        return view('admin.wallet.all-transactions', compact('transactions', 'stats'));
    }

    /**
     * Show withdrawal details
     */
    public function showWithdrawal($id)
    {
        try {
            $withdrawal = WalletTransaction::with('user')
                ->where('type', 'withdraw')
                ->findOrFail($id);

            // Debug log
            \Log::info('Loading withdrawal detail', ['id' => $id, 'withdrawal' => $withdrawal->toArray()]);

            return view('admin.wallet.withdrawal-detail', compact('withdrawal'));
        } catch (\Exception $e) {
            \Log::error('Error loading withdrawal detail', ['id' => $id, 'error' => $e->getMessage()]);
            
            return response('<div class="text-center py-8 text-red-600">
                <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                <h3 class="text-lg font-medium mb-2">Không thể tải chi tiết giao dịch</h3>
                <p class="text-sm">Lỗi: ' . $e->getMessage() . '</p>
            </div>', 500);
        }
    }

    /**
     * Approve withdrawal request
     */
    public function approveWithdrawal(Request $request, $id)
    {
        try {
            $withdrawal = WalletTransaction::where('type', 'withdraw')
                ->where('status', 'pending')
                ->findOrFail($id);

            DB::beginTransaction();

            // Update withdrawal status
            $existingMetadata = $withdrawal->metadata;
            if (is_string($existingMetadata)) {
                $existingMetadata = json_decode($existingMetadata, true) ?? [];
            }
            
            $withdrawal->update([
                'status' => 'completed',
                'processed_at' => now(),
                'metadata' => array_merge($existingMetadata, [
                    'approved_by' => Auth::id(),
                    'approved_at' => now()->toISOString(),
                    'admin_notes' => $request->admin_notes
                ])
            ]);

            // Log the action
            \Log::info('Withdrawal approved', [
                'withdrawal_id' => $withdrawal->id,
                'approved_by' => Auth::id(),
                'amount' => $withdrawal->amount
            ]);

            // Trigger event for status update
            event(new WalletTransactionStatusUpdated($withdrawal, 'pending'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu rút tiền đã được duyệt thành công'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject withdrawal request
     */
    public function rejectWithdrawal(Request $request, $id)
    {
        try {
            $withdrawal = WalletTransaction::where('type', 'withdraw')
                ->where('status', 'pending')
                ->findOrFail($id);

            $request->validate([
                'reject_reason' => 'required|string|max:500'
            ]);

            DB::beginTransaction();

            // Update withdrawal status
            $existingMetadata = $withdrawal->metadata;
            if (is_string($existingMetadata)) {
                $existingMetadata = json_decode($existingMetadata, true) ?? [];
            }
            
            $withdrawal->update([
                'status' => 'failed',
                'processed_at' => now(),
                'metadata' => array_merge($existingMetadata, [
                    'rejected_by' => Auth::id(),
                    'rejected_at' => now()->toISOString(),
                    'reject_reason' => $request->reject_reason
                ])
            ]);

            // Refund the amount back to user (including processing fee)
            $processingFee = $existingMetadata['processing_fee'] ?? 0;
            $totalRefund = $withdrawal->amount + $processingFee;
            
            $withdrawal->user->increment('balance', $totalRefund);

            // Create refund transaction record
            WalletTransaction::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'refund',
                'amount' => $totalRefund,
                'status' => 'completed',
                'description' => "Hoàn tiền do từ chối rút tiền - {$withdrawal->transaction_code}",
                'transaction_code' => 'REFUND_' . time() . '_' . $withdrawal->user_id,
                'metadata' => [
                    'original_withdrawal_id' => $withdrawal->id,
                    'refund_reason' => 'Withdrawal rejected',
                    'processed_by' => Auth::id()
                ],
                'processed_at' => now()
            ]);

            // Log the action
            \Log::info('Withdrawal rejected and refunded', [
                'withdrawal_id' => $withdrawal->id,
                'rejected_by' => Auth::id(),
                'amount' => $withdrawal->amount,
                'refund_amount' => $totalRefund,
                'reason' => $request->reject_reason
            ]);

            // Trigger event for status update
            event(new WalletTransactionStatusUpdated($withdrawal, 'pending'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu rút tiền đã bị từ chối và hoàn tiền cho khách hàng'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive wallet analytics
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '7days');
        
        $startDate = match($period) {
            '24hours' => now()->subDay(),
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            default => now()->subDays(7)
        };

        // Daily transaction trends
        $dailyWithdrawals = WalletTransaction::withdrawals()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dailyDeposits = WalletTransaction::where('type', 'deposit')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown for withdrawals
        $withdrawalStatusBreakdown = WalletTransaction::withdrawals()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        // Status breakdown for deposits
        $depositStatusBreakdown = WalletTransaction::where('type', 'deposit')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        // Payment method breakdown for deposits
        $paymentMethodBreakdown = WalletTransaction::where('type', 'deposit')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get();

        // Hourly patterns
        $hourlyPattern = WalletTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Average processing time for completed transactions
        $avgProcessingTime = WalletTransaction::whereNotNull('processed_at')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, processed_at)) as avg_minutes')
            ->first();

        // Top users by transaction volume
        $topUsersByVolume = User::select('users.*')
            ->selectRaw('SUM(wallet_transactions.amount) as total_amount')
            ->selectRaw('COUNT(wallet_transactions.id) as transaction_count')
            ->join('wallet_transactions', 'users.id', '=', 'wallet_transactions.user_id')
            ->where('wallet_transactions.created_at', '>=', $startDate)
            ->groupBy('users.id')
            ->orderBy('total_amount', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d'),
                'daily_withdrawals' => $dailyWithdrawals,
                'daily_deposits' => $dailyDeposits,
                'withdrawal_status_breakdown' => $withdrawalStatusBreakdown,
                'deposit_status_breakdown' => $depositStatusBreakdown,
                'payment_method_breakdown' => $paymentMethodBreakdown,
                'hourly_pattern' => $hourlyPattern,
                'avg_processing_time_minutes' => $avgProcessingTime->avg_minutes ?? 0,
                'top_users_by_volume' => $topUsersByVolume,
            ]
        ]);
    }

    /**
     * Display user wallet management
     */
    public function userWallets(Request $request)
    {
        $query = User::select('users.*')
            ->selectRaw('
                (SELECT COUNT(*) FROM wallet_transactions WHERE user_id = users.id) as total_transactions,
                (SELECT SUM(amount) FROM wallet_transactions WHERE user_id = users.id AND type = "deposit" AND status = "completed") as total_deposits,
                (SELECT SUM(amount) FROM wallet_transactions WHERE user_id = users.id AND type = "withdraw" AND status = "completed") as total_withdrawals,
                (SELECT COUNT(*) FROM wallet_transactions WHERE user_id = users.id AND status = "pending") as pending_transactions
            ');

        // Filter by balance range
        if ($request->min_balance) {
            $query->where('balance', '>=', $request->min_balance);
        }
        if ($request->max_balance) {
            $query->where('balance', '<=', $request->max_balance);
        }

        // Filter by user activity
        if ($request->has_transactions) {
            $query->whereExists(function($q) {
                $q->select(DB::raw(1))
                  ->from('wallet_transactions')
                  ->whereRaw('wallet_transactions.user_id = users.id');
            });
        }

        // Search by name or email
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('balance', 'desc')->paginate(20);

        return view('admin.wallet.user-wallets', compact('users'));
    }

    /**
     * Show individual user wallet details
     */
    public function userWalletDetail($userId)
    {
        $user = User::findOrFail($userId);
        
        $transactions = WalletTransaction::where('user_id', $userId)
            ->latest()
            ->paginate(20);

        $stats = [
            'total_transactions' => WalletTransaction::where('user_id', $userId)->count(),
            'total_deposits' => WalletTransaction::where('user_id', $userId)
                ->where('type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount'),
            'total_withdrawals' => WalletTransaction::where('user_id', $userId)
                ->where('type', 'withdraw')
                ->where('status', 'completed')
                ->sum('amount'),
            'pending_transactions' => WalletTransaction::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),
            'failed_transactions' => WalletTransaction::where('user_id', $userId)
                ->where('status', 'failed')
                ->count(),
        ];

        return view('admin.wallet.user-wallet-detail', compact('user', 'transactions', 'stats'));
    }



    /**
     * Batch process withdrawals
     */
    public function batchProcess(Request $request)
    {
        try {
            $request->validate([
                'withdrawal_ids' => 'required|array',
                'withdrawal_ids.*' => 'exists:wallet_transactions,id',
                'action' => 'required|in:approve,reject',
                'batch_notes' => 'nullable|string|max:500'
            ]);

            $withdrawalIds = $request->withdrawal_ids;
            $action = $request->action;
            $processed = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($withdrawalIds as $id) {
                try {
                    $withdrawal = WalletTransaction::where('type', 'withdraw')
                        ->where('status', 'pending')
                        ->findOrFail($id);

                    $existingMetadata = $withdrawal->metadata;
                    if (is_string($existingMetadata)) {
                        $existingMetadata = json_decode($existingMetadata, true) ?? [];
                    }
                    
                    if ($action === 'approve') {
                        $withdrawal->update([
                            'status' => 'completed',
                            'processed_at' => now(),
                            'metadata' => array_merge($existingMetadata, [
                                'approved_by' => Auth::id(),
                                'approved_at' => now()->toISOString(),
                                'batch_notes' => $request->batch_notes,
                                'batch_processed' => true
                            ])
                        ]);
                    } else {
                        $withdrawal->update([
                            'status' => 'failed',
                            'processed_at' => now(),
                            'metadata' => array_merge($existingMetadata, [
                                'rejected_by' => Auth::id(),
                                'rejected_at' => now()->toISOString(),
                                'reject_reason' => $request->batch_notes ?? 'Batch rejection',
                                'batch_processed' => true
                            ])
                        ]);

                        // Refund for rejected withdrawals
                        $processingFee = $existingMetadata['processing_fee'] ?? 0;
                        $totalRefund = $withdrawal->amount + $processingFee;
                        $withdrawal->user->increment('balance', $totalRefund);
                    }

                    $processed++;

                } catch (Exception $e) {
                    $errors[] = "ID {$id}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã xử lý {$processed} yêu cầu rút tiền",
                'processed' => $processed,
                'errors' => $errors
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export wallet transactions to CSV
     */
    public function exportTransactions(Request $request)
    {
        try {
            $query = WalletTransaction::with('user');

            // Apply same filters as allTransactions method
            if ($request->type && in_array($request->type, ['deposit', 'withdraw', 'payment', 'refund'])) {
                $query->where('type', $request->type);
            }

            if ($request->status && in_array($request->status, ['pending', 'completed', 'failed', 'cancelled', 'expired'])) {
                $query->where('status', $request->status);
            }

            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $transactions = $query->latest()->get();

            $filename = 'wallet_transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($transactions) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'ID',
                    'User Name',
                    'User Email',
                    'Type',
                    'Amount',
                    'Status',
                    'Payment Method',
                    'Transaction Code',
                    'Description',
                    'Created At',
                    'Processed At',
                    'Bank Info'
                ]);

                // CSV Data
                foreach ($transactions as $transaction) {
                    $bankInfo = '';
                    if ($transaction->metadata && isset($transaction->metadata['bank_name'])) {
                        $metadata = is_string($transaction->metadata) ? json_decode($transaction->metadata, true) : $transaction->metadata;
                        $bankInfo = ($metadata['bank_name'] ?? '') . ' - ' . ($metadata['bank_account'] ?? '');
                    }

                    fputcsv($file, [
                        $transaction->id,
                        $transaction->user->name ?? 'N/A',
                        $transaction->user->email ?? 'N/A',
                        $transaction->type,
                        $transaction->amount,
                        $transaction->status,
                        $transaction->payment_method ?? 'N/A',
                        $transaction->transaction_code,
                        $transaction->description,
                        $transaction->created_at->format('Y-m-d H:i:s'),
                        $transaction->processed_at ? $transaction->processed_at->format('Y-m-d H:i:s') : 'N/A',
                        $bankInfo
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force expire pending transactions
     */
    public function forceExpireTransactions(Request $request)
    {
        try {
            $expiredCount = WalletTransaction::where('status', 'pending')
                ->where('expires_at', '<', now())
                ->whereNotNull('expires_at')
                ->update(['status' => 'expired']);

            \Log::info('Admin forced expiration of transactions', [
                'admin_id' => Auth::id(),
                'expired_count' => $expiredCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã hết hạn {$expiredCount} giao dịch",
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
     * Get system wallet health check
     */
    public function systemHealthCheck()
    {
        try {
            $health = [
                'total_system_balance' => User::sum('balance'),
                'total_completed_deposits' => WalletTransaction::where('type', 'deposit')
                    ->where('status', 'completed')
                    ->sum('amount'),
                'total_completed_withdrawals' => WalletTransaction::where('type', 'withdraw')
                    ->where('status', 'completed')
                    ->sum('amount'),
                'pending_transactions_count' => WalletTransaction::where('status', 'pending')->count(),
                'pending_transactions_amount' => WalletTransaction::where('status', 'pending')->sum('amount'),
                'failed_transactions_today' => WalletTransaction::where('status', 'failed')
                    ->whereDate('created_at', today())
                    ->count(),
                'expired_transactions_today' => WalletTransaction::where('status', 'expired')
                    ->whereDate('created_at', today())
                    ->count(),
                'oldest_pending_transaction' => WalletTransaction::where('status', 'pending')
                    ->oldest()
                    ->first(),
                'users_with_negative_balance' => User::where('balance', '<', 0)->count(),
            ];

            // Calculate balance discrepancy (should be close to 0 in a healthy system)
            $health['balance_discrepancy'] = $health['total_system_balance'] - 
                ($health['total_completed_deposits'] - $health['total_completed_withdrawals']);

            // System health score (0-100)
            $healthScore = 100;
            if ($health['users_with_negative_balance'] > 0) $healthScore -= 20;
            if ($health['failed_transactions_today'] > 10) $healthScore -= 15;
            if ($health['expired_transactions_today'] > 5) $healthScore -= 10;
            if (abs($health['balance_discrepancy']) > 1000) $healthScore -= 25;
            if ($health['pending_transactions_count'] > 100) $healthScore -= 10;

            $health['health_score'] = max(0, $healthScore);
            $health['health_status'] = $healthScore >= 80 ? 'healthy' : ($healthScore >= 60 ? 'warning' : 'critical');

            return response()->json([
                'success' => true,
                'health' => $health
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update transaction status (for maintenance)
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'transaction_ids' => 'required|array',
                'transaction_ids.*' => 'exists:wallet_transactions,id',
                'new_status' => 'required|in:pending,completed,failed,cancelled,expired',
                'admin_notes' => 'nullable|string|max:500'
            ]);

            $updated = WalletTransaction::whereIn('id', $request->transaction_ids)
                ->update([
                    'status' => $request->new_status,
                    'processed_at' => in_array($request->new_status, ['completed', 'failed', 'cancelled']) ? now() : null,
                    'metadata' => DB::raw("JSON_SET(COALESCE(metadata, '{}'), '$.admin_bulk_update', '" . json_encode([
                        'admin_id' => Auth::id(),
                        'updated_at' => now()->toISOString(),
                        'notes' => $request->admin_notes
                    ]) . "')")
                ]);

            \Log::info('Admin bulk status update', [
                'admin_id' => Auth::id(),
                'transaction_ids' => $request->transaction_ids,
                'new_status' => $request->new_status,
                'updated_count' => $updated
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái cho {$updated} giao dịch",
                'updated_count' => $updated
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
