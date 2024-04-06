<?php

namespace App\Entity;

use App\Entity\Trait\TimestampTrait;
use App\Entity\Trait\UidTrait;
use App\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AccessToken
{
    use UidTrait;
    use TimestampTrait;

    const LIFE_TIME = 604800; // a week

    #[ORM\Column(length: 255)]
    private ?string $token;

    #[ORM\ManyToOne(inversedBy: 'accessTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $targetUser = null;

    #[ORM\ManyToOne(inversedBy: 'accessTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OAuthClient $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sessionId = null;

    #[ORM\Column]
    private bool $isRevoked = false;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->generateToken();
    }

    public function isExpired(): bool
    {
        return time() > $this->getCreatedAt()->getTimestamp() + self::LIFE_TIME;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function generateToken(): static
    {
        $this->token = sha1(random_bytes(32));

        return $this;
    }

    public function getTargetUser(): ?User
    {
        return $this->targetUser;
    }

    public function setTargetUser(?User $targetUser): static
    {
        $this->targetUser = $targetUser;

        return $this;
    }

    public function getClient(): ?OAuthClient
    {
        return $this->client;
    }

    public function setClient(?OAuthClient $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(?string $sessionId): static
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }

    public function revoke(): static
    {
        $this->isRevoked = true;

        return $this;
    }
}
