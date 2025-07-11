<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\Driver;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverAssigned
{
    use Dispatchable, SerializesModels;

    public $order;
    public $driver;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, Driver $driver)
    {
        $this->order = $order;
        $this->driver = $driver;
    }
} 