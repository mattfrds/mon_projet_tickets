<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\StatusType;
use App\Form\TicketType;
use App\Form\UserType;
use App\Repository\CategoryRepository;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy([], ['openedAt' => 'DESC']);
        
        return $this->render('admin/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    // ============ GESTION DES TICKETS ============
    
    #[Route('/tickets', name: 'admin_tickets')]
    public function tickets(TicketRepository $ticketRepository): Response
    {
        $tickets = $ticketRepository->findBy([], ['openedAt' => 'DESC']);
        
        return $this->render('admin/tickets/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/new', name: 'admin_ticket_new')]
    public function newTicket(Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();
        $ticket->setOpenedAt(new \DateTimeImmutable());
        
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Le ticket a été créé avec succès.');
            return $this->redirectToRoute('admin_tickets');
        }

        return $this->render('admin/tickets/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ticket/{id}', name: 'admin_ticket_show')]
    public function showTicket(Ticket $ticket): Response
    {
        return $this->render('admin/tickets/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/ticket/{id}/edit', name: 'admin_ticket_edit')]
    public function editTicket(Request $request, Ticket $ticket, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le ticket a été modifié avec succès.');
            return $this->redirectToRoute('admin_tickets');
        }

        return $this->render('admin/tickets/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/ticket/{id}/delete', name: 'admin_ticket_delete', methods: ['POST'])]
    public function deleteTicket(Request $request, Ticket $ticket, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
            $em->remove($ticket);
            $em->flush();
            
            $this->addFlash('success', 'Le ticket a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_tickets');
    }

    // ============ GESTION DES CATÉGORIES ============
    
    #[Route('/categories', name: 'admin_categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['name' => 'ASC']);
        
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/new', name: 'admin_category_new')]
    public function newCategory(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        //$category->setCreatedAt(new \DateTimeImmutable());
        
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}/edit', name: 'admin_category_edit')]
    public function editCategory(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/category/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function deleteCategory(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $em->remove($category);
            $em->flush();
            
            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_categories');
    }

    // ============ GESTION DES STATUTS ============
    
    #[Route('/statuses', name: 'admin_statuses')]
    public function statuses(StatusRepository $statusRepository): Response
    {
        $statuses = $statusRepository->findBy([], ['name' => 'ASC']);
        
        return $this->render('admin/statuses/index.html.twig', [
            'statuses' => $statuses,
        ]);
    }

    #[Route('/status/new', name: 'admin_status_new')]
    public function newStatus(Request $request, EntityManagerInterface $em): Response
    {
        $status = new Status();
       // $status->setCreatedAt(new \DateTimeImmutable());
        
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($status);
            $em->flush();

            $this->addFlash('success', 'Le statut a été créé avec succès.');
            return $this->redirectToRoute('admin_statuses');
        }

        return $this->render('admin/statuses/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/status/{id}/edit', name: 'admin_status_edit')]
    public function editStatus(Request $request, Status $status, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Le statut a été modifié avec succès.');
            return $this->redirectToRoute('admin_statuses');
        }

        return $this->render('admin/statuses/edit.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route('/status/{id}/delete', name: 'admin_status_delete', methods: ['POST'])]
    public function deleteStatus(Request $request, Status $status, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$status->getId(), $request->request->get('_token'))) {
            $em->remove($status);
            $em->flush();
            
            $this->addFlash('success', 'Le statut a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_statuses');
    }

    // ============ GESTION DES UTILISATEURS ============
    
    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['email' => 'ASC']);
        
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/new', name: 'admin_user_new')]
    public function newUser(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User();
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été créé avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'admin_user_edit')]
    public function editUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Ne pas permettre de supprimer son propre compte
            if ($user === $this->getUser()) {
                $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
                return $this->redirectToRoute('admin_users');
            }
            
            $em->remove($user);
            $em->flush();
            
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_users');
    }
}