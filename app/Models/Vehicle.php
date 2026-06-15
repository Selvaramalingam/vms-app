<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'fc_expiry' => 'date',
        'insurance_expiry' => 'date',
        'permit_expiry' => 'date',
        'tax_expiry' => 'date',
        'pollution_expiry' => 'date',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
    ];

    public function drivers()
    {
        return $this->hasMany(Driver::class, 'assigned_vehicle');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function fuelEntries()
    {
        return $this->hasMany(FuelEntry::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function expiries()
    {
        return $this->hasMany(VehicleExpiry::class);
    }
}
