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
    public function index(CoursRepository $coursRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cours = $coursRepository->findAll();

        // Regrouper les cours par jour
        $coursParJour = [];
        $coursParType = [];

        foreach ($coursRepository->findAll() as $cours) {
            $type = $cours->getName(); // exemple : "Eveil", "Initiation", "Classique"
            $coursParType[$type][] = $cours;
            $coursParJour[$cours->getDay()][] = $cours;
        }

        ksort($coursParType); // trie les types alphabétiquement

        // Créneaux horaires à afficher dans le tableau
        $horaires = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'];

        return $this->render('cours/index.html.twig', [
            'cours' => $cours,
            'coursParJour' => $coursParJour,
            'horaires' => $horaires,
            'coursParType' => $coursParType,
        ]);
    }

    #[Route('/cours/{id}', name: 'app_cours_by_id')]
    public function ficheCours($id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cours = $entityManager->getRepository(Cours::class)->find($id);

        if (!$cours) {
            throw $this->createNotFoundException('Cours non trouvé');
        }

        return $this->render('cours/fiche_cours.html.twig', [
            'cours' => $cours,
        ]);
    }

    
}
