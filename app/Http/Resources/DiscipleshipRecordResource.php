<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscipleshipRecordResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'relationship_id' => $this->relationship_id,
            'session_id' => $this->session_id,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'type_id' => $this->type_id,
            'type' => $this->whenLoaded('type', fn () => [
                'id' => $this->type->id,
                'slug' => $this->type->slug,
                'name' => $this->type->name,
            ]),
            'title' => $this->title,
            'content' => $this->content,
            'visibility' => $this->visibility,
            'occurred_at' => $this->occurred_at?->toDateString(),
            'created_at' => $this->created_at,
        ];
    }
}
