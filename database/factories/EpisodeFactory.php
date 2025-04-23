<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);
        
        return [
            'podcast_id' => Podcast::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'audio_url' => $this->faker->url() . '.mp3',
            'duration_in_seconds' => $this->faker->numberBetween(600, 3600), // 10 min to 1 hour
            'transcript' => $this->faker->paragraphs(5, true),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
    
    /**
     * Indicate that the episode is featured.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }
}