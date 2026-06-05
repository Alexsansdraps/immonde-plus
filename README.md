# Immonde-plus

Application web développée avec **Symfony 7.4** (PHP 8.2+) : blog, pages de contenu, formulaire de contact et back-office d'administration.

## Stack technique

- **PHP** 8.2+ · **Symfony** 7.4
- **Doctrine ORM** + migrations — SQLite en local, MySQL en production
- **EasyAdmin** (back-office) + **CKEditor** (édition de contenu riche)
- **Twig**, **AssetMapper**, **Stimulus**, **Turbo**
- **KnpPaginator**, **Doctrine Extensions**

## Prérequis

- PHP ≥ 8.2 avec les extensions `ctype` et `iconv`
- [Composer](https://getcomposer.org/)
- *(Optionnel)* la [Symfony CLI](https://symfony.com/download)

## Installation

```bash
# 1. Installer les dépendances
composer install

# 2. Configuration locale (ce fichier n'est PAS versionné)
#    Copier .env vers .env.local et y renseigner vos valeurs (DATABASE_URL, APP_SECRET…)
cp .env .env.local

# 3. Base de données + migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# (Optionnel) Données de démonstration
php bin/console doctrine:fixtures:load
```

## Lancer le projet

```bash
symfony serve
# ou, sans la Symfony CLI :
php -S localhost:8000 -t public/
```

Le site est alors accessible sur http://localhost:8000.

## Configuration & secrets

- Les **secrets** (mot de passe de la BDD de prod, clés API…) se mettent dans **`.env.local`** (ignoré par git) ou dans les **variables d'environnement du serveur** — **jamais** dans un fichier versionné.
- En **production**, la base est hébergée sur **MySQL (IONOS)** ; en **local**, c'est **SQLite** par défaut.

## Structure du projet

| Dossier | Rôle |
|---|---|
| `src/` | Code applicatif (Controller, Entity, Form, Repository, Security) |
| `templates/` | Vues Twig (blog, contact, pages légales, sécurité…) |
| `config/` | Configuration Symfony et des bundles |
| `migrations/` | Migrations de base de données |
| `assets/` | JS / CSS (Stimulus, styles) |
| `public/` | Racine web (point d'entrée, images, vidéos) |
| `tests/` | Tests (PHPUnit) |
| `translations/` | Fichiers de traduction |
