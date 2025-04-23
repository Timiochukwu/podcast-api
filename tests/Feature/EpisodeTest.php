<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Episode;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_episodes()
    {
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();
        
        // Create episodes
        Episode::factory()->count(5)->for($podcast)->create();

        // Make the request
        $response = $this->getJson('/api/v1/episodes');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data',
                    'links',
                    'meta',
                ]
            ])
            ->assertJson([
                'success' => true,
            ]);

        // Assert data count
        $this->assertCount(5, $response->json('data.data'));
    }

    public function test_can_get_featured_episodes()
    {
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();
        
        // Create regular episodes
        Episode::factory()->count(3)->for($podcast)->create();
        
        // Create featured episodes
        Episode::factory()->count(2)->for($podcast)->featured()->create();

        // Make the request
        $response = $this->getJson('/api/v1/episodes/featured');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
            ]);

        // Assert all returned episodes are featured
        $episodes = $response->json('data');
        $this->assertCount(2, $episodes);
        foreach ($episodes as $episode) {
            $this->assertTrue($episode['is_featured']);
        }
    }

    public function test_can_get_recent_episodes()
    {
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();
        
        // Create episodes with different published dates
        for ($i = 0; $i < 5; $i++) {
            Episode::factory()->for($podcast)->create([
                'published_at' => now()->subDays($i),
            ]);
        }

        // Make the request
        $response = $this->getJson('/api/v1/episodes/recent?limit=3');

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
            ]);

        // Assert the correct number of episodes are returned
        $episodes = $response->json('data');
        $this->assertCount(3, $episodes);
    }

    public function test_can_get_episode_by_slug()
    {
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();
        
        // Create an episode
        $episode = Episode::factory()->for($podcast)->create();

        // Make the request
        $response = $this->getJson("/api/v1/episodes/{$episode->slug}");

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $episode->id,
                    'title' => $episode->title,
                    'slug' => $episode->slug,
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_episode()
    {
        // Make the request with a non-existent slug
        $response = $this->getJson('/api/v1/episodes/non-existent-episode');

        // Assert response status and structure
        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Episode not found',
                'data' => null,
            ]);
    }

    public function test_authenticated_user_can_create_episode()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();

        // Episode data
        $episodeData = [
            'podcast_id' => $podcast->id,
            'title' => 'Test Episode',
            'slug' => 'test-episode',
            'description' => 'This is a test episode',
            'audio_url' => 'https://example.com/audio.mp3',
            'duration_in_seconds' => 1800,
            'transcript' => 'This is the transcript of the episode',
            'is_featured' => true,
            'published_at' => now()->toDateTimeString(),
        ];

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->postJson('/api/v1/episodes', $episodeData);

        // Assert response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Episode created successfully',
            ]);

        // Assert the episode was created in the database
        $this->assertDatabaseHas('episodes', [
            'title' => 'Test Episode',
            'slug' => 'test-episode',
            'podcast_id' => $podcast->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_episode()
    {
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();

        // Episode data
        $episodeData = [
            'podcast_id' => $podcast->id,
            'title' => 'Test Episode',
            'slug' => 'test-episode',
            'description' => 'This is a test episode',
            'audio_url' => 'https://example.com/audio.mp3',
            'duration_in_seconds' => 1800,
            'transcript' => 'This is the transcript of the episode',
            'is_featured' => true,
            'published_at' => now()->toDateTimeString(),
        ];

        // Make the request without authentication
        $response = $this->postJson('/api/v1/episodes', $episodeData);

        // Assert response status
        $response->assertStatus(401);

        // Assert the episode was not created in the database
        $this->assertDatabaseMissing('episodes', [
            'title' => 'Test Episode',
            'slug' => 'test-episode',
        ]);
    }

    public function test_authenticated_user_can_update_episode()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category and podcasts
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();
        $newPodcast = Podcast::factory()->for($category)->create();
        
        // Create an episode
        $episode = Episode::factory()->for($podcast)->create();

        // Update data
        $updateData = [
            'podcast_id' => $newPodcast->id,
            'title' => 'Updated Episode',
            'slug' => 'updated-episode',
            'description' => 'This is an updated episode',
            'audio_url' => 'https://example.com/updated-audio.mp3',
            'duration_in_seconds' => 2400,
            'transcript' => 'This is the updated transcript',
            'is_featured' => false,
            'published_at' => now()->addDay()->toDateTimeString(),
        ];

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->putJson("/api/v1/episodes/{$episode->id}", $updateData);

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Episode updated successfully',
            ]);

        // Assert the episode was updated in the database
        $this->assertDatabaseHas('episodes', [
            'id' => $episode->id,
            'title' => 'Updated Episode',
            'slug' => 'updated-episode',
            'podcast_id' => $newPodcast->id,
        ]);
    }

    public function test_authenticated_user_can_delete_episode()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category and podcast
        $category = Category::factory()->create();
        $podcast = Podcast::factory()->for($category)->create();
        
        // Create an episode
        $episode = Episode::factory()->for($podcast)->create();

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/episodes/{$episode->id}");

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Episode deleted successfully',
                'data' => null,
            ]);

        // Assert the episode was deleted from the database
        $this->assertDatabaseMissing('episodes', [
            'id' => $episode->id,
        ]);
    }
}