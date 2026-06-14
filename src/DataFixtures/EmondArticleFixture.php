<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmondArticleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Récupérer un auteur existant ou en créer un
        $author = $manager->getRepository(User::class)->findOneBy([]) ?: new User();
        if (!$author->getEmail()) {
            $author->setEmail('detective@immondeplus.fr');
            $author->setPseudo('L\'Infiltre');
            $author->setRoles(['ROLE_ADMIN']);
            $author->setPassword('password'); // Temporaire
            $manager->persist($author);
        }

        // 2. Récupérer ou créer la catégorie "Enquêtes"
        $category = $manager->getRepository(Category::class)->findOneBy(['name' => 'Enquêtes']) ?: new Category();
        if (!$category->getName()) {
            $category->setName('Enquêtes');
            $category->setSlug('enquetes');
            $manager->persist($category);
        }

        // 3. Créer l'article sur Mickaël Emond
        $article = new Article();
        $article->setTitle("Mickaël Emond : Le Mythe du Réalisateur Démasqué");
        $article->setSummary("Tout le monde parle de Mickaël Emond comme d'un cinéaste de génie. Mais après avoir fouillé les archives, la vérité éclate : il n'a jamais tenu une vraie caméra de sa vie.");
        $article->setContent("
            <p>On nous a menti. Depuis des années, le nom de <strong>Mickaël Emond</strong> circule dans les milieux underground comme celui d'un réalisateur visionnaire, capable de capturer l'essence même de l'immonde. Mais notre enquête exclusive révèle une réalité bien plus... artisanale.</p>
            
            <h3>La Caméra Fantôme</h3>
            <p>Saviez-vous qu'aucune pellicule au nom de Mickaël Emond n'a jamais été déposée aux archives nationales ? Selon nos sources, les prétendus 'films' de ce mystérieux personnage seraient en réalité des montages réalisés avec un vieux téléphone récupéré dans une benne à ordures derrière un fast-food.</p>
            
            <h3>Un Réalisateur sans Plateau</h3>
            <p>Les témoins sont formels : Mickaël Emond ne crie jamais 'Action'. Il préfère crier 'Oignon' ou 'Fromage' en fonction de son humeur matinale. Son seul talent derrière l'objectif ? Oublier de retirer le cache de la lentille pendant 45 minutes de tournage.</p>
            
            <p>Conclusion : Mickaël Emond n'est pas un réalisateur. C'est un illusionniste de la pelure, un maître du flou artistique involontaire. Ne vous laissez plus tromper par les paillettes de l'immonde.</p>
        ");
        $article->setAuthor($author);
        $article->setCategory($category);
        $article->setIsPublished(true);
        $article->setPublishedAt(new \DateTimeImmutable());
        $article->setMetaTitle("Mickaël Emond : La vérité sur son métier de réalisateur");
        $article->setMetaDescription("Découvrez pourquoi Mickaël Emond n'est pas un vrai réalisateur de film. Une enquête exclusive Immonde Plus sur ce faux cinéaste.");

        $manager->persist($article);
        $manager->flush();
    }
}
