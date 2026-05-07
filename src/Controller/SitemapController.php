<?php

// src/Controller/SitemapController

/*
 * This file is part of SnowTricks. His charged to generate sitemap for SEO and engin sercher.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 *
*/

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', methods: ['GET'])]
    public function sitemap(TrickRepository $trickRepository, Request $request): Response
    {
        $tricks = $trickRepository->findAll();

        $baseUrl = $request->getSchemeAndHttpHost();

        $urls = [];

        // HOME
        $urls[] = [
            'loc' => $baseUrl.'/',
            'priority' => '1.0',
        ];

        // TRICKS
        foreach ($tricks as $trick) {
            $urls[] = [
                'loc' => $baseUrl.'/'.$trick->getId().'/'.$trick->getSlug(),
                'priority' => '0.8',
                'lastmod' => $trick->getUpdatedAt()?->format('Y-m-d'),
            ];
        }

        return new Response(
            $this->renderView('seo/sitemap.xml.twig', [
                'urls' => $urls,
            ]),
            200,
            ['Content-Type' => 'application/xml']
        );
    }
}
