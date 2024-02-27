<?php

namespace App\EventListener;

use App\Entity\User;
use App\Serializer\MediaObjectNormalizer;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JWTCreatedListener
{
    private MediaObjectNormalizer $mediaObjectNormalizer;

    public function __construct(MediaObjectNormalizer $mediaObjectNormalizer, NormalizerInterface $normalizer)
    {
        $this->mediaObjectNormalizer = $mediaObjectNormalizer;
        $this->mediaObjectNormalizer->setNormalizer($normalizer);
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
