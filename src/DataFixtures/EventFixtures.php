<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }


    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $users[] = $this->getReference('user_' . $i);
        }

        for ($i = 0; $i < 20; $i++) {
            $startDate = $faker->dateTimeBetween((new \DateTime('now'))->format('Y-m-d'), '2024-12-31');
            $endDate = $faker->dateTimeBetween($startDate, '2024-12-31');

            while ($endDate < $startDate) {
                $endDate = $faker->dateTimeBetween($startDate, '2024-12-31');
            }

            $event = new Event();
            $event->setTitle($faker->words(5, true));
            $event->setDescription($faker->words(30, true));
            $event->setStartDate($startDate);
            $event->setEndDate($endDate);
            $event->setIsPublic($faker->boolean(50));
            $event->setNbMaxParticipants($faker->randomNumber(2));
            $event->setOwner($users[array_rand($users)]);

            $manager->persist($event);
        }

        $manager->flush();
    }
}
