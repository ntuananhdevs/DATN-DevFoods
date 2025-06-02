<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BranchImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'image_path',
        'caption',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Quan hệ với Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Accessor để lấy URL đầy đủ của ảnh
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    /**
     * Accessor để lấy URL đầy đủ của ảnh (public)
     */
    public function getFullImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    /**
     * Scope để lấy ảnh chính
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope để lấy ảnh phụ
     */
    public function scopeSecondary($query)
    {
        return $query->where('is_primary', false);
    }

    /**
     * Boot method để đảm bảo chỉ có 1 ảnh chính cho mỗi chi nhánh
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($branchImage) {
            if ($branchImage->is_primary) {
                // Đặt tất cả ảnh khác của chi nhánh này thành không phải ảnh chính
                static::where('branch_id', $branchImage->branch_id)
                      ->where('id', '!=', $branchImage->id)
                      ->update(['is_primary' => false]);
            }
        });

        static::deleting(function ($branchImage) {
            // Xóa file ảnh khi xóa record
            if (Storage::exists($branchImage->image_path)) {
                Storage::delete($branchImage->image_path);
            }
        });
    }

    /**
     * Method để set làm ảnh chính
     */
    public function makePrimary()
    {
        // Đặt tất cả ảnh khác của chi nhánh này thành không phải ảnh chính
        $this->branch->images()->update(['is_primary' => false]);
        
        // Đặt ảnh này làm ảnh chính
        $this->update(['is_primary' => true]);
    }
}