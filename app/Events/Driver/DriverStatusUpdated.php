<?php

namespace App\Events\Driver;

use App\Models\Driver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DriverStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Driver $driver;
    public string $oldStatus;
    public string $newStatus;
    public bool $isAvailable;

    /**
     * Create a new event instance.
     */
    public function __construct(Driver $driver, string $oldStatus = null)
    {
        $this->driver = $driver;
        $this->oldStatus = $oldStatus ?? 'unknown';
        $this->newStatus = $driver->driver_status;
        $this->isAvailable = $driver->is_available;

        Log::info('DriverStatusUpdated event constructed', [
            'driver_id' => $driver->id,
            'driver_name' => $driver->full_name,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'is_available' => $this->isAvailable
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            // Channel for specific driver
            new PrivateChannel('driver.' . $this->driver->id),
            // Admin channel for real-time updates
            new Channel('drivers'),
        ];
        
        Log::info('DriverStatusUpdated broadcasting on channels', [
            'channels' => array_map(function($channel) {
                return $channel->name;
            }, $channels),
            'driver_id' => $this->driver->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ]);
        
        return $channels;
    }

    /**
     * The name of the event to broadcast.
     */
    public function broadcastAs(): string
    {
        return 'driver-status-updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // We send only the necessary data to the frontend.
        return [
            'driver' => [
                'id' => $this->driver->id,
                'name' => $this->driver->full_name,
                'phone' => $this->driver->phone_number,
                'status' => $this->driver->driver_status,
                'is_available' => $this->driver->is_available,
            ],
            'driver_id' => $this->driver->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'is_available' => $this->isAvailable,
            'updated_at' => now()->format('H:i - d/m/Y'),
        ];
    }
}