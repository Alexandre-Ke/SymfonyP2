<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsCommand(
    name: 'client:add',
    description: 'Ajoute un client via la ligne de commande',
)]
class ClientAddCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');

        $firstname = $helper->ask($input, $output, new Question('Prénom du client : '));
        $violations = $this->validator->validate($firstname, [
            new Assert\NotBlank(),
            new Assert\Regex([
                'pattern' => "/^[a-zA-ZÀ-ÿ '-]+$/",
                'message' => "Le prénom ne doit pas contenir de caractères spéciaux"
            ]),
        ]);
        if (count($violations) > 0) {
            $io->error($violations[0]->getMessage());
            return Command::FAILURE;
        }

        $lastname = $helper->ask($input, $output, new Question('Nom du client : '));
        $violations = $this->validator->validate($lastname, [
            new Assert\NotBlank(),
            new Assert\Regex([
                'pattern' => "/^[a-zA-ZÀ-ÿ '-]+$/",
                'message' => "Le nom ne doit pas contenir de caractères spéciaux"
            ]),
        ]);
        if (count($violations) > 0) {
            $io->error($violations[0]->getMessage());
            return Command::FAILURE;
        }

        // Demande de l'email
        $email = $helper->ask($input, $output, new Question('Email du client : '));
        $violations = $this->validator->validate($email, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);
        if (count($violations) > 0) {
            $io->error($violations[0]->getMessage());
            return Command::FAILURE;
        }

        $phoneNumber = $helper->ask($input, $output, new Question('Numéro de téléphone (optionnel) : '));

        $address = $helper->ask($input, $output, new Question('Adresse (optionnelle) : '));

        $client = new Client();
        $client->setFirstname($firstname)
               ->setLastname($lastname)
               ->setEmail($email)
               ->setPhoneNumber($phoneNumber)
               ->setAddress($address)
               ->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $io->success('Client ajouté avec succès.');

        return Command::SUCCESS;
    }
}
