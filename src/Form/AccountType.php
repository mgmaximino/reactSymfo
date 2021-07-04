<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration("Prénom","Votre prénom..."))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom","Votre nom de famille..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email","Votre adresse email..."))
            ->add('picture', FileType::class, [
                'label' => "Avatar (jpg, png, gif)",
                'data_class'=>null,
                'required'=>false,
            ])
            ->add('presentation', TextType::class, $this->getConfiguration("Introduction","Présentatez-vous !"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default', 'Registration']
        ]);
    }
}