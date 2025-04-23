<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_categories()
    {
        // Create some categories
        Category::factory()->count(5)->create();

        // Make the request
        $response = $this->getJson('/api/v1/categories');

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

    public function test_can_get_featured_categories()
    {
        // Create regular categories
        Category::factory()->count(3)->create();
        
        // Create featured categories
        Category::factory()->count(2)->featured()->create();

        // Make the request
        $response = $this->getJson('/api/v1/categories/featured');

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

        // Assert all returned categories are featured
        $categories = $response->json('data');
        $this->assertCount(2, $categories);
        foreach ($categories as $category) {
            $this->assertTrue($category['is_featured']);
        }
    }

    public function test_can_get_category_by_slug()
    {
        // Create a category
        $category = Category::factory()->create();

        // Make the request
        $response = $this->getJson("/api/v1/categories/{$category->slug}");

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
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_category()
    {
        // Make the request with a non-existent slug
        $response = $this->getJson('/api/v1/categories/non-existent-category');

        // Assert response status and structure
        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Category not found',
                'data' => null,
            ]);
    }

    public function test_authenticated_user_can_create_category()
    {
        // Create a user
        $user = User::factory()->create();

        // Category data
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category',
            'image_url' => 'https://example.com/image.jpg',
            'is_featured' => true,
            'sort_order' => 1,
        ];

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->postJson('/api/v1/categories', $categoryData);

        // Assert response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $categoryData,
            ]);

        // Assert the category was created in the database
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_category()
    {
        // Category data
        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'This is a test category',
            'image_url' => 'https://example.com/image.jpg',
            'is_featured' => true,
            'sort_order' => 1,
        ];

        // Make the request without authentication
        $response = $this->postJson('/api/v1/categories', $categoryData);

        // Assert response status
        $response->assertStatus(401);

        // Assert the category was not created in the database
        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_authenticated_user_can_update_category()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a category
        $category = Category::factory()->create();

        // Update data
        $updateData = [
            'name' => 'Updated Category',
            'slug' => 'updated-category',
            'description' => 'This is an updated category',
            'image_url' => 'https://example.com/updated-image.jpg',
            'is_featured' => true,
            'sort_order' => 2,
        ];

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->putJson("/api/v1/categories/{$category->id}", $updateData);

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $updateData,
            ]);

        // Assert the category was updated in the database
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'slug' => 'updated-category',
        ]);
    }

    public function test_authenticated_user_can_delete_category()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a category
        $category = Category::factory()->create();

        // Make the request as an authenticated user
        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/categories/{$category->id}");

        // Assert response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Category deleted successfully',
                'data' => null,
            ]);

        // Assert the category was deleted from the database
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}