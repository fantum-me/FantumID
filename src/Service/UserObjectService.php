<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class UserObjectService
{
    public function __construct(
        private readonly RouterInterface $router
    )
    {
    }

    public function getUserObject(User $user): array
    {
        return [
            "id" => $user->getId(),
            "name" => $user->getName(),
            "email" => $user->getEmail(),
            "avatar" => $this->router->generate("api_avatar", ["id" => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
    }
}
