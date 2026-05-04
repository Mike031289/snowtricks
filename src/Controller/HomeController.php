<?php

// src/Controller/HomeController.php

/*
 * This file is part of SnowTricks.
 *
 * (c) Adjoukou AGBELOU <mike.agbelou@gmail.com> Dev-Application PHP Symfony
 */

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        TrickRepository $trickRepository,
        Request $request,
    ): Response {

        // Current page (default = 1, minimum = 1)
        $page = max(1, $request->query->getInt('page', 1));

        // Pagination limit
        $limit = 10;


        // Fetch paginated tricks
        $tricks = $trickRepository->findPaginated($page, $limit);

        // Total tricks count
        $totalTricks = $trickRepository->countAll();

        // Total pages calculation
        $totalPages = (int) ceil($totalTricks / $limit);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
