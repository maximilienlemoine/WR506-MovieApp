<?php

namespace App\EventListener;

use App\Entity\MediaObject;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: MediaObject::class)]
class MediaObjectListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postUpdate(MediaObject $mediaObject): void
    {
        if (
            is_null($mediaObject->getMovie())
            && is_null($mediaObject->getActor())
            && is_null($mediaObject->getUser())
        ) {
            $this->entityManager->remove($mediaObject);
            $this->entityManager->flush();
        }
    }
}
