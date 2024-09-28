<?php

namespace App\Entity;

use App\Entity\Trait\TimestampTrait;
use App\Entity\Trait\UidTrait;
use App\Repository\UserRepository;
use App\Validator as AcmeAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface
{
    use UidTrait;
    use TimestampTrait;

    #[ORM\Column(length: 63)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 63)]
    #[Assert\NoSuspiciousCharacters]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NoSuspiciousCharacters]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'targetUser', targetEntity: AuthCode::class, orphanRemoval: true)]
    private Collection $authCodes;

    #[ORM\OneToMany(mappedBy: 'targetUser', targetEntity: AccessToken::class, orphanRemoval: true)]
    private Collection $accessTokens;

    #[ORM\Column(length: 31)]
    #[AcmeAssert\ValidColorScheme]
    private string $colorScheme = "light";

    #[ORM\Column(nullable: true)]
    private ?string $authCode = null;

    #[ORM\Column]
    private int $trustedVersion;

    public function __construct()
    {
        $this->trustedVersion = 0;
        $this->authCodes = new ArrayCollection();
        $this->accessTokens = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
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
            $authCode->setTargetUser($this);
        }

        return $this;
    }

    public function removeAuthCode(AuthCode $authCode): static
    {
        if ($this->authCodes->removeElement($authCode)) {
            // set the owning side to null (unless already changed)
            if ($authCode->getTargetUser() === $this) {
                $authCode->setTargetUser(null);
            }
        }

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
            $accessToken->setTargetUser($this);
        }

        return $this;
    }

    public function removeAccessToken(AccessToken $accessToken): static
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getTargetUser() === $this) {
                $accessToken->setTargetUser(null);
            }
        }

        return $this;
    }

    public function getColorScheme(): string
    {
        return $this->colorScheme;
    }

    public function setColorScheme(string $colorScheme): static
    {
        $this->colorScheme = $colorScheme;

        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
    }

    public function getTrustedTokenVersion(): int
    {
        return $this->trustedVersion;
    }
}
