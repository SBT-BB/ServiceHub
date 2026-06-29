<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorSupervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'supervisor_id',
        'status',
    ];

    /**
     * वेंडर यूजर
     */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * सुपरवाइज़र यूजर
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
