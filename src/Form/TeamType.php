<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<\App\Entity\Team>
 */

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => ['maxlength' => 255],
            ])
            ->add('members', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])->add('description', TextareaType::class, [
                    'required' => false,
                    'attr' => ['rows' => 4],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ]);
    }
}
