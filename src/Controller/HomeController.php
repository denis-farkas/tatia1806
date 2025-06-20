<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SponsorRepository;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(SponsorRepository $sponsorRepository): Response
    {

        $sponsors = $sponsorRepository->findBy([], ['id' => 'ASC'], 3);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'sponsors' => $sponsors,
        ]);
    }
}
