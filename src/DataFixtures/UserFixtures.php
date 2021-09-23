<?php

namespace App\DataFixtures;

use App\Classes\Enum\EnumRoles;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = (new User())
            ->setUsername("fabrriv")
            ->setRoles([EnumRoles::ROLE_DEV]);
        $user->setPassword($this->passwordHasher->hashPassword($user, "dev"));
        $manager->persist($user);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ["base", "user"];
    }
}
