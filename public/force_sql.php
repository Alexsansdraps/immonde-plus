<?php
/**
 * SCRIPT DE RÉPARATION D'URGENCE (SQL DIRECT)
 * À SUPPRIMER IMMÉDIATEMENT APRÈS UTILISATION
 */

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Chargement de l'environnement
if (file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// Démarrage du Kernel
$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

// Récupération de la connexion Doctrine
$conn = $kernel->getContainer()->get('doctrine')->getConnection();

echo "<html style='background: black; color: #39ff14; font-family: monospace; padding: 20px;'>";
echo "<h2>--- DÉMARRAGE DE LA RÉPARATION SQL D'URGENCE ---</h2>";

try {
    echo "<p>> 1. Création de la table `contact_message`...</p>";
    $conn->executeQuery("CREATE TABLE IF NOT EXISTS contact_message (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, is_read TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    echo "<p>[OK]</p>";
} catch (\Exception $e) {
    echo "<p style='color: orange;'>[IGNORÉ] " . $e->getMessage() . "</p>";
}

try {
    echo "<p>> 2. Création de la table `film_series`...</p>";
    $conn->executeQuery("CREATE TABLE IF NOT EXISTS film_series (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    echo "<p>[OK]</p>";
} catch (\Exception $e) {
    echo "<p style='color: orange;'>[IGNORÉ] " . $e->getMessage() . "</p>";
}

try {
    echo "<p>> 3. Ajout de la colonne `series_id` à la table `film`...</p>";
    $conn->executeQuery("ALTER TABLE film ADD series_id INT DEFAULT NULL");
    echo "<p>[OK] Colonne ajoutée.</p>";
} catch (\Exception $e) {
    echo "<p style='color: orange;'>[IGNORÉ] Colonne probablement déjà existante. (" . $e->getMessage() . ")</p>";
}

try {
    echo "<p>> 4. Ajout de la clé étrangère sur `film`...</p>";
    $conn->executeQuery("ALTER TABLE film ADD CONSTRAINT FK_8244BE225278319C FOREIGN KEY (series_id) REFERENCES film_series (id)");
    $conn->executeQuery("CREATE INDEX IDX_8244BE225278319C ON film (series_id)");
    echo "<p>[OK] Contraintes et index créés.</p>";
} catch (\Exception $e) {
    echo "<p style='color: orange;'>[IGNORÉ] Contraintes probablement déjà en place. (" . $e->getMessage() . ")</p>";
}

echo "<h2>--- RÉPARATION TERMINÉE ---</h2>";
echo "<p style='font-size: 1.5em; font-weight: bold;'>Votre site devrait maintenant fonctionner.</p>";
echo "<p><a href='/' style='color: white;'>RETOURNER SUR LE SITE</a></p>";
echo "<p style='color: red;'>⚠️ N'oubliez pas de supprimer ce fichier (public/force_sql.php) via FTP !</p>";
echo "</html>";