<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
            'user_code' => $this->user_code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'image' => asset("images/{$this->image}"),
            'email_verified_at' => $this->email_verified_at,
            'role' => $this->role,
            'iban' => $this->iban,
        ];
    }
}
