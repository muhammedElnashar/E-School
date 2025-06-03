<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentsResource extends JsonResource
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
            'teacher_id' => $this->teacher->name ?? "" ,
            'lesson_occurrence_id' => $this->lesson_occurrence_id,
            'text' => $this->text,
            'file_path' => asset('files/' . $this->file_path) ,

        ];
    }
}
