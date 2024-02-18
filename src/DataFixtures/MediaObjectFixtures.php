<?php

namespace App\DataFixtures;

use App\Entity\MediaObject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaObjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //MediaObject for movies
        foreach (range(1, 40) as $i) {
            copy(
                __DIR__ . '/../../public/fixtures_media/movies/' . rand(1, 5) . '.jpg',
                __DIR__ . '/../../public/fixtures_media/movies/' . $i . 'copy.jpg'
            );
            $file = new UploadedFile(
                __DIR__ . '/../../public/fixtures_media/movies/' . $i . 'copy.jpg',
                'film' . $i . '.jpg',
                'image/jpeg',
                null,
                true
            );

            $mediaObject = (new MediaObject())
                ->setFile($file)
                ->setFilePath($file->getPathname());

            $manager->persist($mediaObject);
            $this->addReference('mediaObject_movie_' . $i, $mediaObject);
        }

        //MediaObject for actors
        foreach (range(1, 30) as $i) {
            copy(
                __DIR__ . '/../../public/fixtures_media/actors/' . rand(1, 4) . '.jpg',
                __DIR__ . '/../../public/fixtures_media/actors/' . $i . 'copy.jpg'
            );
            $file = new UploadedFile(
                __DIR__ . '/../../public/fixtures_media/actors/' . $i . 'copy.jpg',
                'actor' . $i . '.jpg',
                'image/jpeg',
                null,
                true
            );

            $mediaObject = (new MediaObject())
                ->setFile($file)
                ->setFilePath($file->getPathname());

            $manager->persist($mediaObject);
            $this->addReference('mediaObject_actor_' . $i, $mediaObject);
        }

        //MediaObject for users
        foreach (range(1, 6) as $i) {
            copy(
                __DIR__ . '/../../public/fixtures_media/users/' . rand(1, 2) . '.png',
                __DIR__ . '/../../public/fixtures_media/users/' . $i . 'copy.png'
            );
            $file = new UploadedFile(
                __DIR__ . '/../../public/fixtures_media/users/' . $i . 'copy.png',
                'users' . $i . '.png',
                'image/png',
                null,
                true
            );

            $mediaObject = (new MediaObject())
                ->setFile($file)
                ->setFilePath($file->getPathname());

            $manager->persist($mediaObject);
            $this->addReference('mediaObject_user_' . $i, $mediaObject);
        }

        $manager->flush();
    }
}
