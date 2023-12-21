<?php

namespace App\Controller;

use App\Entity\Palier;
use App\Form\PalierType;
use App\Repository\PalierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/palier')]
class PalierController extends AbstractController
{
    #[Route('/', name: 'app_palier_index', methods: ['GET', 'POST'])]
    public function index(PalierRepository $palierRepository): Response
    {
        return $this->render('palier/index.html.twig', [
            'paliers' => $palierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_palier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le numéro passé en paramètre
        $numero = $request->query->getInt('numero', 0);

        // Vérifier si le numéro existe déjà
        $existingPalier = $entityManager->getRepository(Palier::class)->findOneBy(['numero' => $numero]);

        if ($existingPalier) {
            // Numéro existant, rediriger vers la page de création avec un nouveau numéro
            return $this->redirectToRoute('app_palier_new', ['numero' => $numero + 1]);
        }

        // Le numéro est unique, procéder à la création du palier
        $palier = new Palier();
        $palier->setNumero($numero);

        $form = $this->createForm(PalierType::class, $palier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($palier);
            $entityManager->flush();

            return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('palier/new.html.twig', [
            'palier' => $palier,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_palier_show', methods: ['GET', 'POST'])]
    public function show(Palier $palier): Response
    {
        return $this->render('palier/show.html.twig', [
            'palier' => $palier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_palier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Palier $palier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PalierType::class, $palier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_palier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('palier/edit.html.twig', [
            'palier' => $palier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_palier_delete', methods: ['POST'])]
    public function delete(Request $request, Palier $palier, EntityManagerInterface $entityManager): JsonResponse
    {
        // Supprimer le palier de la base de données
        $entityManager->remove($palier);
        $entityManager->flush();

        // Vous pouvez renvoyer une réponse JSON avec succès
        return new JsonResponse(['success' => true]);
    }

}