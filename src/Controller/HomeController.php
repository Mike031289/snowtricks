<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        // Get the current page number from the query parameters, defaulting to 1 if not provided
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 3;
        
        $tricks = $trickRepository->findPaginated($page, $limit);
        $totalTricks = $trickRepository->countAll();
        $totalPages = ceil($totalTricks / $limit);
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}