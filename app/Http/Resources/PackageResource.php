<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'type' => $this->type,
            'package_scope' => $this->package_scope,
            'lecture_credits' => $this->lecture_credits,
            'education_stage_subject' => [
                'subject' => $this->educationStageSubject->subject->name ?? null,
                'education_stage' => $this->educationStageSubject->educationStage->name ?? null,
            ],

        ];
    }
}
