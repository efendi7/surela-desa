<?php

namespace App\Notifications;

use App\Models\Berita;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BeritaBaruNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $berita;

    /**
     * Create a new notification instance.
     */
    public function __construct(Berita $berita)
    {
        $this->berita = $berita;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // We will store it in the DB
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'berita_id' => $this->berita->id,
            'title' => 'Berita Baru Telah Terbit!',
            'message' => "Cek berita terbaru: '{$this->berita->title}'",
            'url' => route('public.berita.show', $this->berita->slug),
        ];
    }
}