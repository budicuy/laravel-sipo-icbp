<?php

namespace App\Events;

use App\Models\RekamMedisEmergency;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RekamMedisEmergencyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rekamMedisEmergency;

    public $oldKeluhans;

    /**
     * Create a new event instance.
     */
    public function __construct(RekamMedisEmergency $rekamMedisEmergency, $oldKeluhans = [])
    {
        $this->rekamMedisEmergency = $rekamMedisEmergency;
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
