<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'updated_at',
    ];

    protected $dates = ['updated_at'];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}