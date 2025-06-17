<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherTransactionsResource extends JsonResource
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
            'teacher_name' => $this->teacher->name,
            'admin_name'=> $this->admin->name,
            'amount' => $this->amount,
            'paid_at' => $this->paid_at->format('Y-m-d H:i'),
        ];
    }
}
