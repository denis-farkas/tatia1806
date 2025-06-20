<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OutfitController extends AbstractController
{
    #[Route('/outfit', name: 'app_outfit')]
    public function index(): Response
    {
        return $this->render('cours/outfit.html.twig', [
            'outfits' => 'OutfitController',
        ]);
    }
}
