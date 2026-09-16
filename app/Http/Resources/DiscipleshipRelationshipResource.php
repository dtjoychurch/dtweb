<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscipleshipRelationshipResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mentor' => new UserResource($this->whenLoaded('mentor')),
            'disciple' => new UserResource($this->whenLoaded('disciple')),
            'started_at' => $this->started_at?->toDateString(),
            'ended_at' => $this->ended_at?->toDateString(),
            'status' => $this->status,
            'sessions_count' => $this->whenCounted('sessions'),
            'created_at' => $this->created_at,
        ];
    }
}
