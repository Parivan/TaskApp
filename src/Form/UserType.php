<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\UserRole;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractType<\App\Entity\User>
 */
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => ['maxlength' => 255],
            ])
            ->add('name', null, [
                'attr' => ['maxlength' => 255],
            ])
            ->add('role', EnumType::class, [
                'class' => UserRole::class,
                'choice_label' => fn(UserRole $role) => $role->getLabel(),
            ])
            ->add('teams', EntityType::class, [
                'class' => Team::class,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
