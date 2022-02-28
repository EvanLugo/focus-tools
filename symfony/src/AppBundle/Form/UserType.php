<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', EmailType::class, [
                'attr' => [
                    'placeholder' => 'email@gmail.com'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => '......'
                ]
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Evan Lugo'
                ]
            ])
            ->add('player', TextType::class, [
                'attr' => [
                    'placeholder' => 'LPG Levi'
                ],
                'label' => 'Player Tag Name'
            ])
            ->add('xbox', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'fab fa-xbox']
            ])
            ->add('psn', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'fab fa-playstation']
            ])
            ->add('register', SubmitType::class, [
                'label' => 'register',
                'attr' => [
                    'class' => 'btn btn-dark'
                ]
            ]);

    }
}
