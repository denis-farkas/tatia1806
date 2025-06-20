<?php

namespace App\Controller;

use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }

    #[Route('/load-events', name: 'fc_load_events')]
    public function loadEvents(CoursRepository $coursRepository): JsonResponse
    {
        $coursList = $coursRepository->findAll();
        $events = [];
        $startDate = new \DateTimeImmutable('15 September this year');
        $endDate = (clone $startDate)->modify('+4 months'); // Example: generate for 4 months

        $daysOfWeek = [
            'Lundi' => ['num' => 1, 'en' => 'Monday'],
            'Mardi' => ['num' => 2, 'en' => 'Tuesday'],
            'Mercredi' => ['num' => 3, 'en' => 'Wednesday'],
            'Jeudi' => ['num' => 4, 'en' => 'Thursday'],
            'Vendredi' => ['num' => 5, 'en' => 'Friday'],
            'Samedi' => ['num' => 6, 'en' => 'Saturday'],
            'Dimanche' => ['num' => 7, 'en' => 'Sunday'],
        ];

        foreach ($coursList as $cours) {
            $dayInfo = $daysOfWeek[$cours->getDay()] ?? null;
            if (!$dayInfo) {
                continue;
            }

            $current = clone $startDate;
            if ((int)$current->format('N') !== $dayInfo['num']) {
                $current = $current->modify('next ' . $dayInfo['en']);
            }

            while ($current <= $endDate) {
                $start = $current->setTime(
                    (int)$cours->getStartHour()->format('H'),
                    (int)$cours->getStartHour()->format('i')
                );
                $end = $current->setTime(
                    (int)$cours->getEndHour()->format('H'),
                    (int)$cours->getEndHour()->format('i')
                );

                $events[] = [
                    'title' => $cours->getName() . ' (' . $cours->getAge() . ')',
                    'start' => $start->format('Y-m-d\TH:i:s'),
                    'end' => $end->format('Y-m-d\TH:i:s'),
                    'description' => $cours->getDescription(),
                    'salle' => $cours->getSalle(),
                ];

                $current = $current->modify('+1 week');
            }
        }

        return $this->json($events);
    }
}
