<?php

namespace App\Http\Controllers\Backend\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class VendorWalletController extends Controller
{
    /**
     * Display Vendor Wallet Dashboard and Transaction History.
     */
    public function index(Request $request)
    {
        $vendorId = auth()->id();
        
        // Retrieve or create the vendor's wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $vendorId],
            ['balance' => 0.00]
        );

        if ($request->ajax()) {
            // Retrieve transactions for this specific wallet
            $transactions = WalletTransaction::with('booking')
                ->where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'desc');

            return datatables()->of($transactions)
                ->addColumn('booking_number', function ($transaction) {
                    if ($transaction->booking) {
                        return '<span class="font-monospace fw-semibold text-primary">' . $transaction->booking->booking_number . '</span>';
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->editColumn('type', function ($transaction) {
                    if ($transaction->type === 'credit') {
                        return '<span class="badge bg-success-focus text-success"><i class="ri-arrow-left-down-line align-bottom me-1"></i>Credit</span>';
                    } else {
                        return '<span class="badge bg-danger-focus text-danger"><i class="ri-arrow-right-up-line align-bottom me-1"></i>Debit</span>';
                    }
                })
                ->editColumn('amount', function ($transaction) {
                    $prefix = $transaction->type === 'credit' ? '+' : '';
                    $class = $transaction->type === 'credit' ? 'text-success' : 'text-danger';
                    return '<span class="' . $class . ' fw-semibold">' . $prefix . '₹' . number_format(abs($transaction->amount), 2) . '</span>';
                })
                ->editColumn('created_at', function ($transaction) {
                    return $transaction->created_at ? $transaction->created_at->format('d M Y, h:i A') : '—';
                })
                ->rawColumns(['booking_number', 'type', 'amount'])
                ->make(true);
        }

        // Summary metrics for the wallet cards
        $currentBalance = $wallet->balance;
        
        $totalEarned = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'credit')
            ->sum('amount');
            
        $totalCommissionPaid = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'debit')
            ->sum('amount');

        // Absolute value of debits/credits
        $totalEarned = abs($totalEarned);
        $totalCommissionPaid = abs($totalCommissionPaid);

        return view('Backend.Vendor.Wallet.index', compact('currentBalance', 'totalEarned', 'totalCommissionPaid'));
    }
}
