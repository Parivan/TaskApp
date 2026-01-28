<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Enum\ProjectStatus;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('status', EnumType::class, [
                'class' => ProjectStatus::class,
                'choice_label' => fn(ProjectStatus $status) => $status->getLabel(),
            ])
            ->add('team', EntityType::class, [
                'class' => Team::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
