<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Deduct 20% commission from vendor wallet upon booking completion.
     */
    public function deductCommission(Booking $booking)
    {
        if (!$booking->vendor_id) {
            return;
        }

        // We only deduct commission if it hasn't been deducted already
        $alreadyDeducted = WalletTransaction::where('booking_id', $booking->id)
            ->where('type', 'debit')
            ->exists();

        if ($alreadyDeducted) {
            return;
        }

        DB::transaction(function () use ($booking) {
            $amount = $booking->amount;
            $commission = $amount * 0.20;
            $settlement = $amount * 0.80;

            // 1. Update Booking with commission and settlement amounts
            $booking->update([
                'vendor_commission_amount' => $commission,
                'vendor_settlement_amount' => $settlement,
                'tracking_status' => 'completed',
                'status' => 'completed',
            ]);

            // 2. Fetch or create Vendor's wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $booking->vendor_id],
                ['balance' => 0.00]
            );

            // 3. Deduct commission from wallet balance
            $wallet->balance -= $commission;
            $wallet->save();

            // 4. Create wallet transaction log (debit)
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'booking_id' => $booking->id,
                'amount' => -$commission,
                'type' => 'debit',
                'description' => '20% platform commission debited for Booking #' . $booking->booking_number,
            ]);
        });
    }
}
