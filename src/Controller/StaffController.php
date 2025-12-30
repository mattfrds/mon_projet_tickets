<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\StaffTicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/staff')]
#[IsGranted('ROLE_USER')]
class StaffController extends AbstractController
{
    #[Route('', name: 'staff_dashboard')] 
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy([], ['openedAt' => 'DESC']);
        
        return $this->render('staff/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/{id}', name: 'staff_ticket_show')]
    public function showTicket(Ticket $ticket): Response
    {
        return $this->render('staff/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/{id}/edit-status', name: 'staff_ticket_edit_status')]
    public function editTicketStatus(
        Request $request, 
        Ticket $ticket, 
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(StaffTicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le statut est "Fermé", définir la date de clôture
            if ($ticket->getStatus() && $ticket->getStatus()->getName() === 'Fermé') {
                if (!$ticket->getClosedAt()) {
                    $ticket->setClosedAt(new \DateTimeImmutable());
                }
            }
            
            $em->flush();

            $this->addFlash('success', 'Le statut du ticket a été modifié avec succès.');
            return $this->redirectToRoute('staff_dashboard');
        }

        return $this->render('staff/edit_status.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }
}