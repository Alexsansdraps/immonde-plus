<?php

namespace App\Controller\Admin;

use App\Entity\Film;
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

class FilmCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Film::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Film')
            ->setEntityLabelInPlural('Films')
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->setDefaultSort(['publishedAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Nouveau Film');
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier');
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->setLabel('Supprimer');
            });
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        
        yield FormField::addFieldset('Informations du Film');
        yield TextField::new('title', 'Titre du film');
        yield AssociationField::new('series', 'Appartient à la série')
            ->setRequired(false);
        yield SlugField::new('slug', 'URL (Slug)')
            ->setTargetFieldName('title')
            ->hideOnIndex();
        yield TextareaField::new('description', 'Description / Synopsis')
            ->setFormType(CKEditorType::class);
        
        yield FormField::addFieldset('Média & Vidéo');
        yield ImageField::new('image', 'Affiche / Image de couverture')
            ->setBasePath('uploads/films')
            ->setUploadDir('public/uploads/films')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield ImageField::new('videoFile', 'Uploader la vidéo (.mp4)')
            ->setBasePath('videos')
            ->setUploadDir('public/videos')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setRequired(false);

        yield UrlField::new('videoUrl', 'OU Lien vidéo externe (YouTube, etc.)');

        yield FormField::addFieldset('Publication');
        yield BooleanField::new('isPublished', 'Publier le film');
        yield DateTimeField::new('publishedAt', 'Date de publication');
    }
}
