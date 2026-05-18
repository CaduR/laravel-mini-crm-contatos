<?php

namespace App\Events;

use App\Models\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactScoreProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Esta variável precisa ser PUBLIC para o Laravel enviar no WebSocket
    public Contact $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        //canal público chamado contacts
        return [
            new Channel('contacts'),
        ];
    }
}
