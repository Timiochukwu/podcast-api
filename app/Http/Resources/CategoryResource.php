<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'podcasts_count' => $this->whenCounted('podcasts'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}