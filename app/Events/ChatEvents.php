<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChatEvents implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $company_id;
    public $user_id;
    public $catre_id;
    public $mesaj;
    public $status;
    public $eveniment;

   
    public function __construct(string $eveniment,string $company_id,string $user_id,string $catre_id=null,string $mesaj=null,string $status=null)
    {
        $this->company_id=$company_id;
        $this->user_id=$user_id;
        $this->catre_id=$catre_id;
        $this->mesaj=$mesaj;
        $this->status=$status;
        $this->eveniment=$eveniment;
        Log::info("CONSTRUCT CHAT EVENTS");
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        Log::info("BROADCASTON CHAT EVENTS");
         return new Channel('easyifn-dianasoft');
    }
    public function broadcastAs()
    {
            return 'ChatEvents';
    }
}
