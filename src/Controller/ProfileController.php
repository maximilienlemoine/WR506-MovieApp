<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MediaObjectRepository;
use App\Repository\UserRepository;
use App\Serializer\MediaObjectNormalizer;
use App\Service\MailerService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ProfileController extends AbstractController
{
    private UserRepository $userRepository;
    private MediaObjectNormalizer $mediaObjectNormalizer;
    private MediaObjectRepository $mediaObjectRepository;
    private MailerService $mailerService;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        MediaObjectNormalizer $mediaObjectNormalizer,
        MediaObjectRepository $mediaObjectRepository,
        MailerService $mailerService,
        UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->userRepository = $userRepository;
        $this->mediaObjectNormalizer = $mediaObjectNormalizer;
        $this->mediaObjectRepository = $mediaObjectRepository;
        $this->mailerService = $mailerService;
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

    //Forgot PASSWORD

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    #[Route(
        "/api/password/forgot",
        name: "forgot_password",
        methods: ["POST"]
    )]
    public function forgotPassword(Request $request): JsonResponse
    {
        $email = json_decode($request->getContent(), true)['email'];
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
        $user->setResetToken(bin2hex(random_bytes(32)));
        $this->userRepository->saveUser($user);

        $this->mailerService->sendEmail(
            $user->getEmail(),
            'Mot de passe oublié',
            sprintf('Movie App :
            Pour réinitialiser votre mot de passe, cliquez sur le lien suivant : 
            %s/reset-password/%s', $this->getParameter('FRONT_URL'), $user->getResetToken())
        );

        return new JsonResponse(['message' => 'Email sent']);
    }

    //RESET PASSWORD

    #[Route(
        "/api/password/reset",
        name: "reset_password",
        methods: ["POST"]
    )]
    public function resetPassword(Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);
        $token = $requestContent['token'];
        $user = $this->userRepository->findOneBy(['resetToken' => $token]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        $password = $requestContent['password'];
        $confirmPassword = $requestContent['confirmPassword'];

        if ($password !== $confirmPassword) {
            return new JsonResponse(['message' => 'Passwords do not match'], 400);
        }

        $user->setResetToken(null);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->userRepository->saveUser($user);

        return new JsonResponse(['message' => 'Password updated']);
    }
}
