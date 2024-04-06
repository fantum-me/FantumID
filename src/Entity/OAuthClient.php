<?php

namespace App\Entity;

use App\Entity\Trait\TimestampTrait;
use App\Entity\Trait\UidTrait;
use App\Repository\OAuthClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: OAuthClientRepository::class)]
#[ORM\HasLifecycleCallbacks]
class OAuthClient
{
    use UidTrait;
    use TimestampTrait;

    #[ORM\Column(length: 255)]
    private ?string $secret = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: AccessToken::class, orphanRemoval: true)]
    private Collection $accessTokens;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: AuthCode::class, orphanRemoval: true)]
    private Collection $authCodes;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->generateSecret();
        $this->accessTokens = new ArrayCollection();
        $this->authCodes = new ArrayCollection();
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @throws Exception
     */
    public function generateSecret(): static
    {
        $this->secret = bin2hex(random_bytes(32));

        return $this;
    }

    /**
     * @return Collection<int, AccessToken>
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    public function addAccessToken(AccessToken $accessToken): static
    {
        if (!$this->accessTokens->contains($accessToken)) {
            $this->accessTokens->add($accessToken);
            $accessToken->setClient($this);
        }

        return $this;
    }

    public function removeAccessToken(AccessToken $accessToken): static
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getClient() === $this) {
                $accessToken->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AuthCode>
     */
    public function getAuthCodes(): Collection
    {
        return $this->authCodes;
    }

    public function addAuthCode(AuthCode $authCode): static
    {
        if (!$this->authCodes->contains($authCode)) {
            $this->authCodes->add($authCode);
            $authCode->setClient($this);
        }

        return $this;
    }

    public function removeAuthCode(AuthCode $authCode): static
    {
        if ($this->authCodes->removeElement($authCode)) {
            // set the owning side to null (unless already changed)
            if ($authCode->getClient() === $this) {
                $authCode->setClient(null);
            }
        }

        return $this;
    }
}
