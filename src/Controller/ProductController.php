<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/products', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        // Serialize products and their variants
        $serializedProducts = [];
        foreach ($products as $product) {
            $variants = [];
            foreach ($product->getVariants() as $variant) {
                $variants[] = [
                    'id' => $variant->getId(),
                    'attributes' => array_map(function ($attribute) {
                        return [
                            'name' => $attribute->getAttribute()->getName(),
                            'value' => $attribute->getValue(),
                        ];
                    }, $variant->getAttributes()->toArray()),
                    'quantity' => $variant->getQuantity(),
                    'availableQuantity' => $variant->getAvailableQuantity(),
                    'isActive' => $variant->getIsActive(),
                ];
            }

            $serializedProducts[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => number_format($product->getPrice(), 2, '.', ''), // Use product price
                'image1' => $product->getImage1(),
                'createdAt' => $product->getCreatedAt()->format('Y-m-d H:i:s'),
                'variants' => $variants, // Include variants
            ];
        }

        return $this->render('product/index.html.twig', [
            'products' => $serializedProducts,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_by_id')]
    public function ficheProduit($id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        // Serialize product variants
        $variants = [];
        foreach ($product->getVariants() as $variant) {
            $variants[] = [
                'id' => $variant->getId(),
                'attributes' => array_map(function ($attribute) {
                    return [
                        'name' => $attribute->getAttribute()->getName(),
                        'value' => $attribute->getValue(),
                    ];
                }, $variant->getAttributes()->toArray()),
                'quantity' => $variant->getQuantity(),
                'availableQuantity' => $variant->getAvailableQuantity(),
                'isActive' => $variant->getIsActive(),
            ];
        }

        return $this->render('product/fiche_produit.html.twig', [
            'product' => $product,
            'variants' => $variants, // Pass variants to the template
        ]);
    }
}