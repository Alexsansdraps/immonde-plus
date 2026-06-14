<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Doctrine\ORM\EntityManagerInterface;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')
            ->setSearchFields(['title', 'content', 'summary'])
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewArticle = Action::new('viewArticle', 'Voir l\'article', 'fa fa-eye')
            ->linkToRoute('blog_show', function (Article $article): array {
                return [
                    'slug' => $article->getSlug(),
                ];
            })
            ->setHtmlAttributes(['target' => '_blank']);

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Nouvel Article');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Supprimer');
            })
            ->add(Crud::PAGE_INDEX, $viewArticle)
            ->add(Crud::PAGE_DETAIL, $viewArticle);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        
        yield FormField::addFieldset('Contenu Principal');
        yield TextField::new('title', 'Titre de l\'article');
        
        yield SlugField::new('slug', 'URL (Slug)')
            ->setTargetFieldName('title')
            ->hideOnIndex();

        yield TextareaField::new('content', 'Contenu de l\'article')
            ->setFormType(CKEditorType::class)
            ->hideOnIndex();

        yield TextareaField::new('summary', 'Extrait / Résumé')
            ->setFormType(CKEditorType::class)
            ->setHelp('Un court résumé affiché sur la liste des articles.')
            ->hideOnIndex();

        yield FormField::addFieldset('Média & Vidéo');
        yield ImageField::new('image', 'Image de couverture')
            ->setBasePath('uploads/articles')
            ->setUploadDir('public/uploads/articles')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield ImageField::new('videoFile', 'Uploader une vidéo (.mp4)')
            ->setBasePath('videos')
            ->setUploadDir('public/videos')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setRequired(false)
            ->setHelp('Fichier MP4 uniquement. Sera stocké dans public/videos.');

        yield UrlField::new('videoUrl', 'OU Lien vidéo externe (YouTube, Infomaniak)')
            ->setHelp('Exemple: https://www.youtube.com/watch?v=...')
            ->hideOnIndex();

        yield FormField::addFieldset('Classification');
        yield AssociationField::new('category', 'Catégorie');
        yield AssociationField::new('tags', 'Tags / Étiquettes');
        
        yield BooleanField::new('isPublished', 'Publier l\'article');
        
        yield FormField::addFieldset('SEO');
        yield TextField::new('metaTitle', 'Titre SEO (Balise Title)')
            ->setHelp('Si vide, le titre de l\'article sera utilisé.')
            ->hideOnIndex();
        yield TextareaField::new('metaDescription', 'Description SEO (Meta Description)')
            ->setHelp('Si vide, l\'extrait sera utilisé.')
            ->hideOnIndex();

        yield FormField::addFieldset('Informations');
        yield AssociationField::new('author', 'Auteur')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm();
        yield DateTimeField::new('publishedAt', 'Date de publication')->hideOnForm();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;

        $entityInstance->setAuthor($this->getUser());
        
        if ($entityInstance->isPublished() && $entityInstance->getPublishedAt() === null) {
            $entityInstance->setPublishedAt(new \DateTimeImmutable());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Article) return;

        if ($entityInstance->isPublished() && $entityInstance->getPublishedAt() === null) {
            $entityInstance->setPublishedAt(new \DateTimeImmutable());
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
