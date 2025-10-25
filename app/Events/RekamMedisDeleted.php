<?php

namespace App\Events;

use App\Models\RekamMedis;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RekamMedisDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rekamMedis;

    /**
     * Create a new event instance.
     */
    public function __construct(RekamMedis $rekamMedis)
    {
        $this->rekamMedis = $rekamMedis;
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
