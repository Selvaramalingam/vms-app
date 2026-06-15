<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExpiry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'expiry_date' => 'date',
        'renewed_on' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
