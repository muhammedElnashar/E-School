<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SubmissionsResource extends JsonResource
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
            'assignment_id' => $this->assignment_id,
            'student_id' => $this->student_id,
            'text' => $this->text,
            'file_path' =>asset("files/".$this->file_path) ,
            'teacher_note'=> $this->teacher_note,
        ];
    }
}
