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

## 🚀 Lancer le projet

Dans un terminal, **à la racine du projet**, exécuter les commandes dans l'ordre :

```bash
# 1. Installer les dépendances PHP
composer install

# 2. Créer la base de données (SQLite en local) + le schéma
php bin/console doctrine:database:create
php bin/console doctrine:schema:create

# 3. Charger les données de démonstration (articles, comptes, catégories…)
php bin/console doctrine:fixtures:load

# 4. Démarrer le serveur web
symfony serve
```

➡️ Le site est ensuite accessible sur **http://localhost:8000**

Pour arrêter le serveur : `Ctrl + C` (ou `symfony server:stop`).

> **Sans la Symfony CLI**, remplacer l'étape 4 par : `php -S localhost:8000 -t public/`
> **Configuration locale** (base de prod, clés…) : copier `.env` vers `.env.local` (non versionné) et y mettre vos valeurs.
> **Base de données** : on utilise `doctrine:schema:create` (et non `migrations:migrate`) car le schéma fait foi à partir des entités. Pour repartir d'une base propre : `php bin/console doctrine:database:drop --force` puis reprendre à l'étape 2.

## Back-office (administration)

Le back-office **EasyAdmin** est accessible sur **http://localhost:8000/admin** (connexion requise).

Avec les données de démonstration chargées (étape 3), un compte administrateur est disponible :

| Email | Mot de passe |
|---|---|
| `admin@immonde.plus` | `admin123` |

> ⚠️ Ces identifiants sont des **données de démonstration locales uniquement** — à ne jamais utiliser en production.

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
