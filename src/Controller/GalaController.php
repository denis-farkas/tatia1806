<?php

namespace App\Controller;

use App\Entity\Gala;
use App\Repository\GalaRepository;
use App\Repository\GalaImageRepository;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class GalaController extends AbstractController
{
    #[Route('/gala', name: 'app_gala')]
    public function index(GalaRepository $galaRepository, GalaImageRepository $galaImageRepository): Response
    {
        // Fetch all galas from the database
        $galas = $galaRepository->findAll();

        // Fetch one random image for each gala
        $galaImages = [];
        foreach ($galas as $gala) {
            $galaImages[$gala->getId()] = $galaImageRepository->findOneBy(['galaname' => $gala->getName()]);
        }

        return $this->render('gala/index.html.twig', [
            'galas' => $galas,
            'galaImages' => $galaImages,
        ]);
    }

    #[Route('/gala/{id}', name: 'app_gala_gallery')]
    public function galaGallery(Gala $gala, GalaImageRepository $galaImageRepository): Response
    {
        // Fetch the first image and course info for each cours in the gala
        $coursImages = $galaImageRepository->findFirstImagesByGala($gala->getName());

        return $this->render('gala/gallery.html.twig', [
            'gala' => $gala,
            'coursImages' => $coursImages,
        ]);
    }

    #[Route('/gala/{id}/cours/{cours}', name: 'app_gala_cours_gallery')]
    public function coursGallery(
        Gala $gala,
        int $cours, // Use the cours ID from the route
        GalaImageRepository $galaImageRepository,
        CoursRepository $coursRepository,
        Request $request
    ): Response {
        // Fetch the Cours entity using the cours ID
        $coursEntity = $coursRepository->find($cours);

        if (!$coursEntity) {
            throw $this->createNotFoundException('Cours not found.');
        }

        // Pagination setup
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10; // Number of images per page
        $offset = ($page - 1) * $limit;

        // Fetch images for the specific cours in the gala
        $images = $galaImageRepository->findBy(
            ['galaname' => $gala->getName(), 'cours' => $cours],
            null,
            $limit,
            $offset
        );

        // Count total images for pagination
        $totalImages = $galaImageRepository->count(['galaname' => $gala->getName(), 'cours' => $cours]);
        $totalPages = ceil($totalImages / $limit);

        return $this->render('gala/cours_gallery.html.twig', [
            'gala' => $gala,
            'cours' => $coursEntity, // Pass the Cours entity to the template
            'images' => $images,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/gala/image/{id}', name: 'app_gala_image_fiche')]
    public function imageFiche(
        int $id,
        GalaImageRepository $galaImageRepository,
        CoursRepository $coursRepository,
        GalaRepository $galaRepository
    ): Response {
        // Fetch the image by its ID
        $image = $galaImageRepository->find($id);

        if (!$image) {
            throw $this->createNotFoundException('Image not found.');
        }

        // Fetch the related Cours entity using the cours ID from the image
        $cours = $coursRepository->find($image->getCours());

        if (!$cours) {
            throw $this->createNotFoundException('Cours not found.');
        }

        // Fetch the related Gala entity using the galaname from the image
        $gala = $galaRepository->findOneBy(['name' => $image->getGalaname()]);

        if (!$gala) {
            throw $this->createNotFoundException('Gala not found.');
        }

        return $this->render('gala/image_fiche.html.twig', [
            'image' => $image,
            'cours' => $cours,
            'gala' => $gala,
        ]);
    }

    
}
