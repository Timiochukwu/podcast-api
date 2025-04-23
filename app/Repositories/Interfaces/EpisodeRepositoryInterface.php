<?php

namespace App\Repositories\Interfaces;

interface EpisodeRepositoryInterface extends RepositoryInterface
{
    /**
     * Get featured episodes
     *
     * @param int $limit
     * @return mixed
     */
    public function getFeatured(int $limit = 5);
    
    /**
     * Find by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug);
    
    /**
     * Get episodes with pagination and filters
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getFiltered(array $filters, int $perPage = 15);
    
    /**
     * Get episodes by podcast
     *
     * @param int $podcastId
     * @param int $perPage
     * @return mixed
     */
    public function getByPodcast(int $podcastId, int $perPage = 15);
    
    /**
     * Get recent episodes
     *
     * @param int $limit
     * @return mixed
     */
    public function getRecent(int $limit = 10);
}