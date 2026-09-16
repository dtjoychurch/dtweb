<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscipleshipSessionResource extends JsonResource
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
            'session_date' => $this->session_date?->toDateString(),
            'title' => $this->title,
            'content' => $this->content,
            'ordinal' => $this->when(isset($this->ordinal_number), fn () => $this->ordinal_number),
            'comments' => DiscipleshipCommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at,
        ];
    }
}
