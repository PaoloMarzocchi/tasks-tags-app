<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'assignee' => $this->assignee,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(fn($t) => [
                    'slug' => $t->slug,
                    'name' => $t->name,
                ])->values();
            }),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
