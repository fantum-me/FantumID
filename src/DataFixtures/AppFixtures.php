<?php

namespace App\DataFixtures;

use App\Entity\OAuthClient;
use App\Entity\ServiceProvider;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $password = $this->encoder->hashPassword($user, 'admin');

        $user->setName("Admin")
            ->setEmail("admin@admin.com")
            ->setPassword($password)
            ->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);

        $client = new OAuthClient();
        $manager->persist($client);

        $service = new ServiceProvider();
        $service->setName("cloud")->setClient($client);
        $manager->persist($service);

        $manager->flush();
    }
}
