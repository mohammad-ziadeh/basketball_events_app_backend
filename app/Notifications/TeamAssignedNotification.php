<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $coach;

    public function __construct($event, $coach)
    {
        $this->event = $event;
        $this->coach = $coach;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "You have been assigned to the event: {$this->event->title}",
            'event_id' => $this->event ? $this->event->id : null,
            'event_title' => $this->event ? $this->event->title : '',
            'assigned_by' => $this->coach ? $this->coach->name : '',
            'coach_name' => $this->coach ? $this->coach->name : '',

        ];
    }
}
