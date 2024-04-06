<?php

namespace App\EventSubscriber;

use App\Repository\AccessTokenRepository;
use App\Repository\AuthCodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccessTokenRepository $tokenRepository,
        private readonly AuthCodeRepository $codeRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $sessionId = $event->getRequest()->getSession()->getId();
        $event->getRequest()->getSession()->invalidate();

        $tokens = $this->tokenRepository->findBy(["sessionId" => $sessionId]);
        foreach ($tokens as $token) $token->revoke();

        $codes = $this->codeRepository->findBy(["sessionId" => $sessionId]);
        foreach ($codes as $code) $this->entityManager->remove($code);

        $this->entityManager->flush();

        if ($redirectUri = $event->getRequest()->query->get("redirect_uri")) {
            $urlParts = parse_url($redirectUri);

            if ($urlParts['host'] === $event->getRequest()->getHost()) {
                $response = new RedirectResponse($redirectUri);
                $event->setResponse($response);
            };
        }
    }
}