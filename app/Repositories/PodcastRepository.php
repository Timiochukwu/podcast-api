<?php

namespace App\Repositories;

use App\Models\Podcast;
use App\Repositories\Interfaces\PodcastRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class PodcastRepository extends BaseRepository implements PodcastRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Podcast::class;
    }
    
    /**
     * Get featured podcasts
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model
            ->where('is_featured', true)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Find by slug
     *
     * @param string $slug
     * @return Model|null
     */
    public function findBySlug(string $slug): ?Model
    {
        return $this->model
            ->where('slug', $slug)
            ->with(['category', 'tags'])
            ->firstOrFail();
    }
    
    /**
     * Get podcasts with pagination and filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFiltered(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        
        // Always include these relations
        $query->with(['category']);
        
        // Filter by title
        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        
        // Filter by author
        if (isset($filters['author'])) {
            $query->where('author_name', 'like', '%' . $filters['author'] . '%');
        }
        
        // Filter by featured
        if (isset($filters['featured']) && $filters['featured']) {
            $query->where('is_featured', true);
        }
        
        // Sort by field
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        
        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
    
    /**
     * Get podcasts by category
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    /**
     * Get podcasts by tag
     *
     * @param int $tagId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByTag(int $tagId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            })
            ->with(['category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}