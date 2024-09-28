<?php

namespace App\Security;

use App\Repository\AccessTokenRepository;
use App\Repository\ServiceProviderRepository;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\TwoFactorAuthenticator;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly ServiceProviderRepository $serviceRepository,
        private readonly AccessTokenRepository     $tokenRepository
    )
    {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        $service = $this->serviceRepository->findOneBy(["apiKey" => $accessToken]);
        if ($service) return new UserBadge($service->getUserIdentifier());

        $token = $this->tokenRepository->findOneBy(["token" => $accessToken]);

        if ($token == null) throw new BadCredentialsException('Invalid credentials.');
        if ($token->isExpired()) throw new BadCredentialsException('Expired token.');
        if ($token->isRevoked()) throw new BadCredentialsException('Revoked token.');

        return new UserBadge($token->getTargetUser()->getUserIdentifier(), null, [
            TwoFactorAuthenticator::FLAG_2FA_COMPLETE => true
        ]);
    }
}
