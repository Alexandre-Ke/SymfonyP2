<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        if (!$this->isGranted('ROLE_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page.');
        }

        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client ajouté avec succès.');

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('CLIENT_EDIT', $client);

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Client mis à jour avec succès.');

            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form,
            'client' => $client,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('CLIENT_EDIT', $client);

        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client supprimé avec succès.');
        }

        return $this->redirectToRoute('app_client_index');
    }
}
