<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonOccurrenceResource extends JsonResource
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
            'occurrence_date' => $this->occurrence_date,
            'lesson_id' => $this->lesson_id,
            'lesson' => new LessonResource($this->whenLoaded('lesson')),
        ];
    }
}
