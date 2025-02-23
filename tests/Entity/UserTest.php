<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser(): void
    {
        $user = new User();
        $user->setEmail('test@example.com')
            ->setFirstname('Jean')
            ->setLastname('Dupont')
            ->setRoles(['ROLE_USER']);

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('Jean', $user->getFirstname());
        $this->assertEquals('Dupont', $user->getLastname());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertCount(1, $user->getRoles());

        $entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $entityManagerMock->expects($this->once())
            ->method('flush');

        $entityManagerMock->persist($user);
        $entityManagerMock->flush();
    }
}
