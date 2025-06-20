<?php


namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use App\Entity\Cours;
use App\Entity\Child;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    
    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart($id, Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        $quantity = (int)$request->request->get('quantity', 1);

        

        // Vérifier la disponibilité du stock
        if (!$product->isInStock() || $product->getAvailableQuantity() < $quantity) {
            

            $this->addFlash('error', sprintf(
                'Stock insuffisant pour %s. Disponible : %d',
                $product->getName(),
                $product->getAvailableQuantity()
            ));
            return $this->redirectToRoute('app_product_by_id', ['id' => $id]);
        }

        $cart->addProduct($product, $quantity);
        
       

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('app_product');
    }

    #[Route('/cart', name: 'cart')]
    public function index(Cart $cart): Response
    {
        // Récupérer le panier
        $cartItems = $cart->getCart();
        $totals = $cart->getTotals();
        $totalQuantity = $cart->getTotalQuantity();

        

        return $this->render('cart/cart.html.twig', [
            'cart' => $cartItems,
            'totals' => $totals,
            'totalQuantity' => $totalQuantity
        ]);
    }

    #[Route('/decrease-quantity/{id}', name: 'decrease_quantity')]
    public function decreaseQuantity($id, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        

        $product = $entityManager->getRepository(Product::class)->find($id);
        
        if (!$product) {
            
            throw $this->createNotFoundException('Produit non trouvé.');
        }
        
        $cart->decreaseQuantity($product);
        
        
        
        return $this->redirectToRoute('cart');
    }

    #[Route('/increase-quantity/{id}', name: 'increase_quantity')]
    public function increaseQuantity($id, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        

        $product = $entityManager->getRepository(Product::class)->find($id);
        
        if (!$product) {
           
            throw $this->createNotFoundException('Produit non trouvé.');
        }
        
        $cart->increaseQuantity($product);
        
        
        
        return $this->redirectToRoute('cart');
    }

    #[Route('/remove-from-cart/{id}', name: 'remove_from_cart')]
    public function removeFromCart($id, Cart $cart, EntityManagerInterface $entityManager): Response
    {
       

        // Récupérer le produit à partir de son ID
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        $cart->remove($product);

        

        // Ajouter un message flash de succès
        $this->addFlash('success', 'Produit retiré du panier.');

        // Rediriger vers la page panier
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/add-cours/{id}', name: 'add_cours_to_cart', methods: ['POST'])]
    public function addCoursToCart(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response 
    {
        
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
}