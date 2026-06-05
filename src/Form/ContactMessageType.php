<?php

namespace App\Form;

use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Votre Nom',
                'attr' => ['placeholder' => 'M. Tout-le-monde']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre Email',
                'attr' => ['placeholder' => 'contact@exemple.com']
            ])
            ->add('subject', TextType::class, [
                'label' => 'Objet de la dénonciation',
                'attr' => ['placeholder' => 'Affaire Pelure d\'Oignon...']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre Message',
                'attr' => ['rows' => 6, 'placeholder' => 'Détaillez vos informations ici...']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer le message secret',
                'attr' => ['class' => 'btn-submit']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
        ]);
    }
}
