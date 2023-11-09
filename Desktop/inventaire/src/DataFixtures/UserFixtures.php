<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void

    {     $user = new User();
        $user->setEmail('candidate@example.com');
        $user->setRoles(['ROLE_CANDIDATE']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'candidatepassword');
        $user->setPassword($hashedPassword);
        $this->addReference('user_' . 10, $user);
        $manager->persist($user);

        $manager->flush();

        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'adminpassword');
        $user->setPassword($hashedPassword);
        $this->addReference('user_admin', $user);
        $manager->persist($user);

        $manager->flush();
    }
}

