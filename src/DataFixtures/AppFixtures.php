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
        // Créer les catégories
        $categories = ['Incident', 'Panne', 'Évolution', 'Anomalie', 'Information'];
        $categoryObjects = [];
        
        foreach ($categories as $catName) {
            $category = new Category();
            $category->setName($catName);
            $category->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($category);
            $categoryObjects[] = $category;
        }

        // Créer les statuts
        $statuses = ['Nouveau', 'Ouvert', 'Résolu', 'Fermé'];
        $statusObjects = [];
        
        foreach ($statuses as $statusName) {
            $status = new Status();
            $status->setName($statusName);
            $status->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($status);
            $statusObjects[] = $status;
        }

        // Créer l'administrateur
        $admin = new User();
        $admin->setEmail('admin@agence.fr');
        $admin->setFirstName('Admin');
        $admin->setLastName('Principal');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin2024!'));
        $manager->persist($admin);

        // Créer des utilisateurs du personnel
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

        // Créer des tickets de démonstration
        $clientEmails = [
            'client1@example.com',
            'client2@example.com',
            'client3@example.com'
        ];

        $descriptions = [
            'Le site web ne se charge pas correctement sur mobile. Les images sont déformées et le menu ne fonctionne pas.',
            'Demande d\'ajout d\'une fonctionnalité de recherche avancée avec filtres multiples pour améliorer l\'expérience utilisateur.',
            'Le formulaire de contact renvoie une erreur 500 lorsqu\'on essaie de l\'envoyer. Urgent car c\'est notre principal canal.',
            'Les couleurs du thème ne correspondent pas à notre charte graphique. Il faudrait ajuster les teintes de bleu.',
            'Le temps de chargement des pages est très long (plus de 10 secondes). Peut-on optimiser les performances ?'
        ];

        for ($i = 0; $i < 10; $i++) {
            $ticket = new Ticket();
            $ticket->setAuthorEmail($clientEmails[array_rand($clientEmails)]);
            $ticket->setDescription($descriptions[array_rand($descriptions)]);
            $ticket->setCategory($categoryObjects[array_rand($categoryObjects)]);
            $ticket->setStatus($statusObjects[0]); // Nouveau
            $ticket->setOpenedAt(new \DateTimeImmutable('-' . rand(1, 30) . ' days'));
            
            // Assigner un responsable aléatoirement
            if (rand(0, 1)) {
                $ticket->setResponsible($users[array_rand($users)]);
            }
            
            // Clôturer certains tickets
            if (rand(0, 3) === 0) {
                $ticket->setStatus($statusObjects[3]); // Fermé
                $ticket->setClosedAt(new \DateTimeImmutable('-' . rand(1, 10) . ' days'));
            }
            
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}