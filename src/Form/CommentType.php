<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('authorName', TextType::class, [
                'label' => 'Votre nom',
                'attr' => ['placeholder' => 'M. Tout-le-monde']
            ])
            ->add('authorEmail', EmailType::class, [
                'label' => 'Votre email',
                'attr' => ['placeholder' => 'email@exemple.com']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre témoignage',
                'attr' => ['rows' => 5, 'placeholder' => 'Dites-nous tout sur Immonde Plus...']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Poster le commentaire',
                'attr' => ['class' => 'btn-submit']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
