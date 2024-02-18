<?php

namespace App\DataFixtures;

use App\Entity\Nationality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NationaliteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $languages = [
            'French',
            'English',
            'German',
            'Spanish',
            'Italian',
            'Russian',
            'Chinese',
            'Japanese',
            'Korean',
            'Arabic',
            'Portuguese',
            'Brazilian',
            'Indian',
            'Canadian',
            'Australian',
            'English',
            'Irish',
            'Scottish',
            'Welsh',
            'South African',
            'New Zealander',
            'Dutch',
            'Belgian',
            'Swiss',
            'Austrian',
            'Swedish',
            'Norwegian',
            'Danish',
            'Finnish',
            'Polish',
            'Czech',
            'Slovak',
            'Hungarian',
            'Romanian',
            'Bulgarian',
        ];

        $countries = [
            'France',
            'USA',
            'Germany',
            'Spain',
            'Italy',
            'Russia',
            'China',
            'Japan',
            'Korea',
            'Arabia',
            'Portugal',
            'Brazil',
            'India',
            'Canada',
            'Australia',
            'England',
            'Ireland',
            'Scotland',
            'Wales',
            'South Africa',
            'New Zealand',
            'Netherlands',
            'Belgium',
            'Switzerland',
            'Austria',
            'Sweden',
            'Norway',
            'Denmark',
            'Finland',
            'Poland',
            'Czech Republic',
            'Slovakia',
            'Hungary',
            'Romania',
            'Bulgaria',
        ];

        foreach (range(1, 34) as $i) {
            $nationalite = (new Nationality())
                ->setCountry($countries[$i - 1])
                ->setLanguage($languages[$i - 1]);
            $manager->persist($nationalite);
            $this->addReference('nationalite_' . $i, $nationalite);
        }

        $manager->flush();
    }
}
