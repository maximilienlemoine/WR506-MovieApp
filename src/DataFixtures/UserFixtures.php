<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    /**
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 6) as $i) {
            $role = ['ROLE_USER'];
            if ($i === 1) {
                $role = ['ROLE_ADMIN'];
            }

            $user = new User();
            $user->setEmail('user' . $i . '@example.com')
                ->setPassword(
                    $this->hasher->hashPassword($user, 'password')
                )
                ->setFirstname('Firstname' . $i)
                ->setLastname('Lastname' . $i)
                ->setFrontUsername('Username' . $i)
                ->setRoles($role)
                ->setMediaObject($this->getReference('mediaObject_user_' . $i));
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MovieFixtures::class,
            MediaObjectFixtures::class,
        ];
    }
}
