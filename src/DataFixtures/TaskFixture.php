<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 30; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(4));
            $task->setStatus($faker->randomElement(TaskStatus::cases()));
            $task->setPriority($faker->randomElement(TaskPriority::cases()));
            $task->setDescription($faker->paragraphs(2, true));


            /** @var \App\Entity\Project $project */
            $project = $this->getReference(
                ProjectFixture::PROJECT_REFERENCE . rand(0, 4),
                Project::class
            );
            $task->setProject($project);

            if ($faker->boolean(70)) {
                /** @var \App\Entity\User $assignee */
                $assignee = $this->getReference(
                    UserFixture::USER_REFERENCE . rand(0, 9),
                    User::class
                );
                $task->setAssignee($assignee);
            }

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixture::class,
            UserFixture::class,
        ];
    }
}
