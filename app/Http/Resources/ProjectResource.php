<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $this->file_path,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'submitted_at' => $this->submitted_at,
            'created_at' => $this->created_at,
        ];
    }
}
