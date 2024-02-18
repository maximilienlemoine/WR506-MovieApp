<?php

namespace App\EventListener;

use App\Entity\User;
use App\Serializer\MediaObjectNormalizer;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class JWTCreatedListener
{
    private MediaObjectNormalizer $mediaObjectNormalizer;

    public function __construct(MediaObjectNormalizer $mediaObjectNormalizer)
    {
        $this->mediaObjectNormalizer = $mediaObjectNormalizer;
    }

    /**
     * @throws ExceptionInterface
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }
        $media = $this->mediaObjectNormalizer->normalize($user->getMediaObject());
        $payload['frontUsername'] = $user->getFrontUsername();
        $payload['mediaObjects'] = $media['contentUrl'];

        $event->setData($payload);
    }
}
