<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Podcast;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PodcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_podcasts()
    {
        // Create a category
        $category = Category::factory()->create();
        
        // Create podcasts
        Podcast::factory()->count(5)->for($category)->create();

        // Make the request
        $response = $this->getJson('/api/v1/podcasts');

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

    public function test_can_get_featured_podcasts()
    {
        // Create a category
        $category = Category::factory()->create();
        
        // Create regular podcasts
        Podcast::factory()->count(3)->for($category)->create();
        
        // Create featured podcasts
        Podcast::factory()->count(2)->for($category)->featured()->create();

        // Make the request
        $response = $this->getJson('/api/v1/podcasts/featured');

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

        // Assert all returned podcasts are featured
        $podcasts = $response->json('data');
        $this->assertCount(2, $podcasts);
        foreach ($podcasts as $podcast) {
            $this->assertTrue($podcast['is_featured']);
        }
    }

    public function test_can_get_podcast_by_slug()
    {
        // Create a category
        $category = Category::factory()->create();
        
        // Create a podcast
        $podcast = Podcast::factory()->for($category)->create();

        // Make the request
        $response = $this->getJson("/api/v1/podcasts/{$podcast->slug}");

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
                    'id' => $podcast->id,
                    'title' => $podcast->title,
                    'slug' => $podcast->slug,
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_podcast()
    {
        // Make the request with a non-existent slug
        $response = $this->getJson('/api/v1/podcasts/non-existent-podcast');

        // Assert response status and structure
        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Podcast not found',
                'data' => null,
            ]);
    }

    public function test_authenticated_user_can_create_podcast()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();

        // Podcast data
        $podcastData = [
            'title' => 'Test Podcast',
            'slug' => 'test-podcast',
            'description' => 'This is a test podcast',
            'image_url' => 'https://example.com/image.jpg',
            'author_name' => 'John Doe',
            'category_id' => $category->id,
            'is_featured' => true,
        ];

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->postJson('/api/v1/podcasts', $podcastData);

        // Assert response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Podcast created successfully',
                'data' => $podcastData,
            ]);

        // Assert the podcast was created in the database
        $this->assertDatabaseHas('podcasts', [
            'title' => 'Test Podcast',
            'slug' => 'test-podcast',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_podcast()
    {
        // Create a category
        $category = Category::factory()->create();

        // Podcast data
        $podcastData = [
            'title' => 'Test Podcast',
            'slug' => 'test-podcast',
            'description' => 'This is a test podcast',
            'image_url' => 'https://example.com/image.jpg',
            'author_name' => 'John Doe',
            'category_id' => $category->id,
            'is_featured' => true,
        ];

        // Make the request without authentication
        $response = $this->postJson('/api/v1/podcasts', $podcastData);

        // Assert response status
        $response->assertStatus(401);

        // Assert the podcast was not created in the database
        $this->assertDatabaseMissing('podcasts', [
            'title' => 'Test Podcast',
            'slug' => 'test-podcast',
        ]);
    }

    public function test_authenticated_user_can_update_podcast()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create();
        
        // Create a podcast
        $podcast = Podcast::factory()->for($category)->create();

        // Update data
        $updateData = [
            'title' => 'Updated Podcast',
            'slug' => 'updated-podcast',
            'description' => 'This is an updated podcast',
            'image_url' => 'https://example.com/updated-image.jpg',
            'author_name' => 'Jane Smith',
            'category_id' => $newCategory->id,
            'is_featured' => true,
        ];

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->putJson("/api/v1/podcasts/{$podcast->id}", $updateData);

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Podcast updated successfully',
                'data' => $updateData,
            ]);

        // Assert the podcast was updated in the database
        $this->assertDatabaseHas('podcasts', [
            'id' => $podcast->id,
            'title' => 'Updated Podcast',
            'slug' => 'updated-podcast',
            'category_id' => $newCategory->id,
        ]);
    }

    public function test_authenticated_user_can_delete_podcast()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category
        $category = Category::factory()->create();
        
        // Create a podcast
        $podcast = Podcast::factory()->for($category)->create();

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/podcasts/{$podcast->id}");

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Podcast deleted successfully',
                'data' => null,
            ]);

        // Assert the podcast was deleted from the database
        $this->assertDatabaseMissing('podcasts', [
            'id' => $podcast->id,
        ]);
    }
}