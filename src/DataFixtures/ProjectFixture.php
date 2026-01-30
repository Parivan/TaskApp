<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Team;
use App\Enum\ProjectStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjectFixture extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_REFERENCE = 'project_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setTitle($faker->sentence(3));
            $project->setStatus($faker->randomElement(ProjectStatus::cases()));
            $project->setDescription($faker->paragraphs(2, true));


            /** @var \App\Entity\Team $team */
            $team = $this->getReference(
                TeamFixture::TEAM_REFERENCE . rand(0, 2),
                Team::class
            );
            $project->setTeam($team);

            $manager->persist($project);
            $this->addReference(self::PROJECT_REFERENCE . $i, $project);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TeamFixture::class];
    }
}
