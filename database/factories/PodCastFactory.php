<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Podcast>
 */
class PodcastFactory extends Factory
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
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'image_url' => $this->faker->imageUrl(640, 480, 'podcast', true),
            'author_name' => $this->faker->name(),
            'category_id' => Category::factory(),
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
        ];
    }
    
    /**
     * Indicate that the podcast is featured.
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