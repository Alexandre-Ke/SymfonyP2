<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ✅ Listes de prénoms et noms français
        $firstnames = ['Jean', 'Marie', 'Pierre', 'Sophie', 'Jacques', 'Claire', 'Luc', 'Camille', 'Thomas', 'Julie'];
        $lastnames = ['Dupont', 'Durand', 'Lefevre', 'Martin', 'Moreau', 'Rousseau', 'Blanc', 'Garnier', 'Lemoine', 'Bertrand'];

        $adresses = [
            '123 Rue de Paris, 75001 Paris',
            '45 Avenue de la République, 75011 Paris',
            '12 Boulevard des Capucines, 75009 Paris',
            '89 Rue de la Paix, 75002 Paris',
            '67 Avenue des Champs-Élysées, 75008 Paris',
            '23 Rue de Rivoli, 75004 Paris',
            '14 Avenue Montaigne, 75008 Paris',
            '32 Boulevard Haussmann, 75009 Paris',
            '78 Rue Saint-Honoré, 75001 Paris',
            '56 Avenue Victor Hugo, 75016 Paris',
        ];

        // Générer 10 clients aléatoires
        for ($i = 0; $i < 10; $i++) {
            // ✅ Choisir aléatoirement un prénom et un nom
            $firstname = $firstnames[array_rand($firstnames)];
            $lastname = $lastnames[array_rand($lastnames)];

            // ✅ Générer un email unique à partir du prénom et du nom
            $email = strtolower($firstname) . '.' . strtolower($lastname) . $i . '@exemple.com';

            // ✅ Générer un numéro de téléphone français
            $phoneNumber = '0' . rand(6, 7) . rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);

            // ✅ Adresse aléatoire
            $address = $adresses[array_rand($adresses)];

            // ✅ Création de l'entité Client
            $client = new Client();
            $client->setFirstname($firstname)
                   ->setLastname($lastname)
                   ->setEmail($email)
                   ->setPhoneNumber($phoneNumber)
                   ->setAddress($address)
                   ->setCreatedAt(new \DateTimeImmutable());

            // ✅ Persister le client
            $manager->persist($client);
        }

        // ✅ Enregistrer en base de données
        $manager->flush();
    }
}
