<?php

namespace App\Notifications;

use App\Models\AdminNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AdminRealtimeNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public AdminNotification $adminNotification;

    /**
     * Create a new notification instance.
     */
    public function __construct(AdminNotification $adminNotification)
    {
        $this->adminNotification = $adminNotification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->adminNotification->id,
            'type' => $this->adminNotification->type,
            'title' => $this->adminNotification->title,
            'message' => $this->adminNotification->message,
            'link' => $this->adminNotification->link,
            'created_at' => $this->adminNotification->created_at->diffForHumans(),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->adminNotification->id,
            'message' => $this->adminNotification->message,
        ];
    }
}
