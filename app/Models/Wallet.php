<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * वॉलेट का मालिक (वेंडर)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * इस वॉलेट के सभी लेनदेन (ट्रांजैक्शन्स)
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }
}
