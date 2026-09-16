<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscipleshipGoalResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'relationship_id' => $this->relationship_id,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'started_at' => $this->started_at?->toDateString(),
            'completed_at' => $this->completed_at?->toDateString(),
            'visibility' => $this->visibility,
            'created_at' => $this->created_at,
        ];
    }
}
