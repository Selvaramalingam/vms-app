<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function payments()
    {
        return $this->hasMany(TripPayment::class);
    }

    public function payment()
    {
        return $this->hasOne(TripPayment::class);
    }

    public function fuelEntries()
    {
        return $this->hasMany(FuelEntry::class);
    }
    
    public function getProfitAttribute()
    {
        $rent = $this->rent_amount;
        $fuel = $this->fuel_cost;
        $maintenance = 0; // Or calculate from related if applicable
        return $rent - $fuel - $maintenance;
    }
}
