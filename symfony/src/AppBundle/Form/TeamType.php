<?php

namespace AppBundle\Form;

use AppBundle\Entity\Team;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('tag', TextType::class, [
                'attr' => [
                    'maxlength' => 5
                ]
            ])
            ->add('region', ChoiceType::class, [
                'choices' => [
                    'NA' => 'NA',
                    'SA' => 'SA'
                ]
            ])
            ->add('image', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save Team',
                'attr' => [
                    'class' => 'btn btn-dark'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class
        ]);
    }
}
