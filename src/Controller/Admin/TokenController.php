<?php

namespace App\Controller\Admin;

use App\Entity\AccessToken;
use App\Form\AccessTokenType;
use App\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/token')]
class TokenController extends AbstractController
{
    #[Route('/', name: 'app_admin_token_index', methods: ['GET'])]
    public function index(AccessTokenRepository $accessTokenRepository): Response
    {
        return $this->render('admin/token/index.html.twig', [
            'access_tokens' => $accessTokenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_token_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $accessToken = new AccessToken();
        $form = $this->createForm(AccessTokenType::class, $accessToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($accessToken);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/token/new.html.twig', [
            'access_token' => $accessToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_token_show', methods: ['GET'])]
    public function show(AccessToken $accessToken): Response
    {
        return $this->render('admin/token/show.html.twig', [
            'access_token' => $accessToken,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_token_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AccessToken $accessToken, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccessTokenType::class, $accessToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/token/edit.html.twig', [
            'access_token' => $accessToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_token_delete', methods: ['POST'])]
    public function delete(Request $request, AccessToken $accessToken, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$accessToken->getId(), $request->request->get('_token'))) {
            $entityManager->remove($accessToken);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_token_index', [], Response::HTTP_SEE_OTHER);
    }
}
