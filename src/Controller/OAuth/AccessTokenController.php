<?php

namespace App\Controller\OAuth;

use App\Entity\AccessToken;
use App\Repository\AuthCodeRepository;
use App\Repository\OAuthClientRepository;
use App\Utils\RequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class AccessTokenController extends AbstractController
{
    #[Route('/oauth/access_token', name: 'app_oauth_access_token', methods: ["POST"])]
    public function index(
        Request                $request,
        EntityManagerInterface $entityManager,
        AuthCodeRepository     $codeRepository,
        OAuthClientRepository  $clientRepository,
    ): Response
    {
        $codeKey = RequestHandler::getRequestParameter($request, "code");

        $rawAuthorization = explode(" ", $request->headers->get("Authorization"));

        if ($rawAuthorization[0] !== "Bearer") throw new BadRequestHttpException("Bearer is required");
        $authorization = explode(":", base64_decode($rawAuthorization[1]));

        $clientId = $authorization[0];
        $secret = $authorization[1];
        if (!$secret) throw new BadRequestHttpException("client_secret is required");

        $client = $clientRepository->findOneBy(["id" => $clientId, "secret" => $secret]);
        if (!$client) throw new BadRequestHttpException("client_secret is invalid");

        $code = $codeRepository->findOneBy(["code" => $codeKey, "client" => $client]);
        if (!$code) throw new BadRequestHttpException("code is invalid");

        $token = new AccessToken();
        $token->setClient($client)
            ->setTargetUser($code->getTargetUser())
            ->setSessionId($code->getSessionId());

        $client->addAccessToken($token);

        $entityManager->persist($token);

        $entityManager->remove($code);
        $entityManager->flush();

        return $this->json(["access_token" => $token->getToken()]);
    }
}
