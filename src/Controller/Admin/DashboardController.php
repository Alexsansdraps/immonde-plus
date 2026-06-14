<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\ContactMessage;
use App\Entity\Film;
use App\Entity\FilmSeries;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\ContactMessageRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ContactMessageRepository $contactMessageRepository
    ) {
    }

    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ArticleCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<b>Imm🧅nde Plus</b> <small>Admin</small>')
            ->setFaviconPath('favicon.ico')
            ->setLocales(['fr' => '🇫🇷 Français', 'en' => '🇬🇧 English'])
            ->renderContentMaximized()
            ->renderSidebarMinimized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Voir le site', 'fas fa-eye', '/');
        
        yield MenuItem::section('Contenu');
        yield MenuItem::linkTo(FilmSeriesCrudController::class, 'Séries de Films', 'fas fa-folder-open');
        yield MenuItem::linkTo(FilmCrudController::class, 'Films', 'fas fa-film');
        yield MenuItem::linkTo(ArticleCrudController::class, 'Articles', 'fas fa-newspaper');

        // Notification badge for unread comments
        $unreadComments = $this->commentRepository->count(['isRead' => false]);
        yield MenuItem::linkTo(CommentCrudController::class, 'Commentaires', 'fas fa-comments')
            ->setBadge($unreadComments, 'danger');

        // Notification badge for unread contact messages
        $unreadMessages = $this->contactMessageRepository->count(['isRead' => false]);
        yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages', 'fas fa-envelope')
            ->setBadge($unreadMessages, 'warning');
        
        yield MenuItem::section('Classification');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Catégories', 'fas fa-list');
        yield MenuItem::linkTo(TagCrudController::class, 'Tags', 'fas fa-tags');
        
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fas fa-users');
        
        yield MenuItem::section('Paramètres');
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-sign-out-alt');
    }
}
