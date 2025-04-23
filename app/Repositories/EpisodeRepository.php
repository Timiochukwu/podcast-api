<?php

namespace App\Repositories;

use App\Models\Episode;
use App\Repositories\Interfaces\EpisodeRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class EpisodeRepository extends BaseRepository implements EpisodeRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Episode::class;
    }
    
    /**
     * Get featured episodes
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model
            ->where('is_featured', true)
            ->with('podcast.category')
            ->orderBy('published_at', 'desc')
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
            ->with(['podcast.category', 'podcast.tags'])
            ->firstOrFail();
    }
    
    /**
     * Get episodes with pagination and filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFiltered(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();
        
        // Always include these relations
        $query->with(['podcast.category']);
        
        // Filter by title
        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        
        // Filter by podcast
        if (isset($filters['podcast_id'])) {
            $query->where('podcast_id', $filters['podcast_id']);
        }
        
        // Filter by featured
        if (isset($filters['featured']) && $filters['featured']) {
            $query->where('is_featured', true);
        }
        
        // Filter by publication date
        if (isset($filters['from_date'])) {
            $query->where('published_at', '>=', $filters['from_date']);
        }
        
        if (isset($filters['to_date'])) {
            $query->where('published_at', '<=', $filters['to_date']);
        }
        
        // Sort by field
        $sortField = $filters['sort_by'] ?? 'published_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        
        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
    
    /**
     * Get episodes by podcast
     *
     * @param int $podcastId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByPodcast(int $podcastId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('podcast_id', $podcastId)
            ->with(['podcast.category'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }
    
    /**
     * Get recent episodes
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model
            ->with(['podcast.category'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }
}