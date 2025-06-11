<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'message' => $this->message,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'user_code' => $this->sender->user_code,
            ],
        ];
    }
}
