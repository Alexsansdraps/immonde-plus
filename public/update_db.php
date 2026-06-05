<?php

/**
 * SCRIPT DE RÉPARATION, MISE À JOUR ET INJECTION DE CONTENU (PRODUCTION)
 * Usage: https://votre-domaine.fr/update_db.php
 */

use App\Kernel;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $env = $context['APP_ENV'] ?? 'prod';
    $debug = (bool) ($context['APP_DEBUG'] ?? false);
    
    $kernel = new Kernel($env, $debug);
    $application = new Application($kernel);
    $application->setAutoExit(false);

    $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $forceVersion = $request->query->get('force');

    $output = new BufferedOutput();
    $cacheDir = dirname(__DIR__) . '/var/cache';

    echo "<html><body style='background:#000; color:#39ff14; font-family:monospace; padding:20px;'>";
    echo "<h2>--- PROTOCOLE DE MAINTENANCE ET INJECTION ---</h2>";

    try {
        // 1. GESTION DES MIGRATIONS
        if ($forceVersion) {
            echo "<p>> Réinitialisation de la version : $forceVersion...</p>";
            $input = new ArrayInput([
                'command' => 'doctrine:migrations:version',
                'version' => "DoctrineMigrations\\$forceVersion",
                '--delete' => true,
                '--no-interaction' => true,
            ]);
            $application->run($input, $output);
        }

        echo "<p>> Exécution des migrations...</p>";
        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
            '--no-interaction' => true,
            '--allow-no-migration' => true,
        ]);
        $application->run($input, $output);
        echo "<p style='color:white;'>[OK] Base de données à jour.</p>";

        // 2. INJECTION DE L'ARTICLE MICKAËL EMOND
        echo "<h2>--- INJECTION DE CONTENU ---</h2>";
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        
        // A. Récupérer l'auteur admin
        $author = $em->getRepository(User::class)->findOneBy([]) ;
        if (!$author) {
            echo "<p style='color:orange;'>[INFO] Aucun utilisateur trouvé. Création d'un compte système...</p>";
            $author = new User();
            $author->setEmail('detective@immondeplus.fr');
            $author->setPseudo('L\'Infiltre');
            $author->setRoles(['ROLE_ADMIN']);
            $author->setPassword('system_generated');
            $em->persist($author);
        }

        // B. Gérer la catégorie
        $category = $em->getRepository(Category::class)->findOneBy(['name' => 'Enquêtes']);
        if (!$category) {
            $category = new Category();
            $category->setName('Enquêtes');
            $category->setSlug('enquetes');
            $em->persist($category);
            echo "<p>[OK] Catégorie 'Enquêtes' créée.</p>";
        }

        // C. Créer l'article si il n'existe pas
        $existing = $em->getRepository(Article::class)->findOneBy(['title' => "Mickaël Emond : Le Mythe du Réalisateur Démasqué"]);
        if (!$existing) {
            $article = new Article();
            $article->setTitle("Mickaël Emond : Le Mythe du Réalisateur Démasqué");
            $article->setSummary("Tout le monde parle de Mickaël Emond comme d'un cinéaste de génie. Mais après avoir fouillé les archives, la vérité éclate : il n'a jamais tenu une vraie caméra de sa vie.");
            $article->setContent("
                <p>On nous a menti. Depuis des années, le nom de <strong>Mickaël Emond</strong> circule dans les milieux underground comme celui d'un réalisateur visionnaire, capable de capturer l'essence même de l'immonde. Mais notre enquête exclusive révèle une réalité bien plus... artisanale.</p>
                <h3>La Caméra Fantôme</h3>
                <p>Saviez-vous qu'aucune pellicule au nom de Mickaël Emond n'a jamais été déposée aux archives nationales ? Selon nos sources, les prétendus 'films' de ce mystérieux personnage seraient en réalité des montages réalisés avec un vieux téléphone récupéré dans une benne à ordures.</p>
                <h3>Un Réalisateur sans Plateau</h3>
                <p>Les témoins sont formels : Mickaël Emond ne crie jamais 'Action'. Il préfère crier 'Oignon' ou 'Fromage'. Son seul talent ? Oublier de retirer le cache de la lentille.</p>
                <p>Conclusion : Mickaël Emond n'est pas un réalisateur. C'est un illusionniste de la pelure.</p>
            ");
            $article->setAuthor($author);
            $article->setCategory($category);
            $article->setIsPublished(true);
            $article->setPublishedAt(new \DateTimeImmutable());
            $article->setMetaTitle("Mickaël Emond : La vérité sur son métier de réalisateur");
            $article->setMetaDescription("Découvrez pourquoi Mickaël Emond n'est pas un vrai réalisateur de film.");
            
            $em->persist($article);
            $em->flush();
            echo "<p style='color:white; font-weight:bold;'>[SUCCÈS] L'article sur Mickaël Emond a été injecté !</p>";
        } else {
            echo "<p>[INFO] L'article existe déjà dans la base.</p>";
        }

        // 3. NETTOYAGE DU CACHE
        echo "<h2>--- NETTOYAGE ---</h2>";
        $recursiveDelete = function($dir) use (&$recursiveDelete) {
            if (!is_dir($dir)) return;
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $recursiveDelete("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        };

        if (is_dir($cacheDir)) {
            $files = array_diff(scandir($cacheDir), array('.', '..'));
            foreach ($files as $file) { $recursiveDelete("$cacheDir/$file"); }
        }
        echo "<p style='color:white;'>[OK] Cache nettoyé.</p>";
        echo "<p><a href='/' style='color:#39ff14; font-size: 20px;'>--> RETOUR AU SITE <--</a></p>";

    } catch (\Exception $e) {
        echo "<p style='color:red;'>CRITICAL FAILURE: " . $e->getMessage() . "</p>";
        echo "<pre style='color:#555;'>" . $e->getTraceAsString() . "</pre>";
    }

    echo "<h2>--- FIN DE LA TRANSMISSION ---</h2>";
    echo "</body></html>";
    return $kernel;
};
