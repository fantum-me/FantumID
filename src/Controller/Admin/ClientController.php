<?php

namespace App\Controller\Admin;

use App\Entity\OAuthClient;
use App\Form\OAuthClientType;
use App\Repository\OAuthClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(OAuthClientRepository $oAuthClientRepository): Response
    {
        return $this->render('admin/client/index.html.twig', [
            'o_auth_clients' => $oAuthClientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $oAuthClient = new OAuthClient();
        $form = $this->createForm(OAuthClientType::class, $oAuthClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($oAuthClient);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/client/new.html.twig', [
            'o_auth_client' => $oAuthClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(OAuthClient $oAuthClient): Response
    {
        return $this->render('admin/client/show.html.twig', [
            'o_auth_client' => $oAuthClient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OAuthClient $oAuthClient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OAuthClientType::class, $oAuthClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/client/edit.html.twig', [
            'o_auth_client' => $oAuthClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, OAuthClient $oAuthClient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oAuthClient->getId(), $request->request->get('_token'))) {
            $entityManager->remove($oAuthClient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
