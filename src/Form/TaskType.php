<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('status', EnumType::class, [
                'class' => TaskStatus::class,
                'choice_label' => fn(TaskStatus $status) => $status->getLabel(),
            ])
            ->add('priority', EnumType::class, [
                'class' => TaskPriority::class,
                'choice_label' => fn(TaskPriority $priority) => $priority->getLabel(),
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
            ])
            ->add('assignee', EntityType::class, [
                'class' => User::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
