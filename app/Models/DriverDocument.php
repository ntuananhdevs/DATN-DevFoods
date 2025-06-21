<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'license_number',
        'license_class',
        'license_expiry',
        'license_front',
        'license_back',
        'id_card_front',
        'id_card_back',
        'vehicle_type',
        'vehicle_registration',
        'vehicle_color',
        'license_plate',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}