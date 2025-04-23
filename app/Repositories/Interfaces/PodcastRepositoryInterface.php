<?php

namespace App\Repositories\Interfaces;

interface PodcastRepositoryInterface extends RepositoryInterface
{
    /**
     * Get featured podcasts
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
     * Get podcasts with pagination and filters
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getFiltered(array $filters, int $perPage = 15);
    
    /**
     * Get podcasts by category
     *
     * @param int $categoryId
     * @param int $perPage
     * @return mixed
     */
    public function getByCategory(int $categoryId, int $perPage = 15);
    
    /**
     * Get podcasts by tag
     *
     * @param int $tagId
     * @param int $perPage
     * @return mixed
     */
    public function getByTag(int $tagId, int $perPage = 15);
}