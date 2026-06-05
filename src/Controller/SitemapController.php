<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function index(ArticleRepository $articleRepository, FilmRepository $filmRepository): Response
    {
        $urls = [];

        // Accueil
        $urls[] = ['loc' => $this->generateUrl('blog_index', [], 0), 'changefreq' => 'daily', 'priority' => '1.0'];

        // Articles
        $articles = $articleRepository->findBy(['isPublished' => true]);
        foreach ($articles as $article) {
            $urls[] = [
                'loc' => $this->generateUrl('blog_show', ['slug' => $article->getSlug()], 0),
                'lastmod' => ($article->getUpdatedAt() ?? $article->getPublishedAt())->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }

        // Films
        $films = $filmRepository->findBy(['isPublished' => true]);
        foreach ($films as $film) {
            $urls[] = [
                'loc' => $this->generateUrl('film_show', ['slug' => $film->getSlug()], 0),
                'lastmod' => $film->getPublishedAt()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => '0.9'
            ];
        }

        // Jeux
        $urls[] = ['loc' => $this->generateUrl('blog_games', [], 0), 'changefreq' => 'monthly', 'priority' => '0.7'];
        $urls[] = ['loc' => $this->generateUrl('blog_game_snake', [], 0), 'changefreq' => 'monthly', 'priority' => '0.6'];
        $urls[] = ['loc' => $this->generateUrl('blog_game_mickael_world', [], 0), 'changefreq' => 'monthly', 'priority' => '0.6'];
        $urls[] = ['loc' => $this->generateUrl('blog_game_paires', [], 0), 'changefreq' => 'monthly', 'priority' => '0.6'];
        $urls[] = ['loc' => $this->generateUrl('blog_game_ecrase', [], 0), 'changefreq' => 'monthly', 'priority' => '0.6'];

        $response = new Response(
            $this->renderView('sitemap/index.xml.twig', ['urls' => $urls]),
            200
        );
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }

    #[Route('/robots.txt', name: 'robots_txt', defaults: ['_format' => 'txt'])]
    public function robotsTxt(): Response
    {
        $response = new Response(
            $this->renderView('robots.txt.twig'),
            200
        );
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
