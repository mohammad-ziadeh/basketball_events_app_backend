<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class TeamAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $team;
    protected $category;

    public function __construct($team, $category)
    {
        $this->team = $team;
        $this->category = $category;
    }

    public function via($notifiable)
    {
        return ['database'];
    }


    public function toDatabase($notifiable)
    {
        return [
            'message' => "Your team '{$this->team->name}' has been approved in category '{$this->category->name}'.",
            'team_id' => $this->team->id,
            'category_id' => $this->category->id,
            'status' => 'approved',
        ];
    }
}
