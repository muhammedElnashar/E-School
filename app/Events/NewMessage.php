<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat->load('sender');
    }

    public function broadcastOn()
    {
        // بث في قناة خاصة بالمحادثة
        return new PrivateChannel('conversation.' . $this->chat->conversation_id);
    }

    public function broadcastAs()
    {
        return 'new.message';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->chat->id,
            'message' => $this->chat->message,
            'sender_id' => $this->chat->sender->id,
            'sender_name' => $this->chat->sender->name,
            'conversation_id' => $this->chat->conversation_id,
            'created_at' => $this->chat->created_at->toDateTimeString(),

        ];
    }

}
