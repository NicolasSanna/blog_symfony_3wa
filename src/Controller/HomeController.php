<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findBy([], ['createdAt' => 'DESC']);
        
        return $this->render('home/index.html.twig', [
            'articles' => $articles
        ]);
    }
    
    #[Route('/articles', name: 'app_article_public_index', methods: ['GET'])]
    public function public_index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/public.html.twig', [
            'articles' => $articleRepository->findAll()
        ]);
    }
}
