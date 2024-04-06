<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserAvatarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvatarController extends AbstractController
{
    #[Route("/avatar/{id}", name: "api_avatar", methods: "GET")]
    public function get(
        User              $user,
        Filesystem        $filesystem,
        UserAvatarService $userAvatarService
    ): Response
    {
        $path = $userAvatarService->getAvatarPath($user);

        if (!$filesystem->exists($path)) $userAvatarService->generateAvatar($user);

        if ($filesystem->exists($path)) return $this->file($path, $user->getId());
        else return $this->file($userAvatarService->getDefaultAvatarPath(), $user->getId());
    }
}
