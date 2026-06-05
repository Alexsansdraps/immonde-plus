<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Film;
use App\Entity\FilmSeries;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\FilmRepository;
use App\Repository\FilmSeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    private const ITEMS_PER_PAGE = 6;

    #[Route('/', name: 'blog_index')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $latestArticles = $articleRepository->findLatestPublished(3);

        return $this->render('blog/index.html.twig', [
            'latestArticles' => $latestArticles
        ]);
    }

    #[Route('/jeux', name: 'blog_games')]
    public function games(): Response
    {
        return $this->render('blog/games.html.twig');
    }

    #[Route('/articles', name: 'blog_articles')]
    public function articles(ArticleRepository $articleRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('q');
        if ($page < 1) $page = 1;

        $totalItems = $articleRepository->countPublished($search);
        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);

        $articles = $articleRepository->findPublishedPaginated($page, self::ITEMS_PER_PAGE, $search);

        return $this->render('blog/articles.html.twig', [
            'articles' => $articles,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'searchTerm' => $search
        ]);
    }

    #[Route('/films', name: 'blog_films')]
    public function films(FilmRepository $filmRepository, FilmSeriesRepository $seriesRepository, Request $request): Response
    {
        $search = $request->query->get('q');

        $series = $seriesRepository->findAllWithPublishedFilms($search);
        $standaloneFilms = $filmRepository->findStandalonePublishedFilms($search);

        return $this->render('blog/films.html.twig', [
            'series' => $series,
            'standaloneFilms' => $standaloneFilms,
            'searchTerm' => $search
        ]);
    }

    #[Route('/jeux/le-snake-immonde', name: 'blog_game_snake')]
    public function gameSnake(): Response
    {
        return $this->render('blog/snake.html.twig');
    }

    #[Route('/jeux/mickael-world', name: 'blog_game_mickael_world')]
    public function mickaelWorld(): Response
    {
        return $this->render('blog/mickael_world.html.twig');
    }

    #[Route('/jeux/labyrinthe-des-doubles', name: 'blog_game_paires')]
    public function gamePaires(): Response
    {
        return $this->render('blog/paires.html.twig');
    }

    #[Route('/jeux/ecrase-le-mickael', name: 'blog_game_ecrase')]
    public function gameEcrase(): Response
    {
        return $this->render('blog/ecrase.html.twig');
    }

    #[Route('/jeux/justice-shooter', name: 'blog_game_shooter')]
    public function gameShooter(): Response
    {
        return $this->render('blog/bubble_shooter.html.twig');
    }

    #[Route('/films/serie/{slug}', name: 'blog_series_show')]
    public function seriesShow(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        FilmSeries $series
    ): Response
    {
        return $this->render('blog/series_show.html.twig', [
            'series' => $series,
        ]);
    }

    #[Route('/film/{slug}', name: 'film_show')]
    public function filmShow(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Film $film
    ): Response
    {
        if (!$film->isPublished()) {
            throw $this->createNotFoundException('Le film demandé n\'est pas disponible.');
        }

        return $this->render('blog/film_show.html.twig', [
            'film' => $film,
        ]);
    }

    #[Route('/article/{slug}', name: 'blog_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$article->isPublished()) {
            throw $this->createNotFoundException('L\'article demandé n\'est pas disponible.');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setIsApproved(false);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été envoyé et est en attente de modération.');

            return $this->redirect($this->generateUrl('blog_show', ['slug' => $article->getSlug()]) . '#comments');
        }

        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView(),
        ]);
    }
}
