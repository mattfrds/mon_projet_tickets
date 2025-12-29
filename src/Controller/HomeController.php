<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\PublicTicketType;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request, 
        EntityManagerInterface $em,
        StatusRepository $statusRepository
    ): Response {
        $ticket = new Ticket();
        $ticket->setOpenedAt(new \DateTimeImmutable());
        
        // Définir automatiquement le statut "Nouveau"
        $defaultStatus = $statusRepository->findOneBy(['name' => 'Nouveau']);
        if ($defaultStatus) {
            $ticket->setStatus($defaultStatus);
        }
        
        $form = $this->createForm(PublicTicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Votre ticket a été créé avec succès ! Nous vous contacterons bientôt.');
            
            // Rediriger pour éviter la resoumission du formulaire
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }
}