<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class MovieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));

        foreach (range(1, 40) as $i) {
            $movieTitle = '';
            if (!empty($faker->unique()->movie)) {
                $movieTitle = $faker->unique()->movie;
            }
            $movie = (new Movie())
                ->setTitle($movieTitle)
                ->setDescription($faker->text(200))
                ->setDuration(rand(100, 250))
                ->setReleaseDate($faker->dateTimeBetween(
                    "-50 years",
                ))
                ->setDirector($faker->name)
                ->setEntries(rand(5000, 10000000))
                ->setBudget(rand(100000, 100000000))
                ->setWebsite($faker->url)
                ->setAverageRating(rand(0, 10))
                ->setCategory($this->getReference('category_' . rand(1, 18)))
                ->setCreator($this->getReference('user_' . rand(1, 6)))
                ->addMediaObject($this->getReference('mediaObject_movie_' . $i));
            foreach (range(1, rand(2, 28)) as $j) {
                $movie->addActor($this->getReference('actor_' . rand(1, 30)));
            }
            $manager->persist($movie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ActorFixtures::class,
            MediaObjectFixtures::class,
        ];
    }
}
