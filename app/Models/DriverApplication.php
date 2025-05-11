<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'email', 'phone_number', 'date_of_birth', 'gender',
        'id_card_number', 'id_card_issue_date', 'id_card_issue_place',
        'address', 'city', 'district',
        'vehicle_type', 'vehicle_model', 'vehicle_color', 'license_plate', 'driver_license_number',
        'id_card_front_image', 'id_card_back_image', 'driver_license_image', 'profile_image', 'vehicle_registration_image',
        'bank_name', 'bank_account_number', 'bank_account_name',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        'status', 'admin_notes'
    ];

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }
}