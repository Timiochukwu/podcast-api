<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    /**
     * Get featured categories
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
     * Get categories with pagination and sorting
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getFiltered(array $filters, int $perPage = 15);
}