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

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findByAuthorOrderByTitle($this->getUser()),
        ]);
    }

    #[Route('/admin/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, RegisterImage $registerImage): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setAuthor($this->getUser());

            $registerImage->setForm($form);
            $fileName = $registerImage->saveImage();

            $article->setImage($fileName);
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
        $checkArticle = $articleRepository->findOneBy(['author' => $this->getUser()]);
        
        if (!$checkArticle)
        {
            return $this->redirectToRoute('app_article_index');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setAuthor($this->getUser());
            $registerImage->setForm($form);
            $fileName = $registerImage->saveImage();

            if(!empty($fileName))
            {
                if($filesystem->exists('image_directory' . '/' . $article->getImage()))
                {
                    $filesystem->remove('image_directory' . '/' . $article->getImage());
                }
                $article->setImage($fileName);
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
        $checkArticle = $articleRepository->findOneBy(['author' => $this->getUser()]);
        
        if (!$checkArticle)
        {
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
