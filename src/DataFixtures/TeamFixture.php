<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TeamFixture extends Fixture implements DependentFixtureInterface
{
    public const TEAM_REFERENCE = 'team_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $team = new Team();
            $team->setName($faker->company());
            $team->setDescription($faker->paragraphs(2, true));

            $membersCount = rand(3, 5);
            for ($j = 0; $j < $membersCount; $j++) {
                $team->addMember(
                    $this->getReference(
                        UserFixture::USER_REFERENCE . rand(0, 9),
                        User::class
                    )
                );
            }

            $manager->persist($team);
            $this->addReference(self::TEAM_REFERENCE . $i, $team);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixture::class];
    }
}
