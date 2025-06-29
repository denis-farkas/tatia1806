<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\UserCours;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class CoursController extends AbstractController
{
    #[Route('/cours', name: 'app_cours')]
    public function index(CoursRepository $coursRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $subscriptions = [];

        if ($user) {
            // Fetch all UserCours for the logged-in user
            $userCoursRepository = $entityManager->getRepository(UserCours::class);
            $userCours = $userCoursRepository->findBy(['user' => $user]);

            // Create an array to track subscription status
            foreach ($userCours as $uc) {
                $subscriptions[$uc->getCours()->getId()] = $uc->getFirstname(); // Track who is subscribed
            }
        }

        $cours = $coursRepository->findAll();

        // Regrouper les cours par jour et par type
        $coursParJour = [];
        $coursParType = [];

        foreach ($cours as $coursItem) {
            $type = $coursItem->getName(); // exemple : "Eveil", "Initiation", "Classique"
            $coursParType[$type][] = $coursItem;
            $coursParJour[$coursItem->getDay()][] = $coursItem;
        }

        ksort($coursParType); // trie les types alphabétiquement

        // Créneaux horaires à afficher dans le tableau
        $horaires = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

        return $this->render('cours/index.html.twig', [
            'cours' => $cours,
            'coursParJour' => $coursParJour,
            'horaires' => $horaires,
            'coursParType' => $coursParType,
            'subscriptions' => $subscriptions, // Pass subscription info to the template
        ]);
    }

    #[Route('/cours/{id}', name: 'app_cours_by_id')]
    public function ficheCours($id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || !$user instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $cours = $entityManager->getRepository(Cours::class)->find($id);

        if (!$cours) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        // Fetch all UserCours for the given course
        $userCoursRepository = $entityManager->getRepository(UserCours::class);
        $userCours = $userCoursRepository->findBy(['cours' => $cours]);

        // Check if the user or their children are already subscribed
        $isSubscribed = false;
        $subscriptionMessage = null;
        $subscribedChildren = [];
        $ineligibleChildren = []; // Track children who don't meet age requirements

        foreach ($userCours as $uc) {
            if ($uc->getUser() === $user) {
                $isSubscribed = true;
                $subscriptionMessage = 'Vous êtes déjà inscrit(e) à ce cours.';
            }

            foreach ($user->getChildren() as $child) {
                if ($uc->getFirstname() === $child->getFirstname()) {
                    $subscribedChildren[$child->getId()] = $child->getFirstname();
                }
            }
        }

        // Check age eligibility for each child
        foreach ($user->getChildren() as $child) {
            $childAge = $child->getBirthdate()->diff(new \DateTime())->y; // Calculate age in years


            if (($cours->getMinAge() !== null && $childAge < $cours->getMinAge()) ||
                ($cours->getMaxAge() !== null && $childAge > $cours->getMaxAge())) {
                $ineligibleChildren[$child->getId()] = $child->getFirstname();
            }
        }

        
        $userAge =  18; // Default age for the user, assuming they are an adult

        // Check if the user (adult) is eligible for the course
        if ($cours->getMaxAge() !== null && $userAge > $cours->getMaxAge()) {
            // Allow the user to proceed if they are registering a child
            if (empty($user->getChildren())) {
                $this->addFlash('danger', 'Ce cours est réservé aux enfants.');
                return $this->redirectToRoute('app_cours');
            }
        }

        return $this->render('cours/fiche_cours.html.twig', [
            'cours' => $cours,
            'isSubscribed' => $isSubscribed,
            'subscriptionMessage' => $subscriptionMessage,
            'subscribedChildren' => $subscribedChildren,
            'ineligibleChildren' => $ineligibleChildren, // Pass ineligible children info
        ]);
    }
}
