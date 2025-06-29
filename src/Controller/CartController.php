<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\ProductVariant;
use App\Entity\Cours;
use App\Entity\Child;
use App\Entity\GalaImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/add-to-cart', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        $variantId = $request->request->get('variant');
        $quantity = (int) $request->request->get('quantity', 1);

        if (!$variantId) {
            $this->addFlash('error', 'Veuillez sélectionner une variante.');
            return $this->redirectToRoute('app_product_by_id', ['id' => $request->get('id')]);
        }

        $variant = $entityManager->getRepository(ProductVariant::class)->find($variantId);

        if (!$variant) {
            $this->addFlash('error', 'La variante sélectionnée est introuvable.');
            return $this->redirectToRoute('app_product_by_id', ['id' => $request->get('id')]);
        }

        // Check stock availability
        if (!$variant->isInStock() || $variant->getAvailableQuantity() < $quantity) {
            $this->addFlash('error', sprintf(
                'Stock insuffisant pour %s. Disponible : %d',
                $variant->getProduct()->getName(),
                $variant->getAvailableQuantity()
            ));
            return $this->redirectToRoute('app_product_by_id', ['id' => $variant->getProduct()->getId()]);
        }

        // Add the variant to the cart
        $cart->addVariant($variant, $quantity);

        $this->addFlash('success', 'Le produit a été ajouté au panier.');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart', name: 'cart')]
    public function index(Cart $cart): Response
    {
        // Retrieve the cart items
        $cartItems = $cart->getCart();
        $imagesCart = $cart->getImagesCart(); // Retrieve images from the cart
        $coursCart = $cart->getCoursCart(); // Retrieve cours from the cart
        $totals = $cart->getTotals();
        $totalQuantity = $cart->getTotalQuantity();

        return $this->render('cart/cart.html.twig', [
            'cart' => $cartItems,
            'images_cart' => $imagesCart, // Pass images_cart to the template
            'cours_cart' => $coursCart, // Pass cours_cart to the template
            'totals' => $totals,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart($id, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        $variant = $entityManager->getRepository(ProductVariant::class)->find($id);

        if (!$variant) {
            throw $this->createNotFoundException('Variant not found.');
        }

        // Pass the ID of the variant to the removeVariant method
        $cart->removeVariant($variant->getId());

        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/add-cours/{id}', name: 'add_cours_to_cart', methods: ['POST'])]
    public function addCoursToCart(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        $cours = $entityManager->getRepository(Cours::class)->find($id);
        if (!$cours) {
            $this->addFlash('danger', 'Cours introuvable.');
            return $this->redirectToRoute('app_cours');
        }

        $childId = $request->request->get('child');

        // Determine who is being registered: parent or child
        $child = null;
        $childFirstname = null;
        $childLastname = null;
        if ($childId === 'parent') {
            $childFirstname = $user->getFirstname();
            $childLastname = $user->getLastname();
        } else {
            $child = $entityManager->getRepository(Child::class)->find($childId);
            if (!$child || $child->getParent() !== $user) {
                $this->addFlash('danger', 'Enfant non valide.');
                return $this->redirectToRoute('app_cours');
            }
            $childFirstname = $child->getFirstname();
            $childLastname = $child->getLastname();
        }

        // Add the cours to the cart (quantity always 1, with child info)
        $cart->addCours($cours, $childFirstname, $childLastname);

        $this->addFlash('success', sprintf(
            'Le cours "%s" a été ajouté au panier pour %s %s.',
            $cours->getName(),
            $childFirstname,
            $childLastname
        ));

        // Redirect to the page the user came from, or to the cart
        $redirectTo = $request->get('redirect_to') ?: $this->generateUrl('cart');
        return $this->redirect($redirectTo);
    }

    #[Route('/cart/remove-cours/{id}/{child}', name: 'remove_cours_from_cart', methods: ['POST'])]
    public function removeCoursFromCart(
        int $id,
        string $child,
        Request $request,
        Cart $cart
    ): Response {
        // $child is expected as "firstname-lastname"
        [$childFirstname, $childLastname] = explode('-', $child, 2);

        $cartItems = $request->getSession()->get('cours_cart', []);
        $newCart = [];

        foreach ($cartItems as $item) {
            if (
                $item['cours_id'] == $id &&
                $item['child_firstname'] === $childFirstname &&
                $item['child_lastname'] === $childLastname
            ) {
                // Skip this item (remove)
                continue;
            }
            $newCart[] = $item;
        }

        $request->getSession()->set('cours_cart', $newCart);

        $this->addFlash('success', 'Le cours a été retiré du panier.');
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/add-image/{id}', name: 'add_image_to_cart', methods: ['POST'])]
    public function addImageToCart(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response {
        $image = $entityManager->getRepository(GalaImage::class)->find($id);

        if (!$image) {
            $this->addFlash('danger', 'Image introuvable.');
            return $this->redirectToRoute('app_gala');
        }

        $quantity = (int) $request->request->get('quantity', 1);

        // Add the image to the images cart
        $cart->addImage($image, $quantity);

        $this->addFlash('success', sprintf(
            'L\'image "%s" a été ajoutée au panier.',
            $image->getFilename()
        ));

        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove-image/{id}', name: 'remove_image_from_cart', methods: ['POST'])]
    public function removeImageFromCart(
        int $id,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response {
        $image = $entityManager->getRepository(GalaImage::class)->find($id);

        if (!$image) {
            $this->addFlash('danger', 'Image introuvable.');
            return $this->redirectToRoute('cart');
        }

        // Remove the image from the images cart
        $cart->removeImage($image);

        $this->addFlash('success', sprintf(
            'L\'image "%s" a été retirée du panier.',
            $image->getFilename()
        ));

        return $this->redirectToRoute('cart');
    }
}