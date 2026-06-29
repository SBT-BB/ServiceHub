<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'booking_id',
        'amount',
        'type',
        'description',
    ];

    /**
     * यह ट्रांजैक्शन किस वॉलेट से जुड़ा है
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * यह ट्रांजैक्शन किस बुकिंग से जुड़ा है (यदि कोई हो)
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
