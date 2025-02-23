<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('admin@gmail.com');
        $user1->setFirstname('Ana');
        $user1->setLastname('Test');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, "123456"));
        $user1->setRoles(['ROLE_ADMIN']);

        $user2 = new User();
        $user2->setEmail('user@gmail.com');
        $user2->setFirstname('Bob');
        $user2->setLastname('Test');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, "123456"));
        $user2->setRoles(['ROLE_USER']);

        $user3 = new User();
        $user3->setEmail('manager@gmail.com');
        $user3->setFirstname('Alex');
        $user3->setLastname('Test');
        $user3->setPassword($this->passwordHasher->hashPassword($user3, "123456"));
        $user3->setRoles(['ROLE_MANAGER']);

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($user3);

        $manager->flush();
    }
}
