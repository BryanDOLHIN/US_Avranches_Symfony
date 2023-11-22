<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use Psr\Log\LoggerInterface;
use App\Repository\PlayerRepository;
use App\Service\UserVerificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/player')]
class PlayerController extends AbstractController
{
    private $userVerificationService;

    public function __construct(UserVerificationService $userVerificationService)
    {
        $this->userVerificationService = $userVerificationService;
    }
    
    #[Route('/poste/set-poste-principal/{id}', name: 'app_set_poste_principal')]
    public function setPostePrincipal(Player $player, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPostePrincipal() player->getFirstname() = ' . $player->getFirstname());

        // Récupérez les données de la requête AJAX
        $postePrincipal = $_POST["postePrincipal"];
        $logger->debug('setPostePrincipal() postePrincipal = ' . $postePrincipal);

        // Mettez à jour l'entité Player
        $player->setPostePrincipal($postePrincipal);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($player);
        $entityManager->flush();
    }

    #[Route('/poste/set-poste-secondaire/{id}', name: 'app_set_poste_secondaire')]
    public function setPosteSecondaire(Player $player, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPosteSecondaire() player->getFirstname() = ' . $player->getFirstname());

        // Récupérez les données de la requête AJAX
        $posteSecondaire = $_POST["posteSecondaire"];
        $logger->debug('setPosteSecondaire() posteSecondaire = ' . $posteSecondaire);

        // Mettez à jour l'entité Player
        $player->setPosteSecondaire($posteSecondaire);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($player);
        $entityManager->flush();
    }

    #[Route('/', name: 'app_player_index', methods: ['GET'])]
    public function index(PlayerRepository $playerRepository): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player/index.html.twig', [
            'players' => $playerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_player_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }
        
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($player);
            $entityManager->flush();

            return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player/new.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_player_show', methods: ['GET'])]
    public function show(Player $player): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('player/show.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_player_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('player/edit.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id}', name: 'app_player_delete', methods: ['POST'])]
    public function delete(Request $request, Player $player, EntityManagerInterface $entityManager): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$player->getId(), $request->request->get('_token'))) {
            $entityManager->remove($player);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_player_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/poste', name: 'app_player_poste', methods: ['GET'])]
    public function poste(Player $player, LoggerInterface $logger): Response
    {
        if(!$this->userVerificationService->verifyUser()){
            return $this->redirectToRoute('app_verif_code', [], Response::HTTP_SEE_OTHER);
        }

        // $logger->debug('poste() player->getFirstname() = ' . $player->getFirstname());
        return $this->render('player/poste.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/{id}/poste-cache', name: 'app_player_poste', methods: ['GET'])]
    public function poste_cache(Player $player, LoggerInterface $logger): Response
    {
        // $logger->debug('poste() player->getFirstname() = ' . $player->getFirstname());
        return $this->render('player/hiddenposte.html.twig', [
            'player' => $player,
        ]);
    }

    #[Route('/poste/set-poste-cache-x/{id}', name: 'app_set_poste_cache_x')]
    public function setPosteCacheX(Player $player, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPosteCacheX() player->getFirstname() = ' . $player->getFirstname());

        // Récupérez les données de la requête AJAX
        $coord = $_POST["coordX"];
        $logger->debug('setPosteCoordX() PosteCoordX = ' . $coord);

        // Mettez à jour l'entité Player
        $player->setPosteCoordX($coord);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($player);
        $entityManager->flush();
    }

    #[Route('/poste/set-poste-cache-y/{id}', name: 'app_set_poste_cache_y')]
    public function setPosteCacheY(Player $player, Request $request, LoggerInterface $logger): Response
    {
        $logger->debug('setPosteCacheY() player->getFirstname() = ' . $player->getFirstname());

        // Récupérez les données de la requête AJAX
        $coord = $_POST["coordY"];
        $logger->debug('setPosteCoordY() PosteCoordY = ' . $coord);

        // Mettez à jour l'entité Player
        $player->setPosteCordY($coord);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($player);
        $entityManager->flush();
    }

    //////////////////////////////


}
