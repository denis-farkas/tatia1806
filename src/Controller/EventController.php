<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event')]
    public function index(EventRepository $eventRepository): Response
    {
        // Trier par date croissante (le plus ancien en premier, le plus récent en dernier)
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findBy([], ['date' => 'ASC']),
        ]);
    }
}
