<?php

namespace App\Entity;

use App\Entity\Trait\TimestampTrait;
use App\Entity\Trait\UidTrait;
use App\Repository\AuthCodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: AuthCodeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AuthCode
{
    use UidTrait;
    use TimestampTrait;

    #[ORM\Column(length: 255)]
    private ?string $code;

    #[ORM\ManyToOne(inversedBy: 'authCodes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $targetUser = null;

    #[ORM\ManyToOne(inversedBy: 'authCodes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?OAuthClient $client = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sessionId = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->code = sha1(random_bytes(32));
    }

    public function getCode(): ?string
    {
        return $this->code;
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
}
