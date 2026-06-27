<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'customer_id',
        'booking_request_id',
        'pickup_location',
        'drop_location',
        'pickup_latitude',
        'pickup_longitude',
        'drop_latitude',
        'drop_longitude',
        'shifting_date',
        'shifting_time',
        'floors',
        'amount',
        'status',
        
        // New Fields
        'pickup_contact_name',
        'pickup_contact_mobile',
        'drop_contact_name',
        'drop_contact_mobile',
        'total_distance',
        'total_volume_score',
        'category_id',
        'vehicle_id',
        'base_fare',
        'distance_charges',
        'addon_charges',
        'floor_charges',
        'weekend_charges',
        'month_end_charges',
        'loading_charge',
        'unloading_charge',
        'packing_charge',
        'labour_charge',
        'advance_amount',
        'remaining_amount',
        'advance_payment_status',
        'remaining_payment_status',
        'tracking_status',
        
        // Vendor & Settlement Fields
        'vendor_id',
        'vendor_commission_amount',
        'vendor_settlement_amount',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = self::generateUniqueBookingNumber();
            }
        });
    }

    /**
     * Generate unique booking number
     */
    public static function generateUniqueBookingNumber()
    {
        $prefix = 'SH-' . date('Ymd') . '-';
        do {
            $number = $prefix . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('booking_number', $number)->exists());

        return $number;
    }

    /**
     * Get the customer associated with the booking.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the booking request associated with the booking.
     */
    public function bookingRequest()
    {
        return $this->belongsTo(BookingRequest::class, 'booking_request_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'booking_items')
            ->withPivot('quantity', 'calculated_volume_score')
            ->withTimestamps();
    }

    public function addOns()
    {
        return $this->belongsToMany(AddOn::class, 'booking_add_ons')
            ->withPivot('price')
            ->withTimestamps();
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class)->orderBy('created_at', 'asc');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
