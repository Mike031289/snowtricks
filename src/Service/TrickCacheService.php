<?php
// src/Service/TrickCacheService.php

/*
 * This file is part of SnowTricks.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 */

namespace App\Service;

use App\Repository\TrickRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Service responsible for caching Trick-related data.
 *
 * This improves performance by avoiding repeated database queries
 * for frequently accessed data such as homepage listings.
 *
 * Responsibilities:
 * - Cache paginated tricks for homepage
 * - Retrieve cached data when available
 * - Clear cache when tricks are updated
 */
class TrickCacheService
{
    public function __construct(
        private CacheInterface $cache,
        private TrickRepository $trickRepository
    ) {}
    

    /**
     * Get paginated tricks for homepage with caching.
     *
     * Each page is cached separately to support pagination.
     *
     * @param int $page Current page number
     * @param int $limit Number of items per page
     *
     * @return array Cached or fresh list of tricks
     */
    public function getHomepageTricks(int $page, int $limit): array
    {
        $cacheKey = 'homepage_tricks_page_' . $page;

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($page, $limit) {

            // Cache duration: 1 hour
            $item->expiresAfter(3600);

            return $this->trickRepository->findPaginated($page, $limit);
        });
    }

    /**
     * Clear all cached homepage tricks.
     *
     * This should be called whenever a Trick is created,
     * updated or deleted to avoid stale data.
     */
    public function clearHomepageCache(): void
    {
        // Assuming a reasonable max pagination depth
        $maxPages = 20;

        for ($page = 1; $page <= $maxPages; $page++) {
            $this->cache->delete('homepage_tricks_page_' . $page);
        }
    }
}