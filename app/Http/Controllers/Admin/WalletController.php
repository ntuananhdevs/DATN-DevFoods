<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;
use App\Models\User;
use Exception;

class WalletController extends Controller
{
    /**
     * Display wallet overview
     */
    public function index()
    {
        $stats = [
            'pending_withdrawals' => WalletTransaction::pendingWithdrawals()->count(),
            'pending_amount' => WalletTransaction::pendingWithdrawals()->sum('amount'),
            'today_withdrawals' => WalletTransaction::withdrawals()
                ->whereDate('created_at', today())
                ->count(),
            'today_amount' => WalletTransaction::withdrawals()
                ->whereDate('created_at', today())
                ->sum('amount'),
            'total_processed' => WalletTransaction::completedWithdrawals()->count(),
            'total_processed_amount' => WalletTransaction::completedWithdrawals()->sum('amount'),
        ];

        return view('admin.wallet.index', compact('stats'));
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
     * Show withdrawal details
     */
    public function showWithdrawal($id)
    {
        $withdrawal = WalletTransaction::with('user')
            ->where('type', 'withdraw')
            ->findOrFail($id);

        return view('admin.wallet.withdrawal-detail', compact('withdrawal'));
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
            $withdrawal->update([
                'status' => 'completed',
                'processed_at' => now(),
                'metadata' => array_merge($withdrawal->metadata ?? [], [
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
            $withdrawal->update([
                'status' => 'failed',
                'processed_at' => now(),
                'metadata' => array_merge($withdrawal->metadata ?? [], [
                    'rejected_by' => Auth::id(),
                    'rejected_at' => now()->toISOString(),
                    'reject_reason' => $request->reject_reason
                ])
            ]);

            // Refund the amount back to user (including processing fee)
            $processingFee = $withdrawal->metadata['processing_fee'] ?? 0;
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
     * Get withdrawal analytics
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

        $withdrawals = WalletTransaction::withdrawals()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $statusBreakdown = WalletTransaction::withdrawals()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'daily_withdrawals' => $withdrawals,
                'status_breakdown' => $statusBreakdown,
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => now()->format('Y-m-d')
            ]
        ]);
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

                    if ($action === 'approve') {
                        $withdrawal->update([
                            'status' => 'completed',
                            'processed_at' => now(),
                            'metadata' => array_merge($withdrawal->metadata ?? [], [
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
                            'metadata' => array_merge($withdrawal->metadata ?? [], [
                                'rejected_by' => Auth::id(),
                                'rejected_at' => now()->toISOString(),
                                'reject_reason' => $request->batch_notes ?? 'Batch rejection',
                                'batch_processed' => true
                            ])
                        ]);

                        // Refund for rejected withdrawals
                        $processingFee = $withdrawal->metadata['processing_fee'] ?? 0;
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
}
