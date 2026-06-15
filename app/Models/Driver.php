<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
