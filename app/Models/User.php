<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'image',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get all booking requests for this user.
     */
    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class, 'customer_id');
    }

    /**
     * Get all bookings for this user.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    /**
     * वेंडर के अधीन काम करने वाले सुपरवाइज़र्स
     */
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'vendor_supervisors', 'vendor_id', 'supervisor_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * सुपरवाइज़र किस वेंडर के अधीन काम करता है
     */
    public function vendorOf()
    {
        return $this->belongsToMany(User::class, 'vendor_supervisors', 'supervisor_id', 'vendor_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * वेंडर का डिजिटल वॉलेट
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
