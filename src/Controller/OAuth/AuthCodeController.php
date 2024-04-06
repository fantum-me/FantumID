<?php

namespace App\Controller\OAuth;

use App\Entity\AuthCode;
use App\Entity\OAuthClient;
use App\Entity\User;
use App\Repository\OAuthClientRepository;
use App\Repository\ServiceProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthCodeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly OAuthClientRepository  $clientRepository
    )
    {
    }

    #[Route('/oauth/code', name: 'app_oauth_code', methods: ['GET'])]
    public function index(
        #[CurrentUser] $user,
        Request        $request,
    ): Response
    {
        $clientId = $request->query->get('client_id');
        if (!$clientId) throw new BadRequestHttpException("client_id is missing");

        $client = $this->clientRepository->findOneBy(["id" => $clientId]);
        if (!$client) throw new BadRequestHttpException("client_id is invalid");

        return $this->generateResponse($user, $request, $client);
    }

    #[Route('/oauth/code/service', name: 'app_oauth_code_service', methods: ['GET'])]
    public function service(
        #[CurrentUser]            $user,
        Request                   $request,
        ServiceProviderRepository $serviceRepository
    ): Response
    {
        $serviceName = $request->query->get('service');
        if (!$serviceName) throw new BadRequestHttpException("service is missing");

        $service = $serviceRepository->findOneBy(["name" => $serviceName]);
        if (!$service) throw new BadRequestHttpException("unknown service");

        return $this->generateResponse($user, $request, $service->getClient(), $request->getSession()->getId());
    }

    private function generateResponse(User $user, Request $request, OAuthClient $client, ?string $sessionId = null): RedirectResponse|Response
    {
        $redirectUri = $request->query->get('redirect_uri');
        if (!$redirectUri) throw new BadRequestHttpException("redirect_uri is missing");

        $code = new AuthCode();
        $code->setTargetUser($user)
            ->setClient($client)
            ->setSessionId($sessionId);

        $client->addAuthCode($code);

        $this->entityManager->persist($code);
        $this->entityManager->flush();

        return $this->redirect($redirectUri . "?code=" . $code->getCode());
    }
}
