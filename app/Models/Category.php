<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'vehicle_id',
        'min_score',
        'max_score',
        'base_fare',
        'price_per_point',
        'weekend_surcharge_percent',
        'month_end_surcharge_percent',
        'peak_time_surcharge_percent',
        'peak_time_start',
        'peak_time_end',
        'status',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
