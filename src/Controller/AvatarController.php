<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AvatarGenerationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvatarController extends AbstractController
{
    #[Route("/avatar/{id}", name: "api_avatar", methods: "GET")]
    public function get(
        string $id,
        UserRepository $userRepository,
        Filesystem $filesystem,
        AvatarGenerationService $userAvatarService
    ): Response {
        if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            $user = $userRepository->findOneBy(["email" => $id]);
        } else {
            $user = $userRepository->find($id);
        }

        if ($user) {
            $path = $this->getParameter("app.data.user.avatar") . DIRECTORY_SEPARATOR . $user->getId() . ".png";
            if ($filesystem->exists($path)) {
                return $this->file($path, $user->getId());
            }
        }

        $path = $userAvatarService->generateAvatar($user?->getId() ?? $id);
        return $this->file($path, $id);
    }
}
