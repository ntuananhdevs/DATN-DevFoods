<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DriverApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'email', 'phone_number', 'date_of_birth', 'gender',
        'id_card_number', 'id_card_issue_date', 'id_card_issue_place',
        'address',
        'vehicle_type', 'vehicle_model', 'vehicle_color', 'license_plate', 'driver_license_number',
        'id_card_front_image', 'id_card_back_image', 'driver_license_image', 'profile_image', 'vehicle_registration_image',
        'bank_name', 'bank_account_number', 'bank_account_name',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
        'status', 'admin_notes',
        'reviewed_at',
    ];

    protected $dates = [
        'date_of_birth',
        'id_card_issue_date',
        'reviewed_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'id_card_issue_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Get secure URL for profile image
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->getSecureImageUrl($this->profile_image);
    }

    /**
     * Get secure URL for ID card front image
     */
    public function getIdCardFrontImageUrlAttribute()
    {
        return $this->getSecureImageUrl($this->id_card_front_image);
    }

    /**
     * Get secure URL for ID card back image
     */
    public function getIdCardBackImageUrlAttribute()
    {
        return $this->getSecureImageUrl($this->id_card_back_image);
    }

    /**
     * Get secure URL for driver license image
     */
    public function getDriverLicenseImageUrlAttribute()
    {
        return $this->getSecureImageUrl($this->driver_license_image);
    }

    /**
     * Get secure URL for vehicle registration image
     */
    public function getVehicleRegistrationImageUrlAttribute()
    {
        return $this->getSecureImageUrl($this->vehicle_registration_image);
    }

    /**
     * Generate secure URL for S3 image
     */
    private function getSecureImageUrl($path, $expiration = 60)
    {
        if (!$path) {
            return null;
        }

        try {
            return Storage::disk('driver_documents')->temporaryUrl($path, now()->addMinutes($expiration));
        } catch (\Exception $e) {
            \Log::error('Failed to generate image URL for path: ' . $path . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for processed applications (approved or rejected)
     */
    public function scopeProcessed($query)
    {
        return $query->whereIn('status', ['approved', 'rejected']);
    }

    /**
     * Get age from date of birth
     */
    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get vehicle type in Vietnamese
     */
    public function getVehicleTypeVietnameseAttribute()
    {
        $types = [
            'motorcycle' => 'Xe máy',
            'car' => 'Ô tô',
            'bicycle' => 'Xe đạp',
        ];

        return $types[$this->vehicle_type] ?? $this->vehicle_type;
    }

    /**
     * Get gender in Vietnamese
     */
    public function getGenderVietnameseAttribute()
    {
        $genders = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];

        return $genders[$this->gender] ?? $this->gender;
    }

    /**
     * Get status in Vietnamese
     */
    public function getStatusVietnameseAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Đã từ chối',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Delete associated files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($application) {
            // Delete files from S3
            $filePaths = [
                $application->profile_image,
                $application->id_card_front_image,
                $application->id_card_back_image,
                $application->driver_license_image,
                $application->vehicle_registration_image,
            ];

            foreach ($filePaths as $path) {
                if ($path && Storage::disk('driver_documents')->exists($path)) {
                    Storage::disk('driver_documents')->delete($path);
                }
            }
        });
    }
}