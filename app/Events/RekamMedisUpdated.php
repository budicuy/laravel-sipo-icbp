<?php

namespace App\Events;

use App\Models\RekamMedis;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RekamMedisUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rekamMedis;

    public $oldKeluhans;

    /**
     * Create a new event instance.
     */
    public function __construct(RekamMedis $rekamMedis, $oldKeluhans = [])
    {
        $this->rekamMedis = $rekamMedis;
        $this->oldKeluhans = $oldKeluhans;
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
