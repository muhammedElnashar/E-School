<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $levels = $this->stages->pluck('name');

        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'image'  => asset("images/" . $this->image),
            'levels' => $levels->isEmpty() ? "" : $levels,

        ];
    }
}
