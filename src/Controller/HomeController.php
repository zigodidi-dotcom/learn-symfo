<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CategoryRepository $categories, TermRepository $terms): Response
    {
        return $this->render('home/index.html.twig', [
            'categories' => $categories->findAllWithFeatures(),
            'terms'      => $terms->findAllWithFeatures(),
        ]);
    }
}
