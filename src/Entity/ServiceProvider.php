<?php

namespace App\Entity;

use App\Entity\Trait\TimestampTrait;
use App\Entity\Trait\UidTrait;
use App\Repository\ServiceProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Random\RandomException;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ServiceProviderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ServiceProvider implements UserInterface
{
    use UidTrait;
    use TimestampTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?OAuthClient $client = null;

    #[ORM\Column(length: 255)]
    private ?string $apiKey = null;

    /**
     * @throws RandomException
     */
    public function __construct()
    {
        $this->generateApiKey();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getClient(): ?OAuthClient
    {
        return $this->client;
    }

    public function setClient(OAuthClient $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @throws RandomException
     */
    public function generateApiKey(): static
    {
        $this->apiKey = bin2hex(random_bytes(32));

        return $this;
    }

    public function getRoles(): array
    {
        return ["ROLE_SERVICE"];
    }

    public function eraseCredentials(): void
    {}

    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}
