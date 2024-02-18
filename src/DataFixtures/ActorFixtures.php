<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Xylis\FakerCinema\Provider\Person;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $reward = [
            'Oscar',
            'César',
            'Palme d\'or',
            'Prix d\'interprétation masculine',
            'Prix d\'interprétation féminine',
            'Golden Globes',
            'Grammy Awards',
        ];

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Person($faker));

        foreach (range(1, 30) as $i) {
            $fullname = $faker->unique()->actor;
            $actor = (new Actor())
                ->setBirthday($faker->dateTimeBetween(
                    '-80 years'
                ))
                ->setReward($reward[rand(0, 6)])
                ->setFirstName(substr($fullname, 0, strpos($fullname, ' ')))
                ->setLastName(substr($fullname, strpos($fullname, ' ') + 1))
                ->setNationality($this->getReference('nationalite_' . rand(1, 34)));
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            NationaliteFixtures::class,
        ];
    }
}
