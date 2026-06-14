<?php

namespace App\Controller\Admin;

use App\Entity\FilmSeries;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class FilmSeriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FilmSeries::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Série de Films')
            ->setEntityLabelInPlural('Séries de Films');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        
        yield FormField::addFieldset('Informations de la Série');
        yield TextField::new('title', 'Titre de la série');
        yield SlugField::new('slug', 'URL (Slug)')
            ->setTargetFieldName('title')
            ->hideOnIndex();
        yield TextareaField::new('description', 'Description');
        
        yield FormField::addFieldset('Design du Dossier');
        yield ImageField::new('image', 'Image de couverture du dossier')
            ->setBasePath('uploads/films')
            ->setUploadDir('public/uploads/films')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield FormField::addFieldset('Contenu');
        yield AssociationField::new('films', 'Films de la série')
            ->onlyOnDetail();
    }
}
