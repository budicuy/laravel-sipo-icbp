<?php

namespace App\Events;

use App\Models\RekamMedisEmergency;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RekamMedisEmergencyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rekamMedisEmergency;

    /**
     * Create a new event instance.
     */
    public function __construct(RekamMedisEmergency $rekamMedisEmergency)
    {
        $this->rekamMedisEmergency = $rekamMedisEmergency;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
