<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\ProjectStatus;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * @extends AbstractType<\App\Entity\Project>
 */
class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => ['maxlength' => 255],
            ])
            ->add('status', EnumType::class, [
                'class' => ProjectStatus::class,
                'choice_label' => fn(ProjectStatus $status) => $status->getLabel(),
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 4],
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
