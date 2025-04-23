<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Category::class;
    }
    
    /**
     * Get featured categories
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model
            ->where('is_featured', true)
            ->orderBy('sort_order')
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
            ->firstOrFail();
    }
    
    /**
     * Get categories with pagination and sorting
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFiltered(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        
        // Filter by name
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        
        // Filter by featured
        if (isset($filters['featured']) && $filters['featured']) {
            $query->where('is_featured', true);
        }
        
        // Sort by field
        $sortField = $filters['sort_by'] ?? 'sort_order';
        $sortDirection = $filters['sort_direction'] ?? 'asc';
        
        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
}