<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\PodcastRepositoryInterface;
use App\Repositories\Interfaces\EpisodeRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\PodcastRepository;
use App\Repositories\EpisodeRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );
        
        $this->app->bind(
            PodcastRepositoryInterface::class,
            PodcastRepository::class
        );
        
        $this->app->bind(
            EpisodeRepositoryInterface::class,
            EpisodeRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}