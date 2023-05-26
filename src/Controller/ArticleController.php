<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Services\RegisterImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \DateTimeImmutable;
use Symfony\Component\Filesystem\Filesystem;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $articles = $articleRepository->findByAuthorOrderByTitle($this->getUser());

        $pagination = $paginatorInterface->paginate($articles, $request->query->get('page', 1), 5);
        return $this->render('article/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/admin/new', name: 'app_article_new', methods: ['POST', 'GET'])]
    public function new(Request $request, ArticleRepository $articleRepository, RegisterImage $registerImage): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setAuthor($this->getUser());

            if ($form->get('image')->getData() != null)
            {   
                $registerImage->setForm($form);
                $fileName = $registerImage->saveImage();
    
                $article->setImage($fileName);
            }
         
            $article->setCreatedAt(new DateTimeImmutable());
            
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET', 'POST'])]
    public function show(Article $article, Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setCreatedAt(new DateTimeImmutable());
            $comment->setAuthor($this->getUser());

            $commentRepository->save($comment, true);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository, RegisterImage $registerImage, Filesystem $filesystem): Response
    {
        
        $checkArticle = $articleRepository->findByAuthor($this->getUser(), $article);
        
        if (!$checkArticle)
        {
            $this->addFlash('error', 'Vous ne pouvez pas modifier l\'article d\'un autre utilisateur');
            return $this->redirectToRoute('app_article_index');
        }

        $image = $checkArticle->getImage();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setAuthor($this->getUser());

            if ($form->get('image')->getData() != null) {

                $registerImage->setForm($form);
                $fileName = $registerImage->saveImage();

                if($filesystem->exists('image_directory' . '/' . $image))
                {
                    $filesystem->remove('image_directory' . '/' . $image);
                }
                
                $article->setImage($fileName);
            }
            else
            {
                if($image)
                {
                    $article->setImage($image);
                }
            }
           
            $articleRepository->save($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository, Filesystem $filesystem): Response|JsonResponse
    {
        $checkArticle = $articleRepository->findByAuthor($this->getUser(), $article);
        
        if (!$checkArticle)
        {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer l\'article d\'un autre utilisateur');
            return $this->redirectToRoute('app_article_index');
        }

        $articleId = $checkArticle->getId();

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {

            if($filesystem->exists('image_directory' . '/' . $article->getImage()))
            {
                $filesystem->remove('image_directory' . '/' . $article->getImage());
            }
            $articleRepository->remove($article, true);
        }

        if($request->isXmlHttpRequest())
        {
            return $this->json($articleId);
        }
        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }

   
}