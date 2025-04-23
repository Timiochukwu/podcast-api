<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'audio_url' => $this->audio_url,
            'duration_in_seconds' => $this->duration_in_seconds,
            'formatted_duration' => $this->formatted_duration,
            'transcript' => $this->transcript,
            'is_featured' => $this->is_featured,
            'published_at' => $this->published_at,
            'podcast' => new PodcastResource($this->whenLoaded('podcast')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}