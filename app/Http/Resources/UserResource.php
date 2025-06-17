<?php

namespace App\Http\Resources;

use App\Enums\RoleEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'user_code' => $this->user_code,
            'image' => $this->image ? asset('images/' . $this->image) :    null,
            'role' => $this->role,
            'iban' =>  RoleEnum::Teacher ? $this->iban:"",
            "phone" => $this->phone,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toDateTimeString() : null,
        ];
    }
}
