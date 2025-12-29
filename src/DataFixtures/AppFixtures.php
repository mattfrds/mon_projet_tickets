<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Créer les catégories (Suppression de setCreatedAt)
        $categories = ['Incident', 'Panne', 'Évolution', 'Anomalie', 'Information'];
        $categoryObjects = [];
        
        foreach ($categories as $catName) {
            $category = new Category();
            $category->setName($catName);
            // La ligne problématique a été retirée ici
            $manager->persist($category);
            $categoryObjects[] = $category;
        }

        // 2. Créer les statuts (Suppression de setCreatedAt)
        $statuses = ['Nouveau', 'Ouvert', 'Résolu', 'Fermé'];
        $statusObjects = [];
        
        foreach ($statuses as $statusName) {
            $status = new Status();
            $status->setName($statusName);
            // La ligne problématique a été retirée ici
            $manager->persist($status);
            $statusObjects[] = $status;
        }

        // 3. Créer l'administrateur
        $admin = new User();
        $admin->setEmail('admin@agence.fr');
        $admin->setFirstName('Admin');
        $admin->setLastName('Principal');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin2024!'));
        $manager->persist($admin);

        // 4. Créer des utilisateurs staff
        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@agence.fr");
            $user->setFirstName("Employé");
            $user->setLastName("N°{$i}");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'User2024!'));
            $manager->persist($user);
            $users[] = $user;
        }

        // 5. Créer des tickets de démonstration
        $clientEmails = ['client1@example.com', 'client2@example.com', 'client3@example.com'];
        $descriptions = [
            'Le site web ne se charge pas correctement sur mobile.',
            'Demande d\'ajout d\'une fonctionnalité de recherche.',
            'Erreur 500 sur le formulaire de contact.',
            'Les couleurs ne correspondent pas à la charte.',
            'Temps de chargement trop long.'
        ];

        for ($i = 0; $i < 10; $i++) {
            $ticket = new Ticket();
            $ticket->setAuthorEmail($clientEmails[array_rand($clientEmails)]);
            $ticket->setDescription($descriptions[array_rand($descriptions)]);
            $ticket->setCategory($categoryObjects[array_rand($categoryObjects)]);
            $ticket->setStatus($statusObjects[0]); // Nouveau
            $ticket->setOpenedAt(new \DateTimeImmutable('-' . rand(1, 30) . ' days'));
            
            if (rand(0, 1)) {
                $ticket->setResponsible($users[array_rand($users)]);
            }
            
            if (rand(0, 3) === 0) {
                $ticket->setStatus($statusObjects[3]); // Fermé
                $ticket->setClosedAt(new \DateTimeImmutable('-' . rand(1, 10) . ' days'));
            }
            
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}