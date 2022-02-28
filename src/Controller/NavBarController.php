<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavBarController extends AbstractController
{
    #[Route('/nav/bar', name: 'nav_bar')]
    public function navBarCats(CategoryRepository $catRepository): Response
    {
        $categories = $catRepository->findAll();
        return $this->render('partial/_navBarCats.html.twig', [
            'categories' => $categories,
        ]);
    }
}
