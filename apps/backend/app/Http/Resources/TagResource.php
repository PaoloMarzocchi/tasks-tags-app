<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color,
            'parent' => $this->parent ? [
                'slug' => $this->parent->slug,
                'name' => $this->parent->name,
            ] : null,
            'parent_slug' => $this->parent?->slug,
        ];
    }
}
