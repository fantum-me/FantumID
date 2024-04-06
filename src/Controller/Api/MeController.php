<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserObjectService;
use App\Utils\ColorScheme;
use App\Utils\RequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/me', name: 'api_me')]
class MeController extends AbstractController
{
    #[Route(name: '_get', methods: ['GET'])]
    public function get(#[CurrentUser] User $user, UserObjectService $userObjectService): JsonResponse
    {
        $userObject = $userObjectService->getUserObject($user);
        $userObject["scheme"] = $user->getColorScheme();
        return $this->json($userObject);
    }

    #[Route(name: '_patch', methods: ['PATCH'])]
    public function patch(
        Request                $request,
        #[CurrentUser] User    $user,
        ValidatorInterface     $validator,
        EntityManagerInterface $entityManager
    ): Response
    {
        $scheme = RequestHandler::getRequestParameter($request, "scheme", false);
        $name = RequestHandler::getRequestParameter($request, "name", false);

        if ($scheme) {
            if (!ColorScheme::isValid($scheme)) throw new BadRequestHttpException("$scheme is not a valid color scheme");
            $user->setColorScheme($scheme);
        }

        if ($name) $user->setName($name);

        if (count($errors = $validator->validate($user)) > 0) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $entityManager->flush();

        return new Response("done");
    }
}
