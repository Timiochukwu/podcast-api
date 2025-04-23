<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create some tags
        $tags = Tag::factory(10)->create();
        
        // Create categories
        $categories = Category::factory(5)
            ->sequence(
                ['name' => 'News & Storytelling', 'slug' => 'news-storytelling'],
                ['name' => 'Entertainment & Lifestyle', 'slug' => 'entertainment-lifestyle'],
                ['name' => 'Tech, Sport & Business', 'slug' => 'tech-sport-business'],
                ['name' => 'Education & Learning', 'slug' => 'education-learning'],
                ['name' => 'Health & Wellness', 'slug' => 'health-wellness']
            )
            ->create();
        
        // Create featured categories
        Category::factory(2)
            ->featured()
            ->create();
        
        // For each category, create some podcasts
        $categories->each(function ($category) use ($tags) {
            // Create regular podcasts
            $podcasts = Podcast::factory(5)
                ->for($category)
                ->create();
            
            // Create featured podcasts
            $featuredPodcasts = Podcast::factory(2)
                ->for($category)
                ->featured()
                ->create();
            
            // Combine all podcasts
            $allPodcasts = $podcasts->merge($featuredPodcasts);
            
            // Attach random tags to each podcast
            $allPodcasts->each(function ($podcast) use ($tags) {
                $podcast->tags()->attach(
                    $tags->random(rand(1, 3))->pluck('id')->toArray()
                );
                
                // Create episodes for each podcast
                Episode::factory(rand(5, 10))
                    ->for($podcast)
                    ->create();
                
                // Create at least one featured episode per podcast
                Episode::factory()
                    ->for($podcast)
                    ->featured()
                    ->create();
            });
        });
    }
}