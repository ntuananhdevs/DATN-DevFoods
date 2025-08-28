<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class ProductImg extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'img',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    // Accessor để lấy URL của ảnh, từ S3
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->img) {
                    // Sử dụng trực tiếp đường dẫn từ DB vì nó đã bao gồm thư mục con
                    return Storage::disk('s3')->url($this->img);
                }
                return asset('images/default-product.png');
            },
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
