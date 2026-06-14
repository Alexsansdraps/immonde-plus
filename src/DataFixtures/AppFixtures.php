<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Film;
use App\Entity\FilmSeries;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1. Création des utilisateurs demandés (Prod & Dev)
        $usersData = [
            ['email' => 'admin@immonde.plus', 'pseudo' => 'Admin', 'pass' => 'admin123', 'role' => 'ROLE_ADMIN'],
            ['email' => 'delicieux@immondeplus.fr', 'pseudo' => 'Délicieux', 'pass' => 'alicia123', 'role' => 'ROLE_ADMIN'],
            ['email' => 'miserable@immondeplus.fr', 'pseudo' => 'Misérable', 'pass' => 'alicia123', 'role' => 'ROLE_ADMIN'],
            ['email' => 'delicieuse@immondeplus.fr', 'pseudo' => 'Délicieuse', 'pass' => 'alicia123', 'role' => 'ROLE_ADMIN'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPseudo($data['pseudo']);
            $user->setRoles([$data['role']]);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['pass']));
            $manager->persist($user);
            $users[] = $user;
        }

        // 2. Création de données FAKE (Uniquement pour le remplissage)
        // Séries de Films
        $series = [];
        for ($i = 1; $i <= 3; $i++) {
            $serie = new FilmSeries();
            $serie->setTitle("Dossier Secret #" . $i);
            $serie->setDescription($faker->paragraph());
            $manager->persist($serie);
            $series[] = $serie;
        }

        // Films (Série)
        foreach ($series as $s) {
            for ($j = 1; $j <= 3; $j++) {
                $film = new Film();
                $film->setTitle($s->getTitle() . " : Partie " . $j);
                $film->setDescription($faker->paragraph(3));
                $film->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
                $film->setIsPublished(true);
                $film->setSeries($s);
                // Video fallback URL
                $film->setVideoUrl('https://www.youtube.com/embed/dQw4w9WgXcQ');
                $manager->persist($film);
            }
        }

        // Films (Indépendants)
        for ($i = 1; $i <= 5; $i++) {
            $film = new Film();
            $film->setTitle("Révélation Choc #" . $i);
            $film->setDescription($faker->paragraph(3));
            $film->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $film->setIsPublished(true);
            $film->setVideoUrl('https://www.youtube.com/embed/dQw4w9WgXcQ');
            $manager->persist($film);
        }

        // Catégories
        $categories = [];
        foreach (['Films', 'Articles', 'Révélations', 'Société', 'Politique'] as $catName) {
            $category = new Category();
            $category->setName($catName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Tags
        $tags = [];
        foreach (['Oignon', 'Scandale', 'Secret', 'Exclusive', 'Abominable'] as $tagName) {
            $tag = new Tag();
            $tag->setName($tagName);
            $manager->persist($tag);
            $tags[] = $tag;
        }

        // Articles & Commentaires
        for ($i = 1; $i <= 15; $i++) {
            $article = new Article();
            $article->setTitle($faker->sentence(6));
            $article->setContent($faker->paragraphs(5, true));
            $article->setSummary($faker->text(150));
            $article->setIsPublished(true);
            $article->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));
            $article->setAuthor($faker->randomElement($users));
            $article->setCategory($faker->randomElement($categories));
            
            // Ajouter 2 tags au hasard
            for ($j = 0; $j < 2; $j++) {
                $article->addTag($faker->randomElement($tags));
            }

            $manager->persist($article);

            // Ajouter des commentaires
            for ($k = 0; $k < $faker->numberBetween(0, 5); $k++) {
                $comment = new Comment();
                $comment->setAuthorName($faker->name);
                $comment->setAuthorEmail($faker->email);
                $comment->setContent($faker->sentence(10));
                $comment->setIsApproved($faker->boolean(70)); // 70% de chance d'être approuvé
                $comment->setArticle($article);
                $comment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween($article->getPublishedAt()->format('Y-m-d H:i:s'), 'now')));
                
                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
