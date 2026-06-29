<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingVendorRequest extends Model
{
    use HasFactory;

    protected $table = 'booking_vendor_requests';

    protected $fillable = [
        'booking_id',
        'vendor_id',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
