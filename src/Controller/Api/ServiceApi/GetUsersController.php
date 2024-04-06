<?php

namespace App\Controller\Api\ServiceApi;

use App\Entity\ServiceProvider;
use App\Repository\UserRepository;
use App\Service\UserObjectService;
use App\Utils\RequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Uid\Uuid;

class GetUsersController extends AbstractController
{
    #[Route('/api/users', name: 'api_users', methods: "POST")]
    public function get(
        Request                      $request,
        #[CurrentUser] UserInterface $user,
        UserRepository               $userRepository,
        UserObjectService            $userObjectService
    ): Response
    {
        if (!($user instanceof ServiceProvider)) throw new AccessDeniedHttpException(); // This route is reserved for Services

        $ids = RequestHandler::getRequestParameter($request, "ids");
        if (!is_array($ids)) throw new BadRequestHttpException("ids must be an array");

        $users = [];
        foreach ($ids as $id) {
            $user = $userRepository->findOneBy(["id" => $id]);
            if (!$user) throw new BadRequestHttpException("user $id does not exist");
            $users[] = $userObjectService->getUserObject($user);
        }

        return $this->json($users);
    }
}
