<?php

use App\Entity\Article;
use App\Entity\Category;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Script de MISE À JOUR de contenu et STRUCTURE pour la PROD
 * À SUPPRIMER APRÈS USAGE !
 */

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();
$conn = $entityManager->getConnection();

echo "<h1>🛠 Mise à jour de la Production</h1>";

// 1. Forcer l'ajout de la colonne pseudo si elle manque encore
echo "<h2>1. Vérification de la structure...</h2>";
try {
    $conn->executeQuery("ALTER TABLE user ADD pseudo VARCHAR(255) DEFAULT NULL");
    echo "Colonne 'pseudo' ajoutée.<br>";
} catch (\Exception $e) {
    echo "Colonne 'pseudo' déjà présente ou erreur ignorée.<br>";
}

try {
    $conn->executeQuery("ALTER TABLE article ADD video_url VARCHAR(500) DEFAULT NULL");
    echo "Colonne 'video_url' ajoutée.<br>";
} catch (\Exception $e) {
    echo "Colonne 'video_url' déjà présente ou erreur ignorée.<br>";
}

// 2. Gérer la catégorie "Films"
echo "<h2>2. Gestion de la catégorie 'Films'...</h2>";
$categoryRepo = $entityManager->getRepository(Category::class);
$filmsCategory = $categoryRepo->findOneBy(['name' => 'Films']);

if (!$filmsCategory) {
    $filmsCategory = new Category();
    $filmsCategory->setName('Films');
    $filmsCategory->setSlug('films');
    $entityManager->persist($filmsCategory);
    $entityManager->flush();
    echo "Catégorie 'Films' créée.<br>";
} else {
    echo "Catégorie 'Films' existante.<br>";
}

// 3. Mettre à jour tous les articles
echo "<h2>3. Mise à jour des articles...</h2>";
$articleRepo = $entityManager->getRepository(Article::class);
$articles = $articleRepo->findAll();

foreach ($articles as $article) {
    // Changement de la catégorie
    $article->setCategory($filmsCategory);

    // Mise à jour du résumé si c'est le texte générique
    if ($article->getSummary() === "Découvrez cette enquête exclusive en vidéo.") {
        $article->setSummary("Enquête exclusive en vidéo : " . $article->getTitle() . ". Une révélation Immonde Plus.");
    }

    echo "Article mis à jour : <b>" . $article->getTitle() . "</b><br>";
}

$entityManager->flush();

echo "<h2>4. Nettoyage du Cache...</h2>";
try {
    // Fonction de suppression récursive sécurisée
    $recursiveDelete = function($dir) use (&$recursiveDelete) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $recursiveDelete("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    };

    $cacheDir = dirname(__DIR__) . '/var/cache';
    if (is_dir($cacheDir)) {
        $files = array_diff(scandir($cacheDir), array('.', '..'));
        foreach ($files as $file) {
            $recursiveDelete("$cacheDir/$file");
        }
        echo "Dossier var/cache vidé physiquement.<br>";
    }
} catch (\Exception $e) {
    echo "Échec du nettoyage automatique : " . $e->getMessage() . "<br>";
}

echo "<h2>5. Nettoyage APCu (si disponible)...</h2>";
if (function_exists('apcu_clear_cache')) {
    if (apcu_clear_cache()) {
        echo "Cache APCu vidé avec succès.<br>";
    } else {
        echo "Échec du vidage du cache APCu.<br>";
    }
} else {
    echo "APCu n'est pas activé pour PHP CLI ou la fonction est absente.<br>";
}

echo "<hr><h2 style='color:green;'>✅ Opération terminée avec succès !</h2>";
echo "<p>Veuillez supprimer ce fichier (public/fix_prod.php).</p>";
