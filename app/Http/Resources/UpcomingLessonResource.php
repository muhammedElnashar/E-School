<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UpcomingLessonResource extends JsonResource
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
            'lesson_id' => $this->lessonOccurrence->lesson->id,
            'lesson_type' => $this->lessonOccurrence->lesson->lesson_type,
            'occurrence_date' => $this->lessonOccurrence->occurrence_date->format('Y-m-d'),
            'start_time' => \Carbon\Carbon::parse($this->lessonOccurrence->lesson->start_datetime)->format('H:i'),
            'end_time' => \Carbon\Carbon::parse($this->lessonOccurrence->lesson->end_datetime)->format('H:i'),
            'subject_id' => $this->lessonOccurrence->lesson->subject_id,
            'education_stage_id' => $this->lessonOccurrence->lesson->education_stage_id,
            'agora_channel' => $this->lessonOccurrence->lesson->agora_channel_name,
        ];
    }
}
