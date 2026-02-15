<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BatuNotification extends Notification
{
    use Queueable;

    private $data;


    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // هنخزنها في الداتا بيز
    }

    public function toArray($notifiable)
    {
        return $this->data; // حفظ البيانات زي ما هي
    }
}
