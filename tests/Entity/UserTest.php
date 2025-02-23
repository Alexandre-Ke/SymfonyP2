<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser(): void
    {
        // 🔥 Création d'un nouvel utilisateur
        $user = new User();
        $user->setEmail('test@example.com')
            ->setFirstname('Jean')
            ->setLastname('Dupont')
            ->setRoles(['ROLE_USER']);

        // 🔥 Vérification des données de l'utilisateur
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('Jean', $user->getFirstname());
        $this->assertEquals('Dupont', $user->getLastname());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertCount(1, $user->getRoles());

        // 🔥 Mock du EntityManagerInterface
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // ✅ Vérification que persist() est appelé 1 fois avec l'instance de User
        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        // ✅ Vérification que flush() est appelé 1 fois
        $entityManagerMock->expects($this->once())
            ->method('flush');

        // ✅ Simulation de l'enregistrement de l'utilisateur
        $entityManagerMock->persist($user);
        $entityManagerMock->flush();
    }
}
