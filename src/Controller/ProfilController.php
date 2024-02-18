<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MediaObjectRepository;
use App\Repository\UserRepository;
use App\Serializer\MediaObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ProfilController extends AbstractController
{
    private UserRepository $userRepository;
    private MediaObjectNormalizer $mediaObjectNormalizer;
    private MediaObjectRepository $mediaObjectRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        MediaObjectNormalizer $mediaObjectNormalizer,
        MediaObjectRepository $mediaObjectRepository,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->userRepository = $userRepository;
        $this->mediaObjectNormalizer = $mediaObjectNormalizer;
        $this->mediaObjectRepository = $mediaObjectRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(
        "/api/me",
        name: "get_current_user",
        defaults: [
            "_api_resource_class" => User::class,
            "_api_item_operation_name" => "get_current_user"
        ],
        methods: ["GET"]
    )]
    public function getCurrentUser(UserInterface $user): JsonResponse
    {
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        $media = $this->mediaObjectNormalizer->normalize($user->getMediaObject());

        $userData = [
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'username' => $user->getFrontUsername(),
            'mediaObjects' => $media['contentUrl']
        ];

        return new JsonResponse($userData);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(
        "/api/me/update",
        name: "update_current_user",
        defaults: [
            "_api_resource_class" => User::class,
            "_api_item_operation_name" => "patch"
        ],
        methods: ["PATCH"]
    )]
    public function updateCurrentUser(UserInterface $user, Request $request): JsonResponse
    {
        if (!$user instanceof User) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        $requestContent = json_decode($request->getContent(), true);
        $user->setEmail($requestContent['email']);
        $user->setFirstname($requestContent['firstname']);
        $user->setLastname($requestContent['lastname']);
        $user->setFrontUsername($requestContent['username']);

        if (isset($requestContent['mediaObject'])) {
            $media = $this->mediaObjectRepository->find($requestContent['mediaObject']);
            if (!$media) {
                return new JsonResponse(['message' => 'Media not found'], 404);
            }
            $user->setMediaObject($media);
        }

        if (isset($requestContent['password'])) {
            if ($requestContent['password'] !== $requestContent['confirmPassword']) {
                return new JsonResponse(['message' => 'Passwords do not match'], 400);
            }
            $user->setPassword($this->passwordHasher->hashPassword($user, $requestContent['password']));
        }

        $this->userRepository->saveUser($user);

        if ($user->getMediaObject()) {
            $media = $this->mediaObjectNormalizer->normalize($user->getMediaObject());
        } else {
            $media = null;
        }
        $userData = [
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'username' => $user->getFrontUsername(),
            'mediaObjects' => $media['contentUrl'],
        ];

        return new JsonResponse($userData);
    }
}
