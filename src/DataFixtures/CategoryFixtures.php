<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Action',
            'Aventure',
            'Comédie',
            'Drame',
            'Fantastique',
            'Horreur',
            'Policier',
            'Science-fiction',
            'Thriller',
            'Western',
            'Animation',
            'Biopic',
            'Documentaire',
            'Guerre',
            'Historique',
            'Musical',
            'Romance',
            'Sport',
            'Super-héros',
            'Téléfilm',
        ];

        foreach (range(1, 20) as $i) {
            $category = (new Category())
                ->setName($categories[$i - 1]);
            $manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }

        $manager->flush();
    }
}
