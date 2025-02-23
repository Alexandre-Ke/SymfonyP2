<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsCommand(
    name: 'product:import-csv',
    description: 'Importe un fichier CSV de produits depuis le dossier public/uploads/',
)]
class ProductImportCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::REQUIRED, 'Nom du fichier CSV à importer (placé dans public/uploads/)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $input->getArgument('filename');
        $filePath = 'public/uploads/' . $filename;

        if (!file_exists($filePath)) {
            $io->error("Le fichier \"$filename\" n'existe pas dans le dossier public/uploads/");
            return Command::FAILURE;
        }

        $io->section("Lecture du fichier CSV : $filename");
        $handle = fopen($filePath, 'r');

        $header = fgetcsv($handle, 1000, ';');

        if ($header !== ['name', 'description', 'price']) {
            $io->error("Le fichier CSV doit contenir les colonnes suivantes : name, description, price");
            return Command::FAILURE;
        }

        $productsImported = 0;
        $errors = [];

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $name = $data[0];
            $description = $data[1];
            $price = $data[2];

            $violations = $this->validator->validate($price, [
                new Assert\NotBlank(),
                new Assert\Positive(),
            ]);

            if (count($violations) > 0) {
                $errors[] = "Erreur sur le produit \"$name\" : " . $violations[0]->getMessage();
                continue;
            }

            $product = new Product();
            $product->setName($name)
                ->setDescription($description)
                ->setPrice((float)$price);

            $this->entityManager->persist($product);
            $productsImported++;
        }

        fclose($handle);

        $this->entityManager->flush();

        $io->success("$productsImported produits importés avec succès.");

        if (count($errors) > 0) {
            $io->section("Erreurs rencontrées :");
            foreach ($errors as $error) {
                $io->error($error);
            }
        }

        return Command::SUCCESS;
    }
}
