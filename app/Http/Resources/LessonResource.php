<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            'teacher' => $this->teacher->name,
            'subject' => $this->subject->name,
            'education_stage' => $this->educationStage->name?? null,
            'start_time' => $this->start_datetime->format('H:i'),
            'end_time' => $this->end_datetime->format('H:i'),
            'lesson_type' => $this->lesson_type,
        ];
    }
}
