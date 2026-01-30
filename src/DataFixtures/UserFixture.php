<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixture extends Fixture
{
    public const USER_REFERENCE = 'user_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setName($faker->name());
            $user->setEmail($faker->unique()->safeEmail());
            $user->setRole($faker->randomElement(UserRole::cases()));

            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE . $i, $user);
        }

        $manager->flush();
    }
}
