<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetsResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'price' => $this->price,
            'education_stage_subject' => [
                'subject' => [
                    'id' => $this->educationStageSubject->subject->id,
                    'name' => $this->educationStageSubject->subject->name,
                ],
                'education_stage' => [
                    'id' => $this->educationStageSubject->educationStage->id,
                    'name' => $this->educationStageSubject->educationStage->name,
                ],

            ],
        ];
    }
}
