<?php

namespace App\EventListener;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Movie::class)]
class MovieListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(Movie $movie, PrePersistEventArgs $event): void
    {
        if (!$this->security->getUser() instanceof User) {
            return;
        }
        $movie->setCreator($this->security->getUser());
    }
}
