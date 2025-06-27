<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $table = 'general_setting';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Get setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     * 
     * @param string $key
     * @param mixed $value
     * @param string|null $description
     * @return bool
     */
    public static function set($key, $value, $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description
            ]
        );

        // Clear cache
        Cache::forget("setting_{$key}");

        return $setting ? true : false;
    }

    /**
     * Get tax rate as percentage
     * 
     * @return float
     */
    public static function getTaxRate()
    {
        return (float) self::get('tax_rate', 10);
    }

    /**
     * Get free shipping threshold
     * 
     * @return int
     */
    public static function getFreeShippingThreshold()
    {
        return (int) self::get('free_shipping_threshold', 200000);
    }

    /**
     * Get shipping fee
     * 
     * @return int
     */
    public static function getShippingFee()
    {
        return (int) self::get('shipping_fee', 30000);
    }

    /**
     * Get currency
     * 
     * @return string
     */
    public static function getCurrency()
    {
        return self::get('currency', 'VND');
    }

    /**
     * Get order auto cancel time in minutes
     * 
     * @return int
     */
    public static function getOrderAutoCancelTime()
    {
        return (int) self::get('order_auto_cancel_time', 30);
    }

    /**
     * Calculate tax amount
     * 
     * @param float $amount
     * @return float
     */
    public static function calculateTax($amount)
    {
        $taxRate = self::getTaxRate();
        return round($amount * ($taxRate / 100), 0);
    }

    /**
     * Check if order qualifies for free shipping
     * 
     * @param float $amount
     * @return bool
     */
    public static function qualifiesForFreeShipping($amount)
    {
        return $amount >= self::getFreeShippingThreshold();
    }

    /**
     * Calculate shipping fee based on order amount
     * 
     * @param float $amount
     * @return int
     */
    public static function calculateShippingFee($amount)
    {
        return self::qualifiesForFreeShipping($amount) ? 0 : self::getShippingFee();
    }
}