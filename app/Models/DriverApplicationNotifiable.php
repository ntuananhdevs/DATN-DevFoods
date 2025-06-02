<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

class DriverApplicationNotifiable
{
    use Notifiable;

    protected $email;
    protected $name;

    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    /**
     * Get the notification routing information for the given driver.
     */
    public function getEmailForNotifications()
    {
        return $this->email;
    }

    /**
     * Get the name of the notifiable entity.
     */
    public function getName()
    {
        return $this->name;
    }
} 