<?php

namespace App\Controller;

use App\Entity\User;
use App\Classe\Cart;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\StockManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    public function __construct(private StockManager $stockManager)
    {
    }

    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, Request $request, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $order = $orderRepository->findOneBy([
            'id' => $id_order,
            'user' => $this->getUser()
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        // Retrieve the shipping cost from the request
        $shippingCost = $request->query->get('shipping', 0);

        // Verify stock availability
        $unavailableProducts = [];
        foreach ($order->getOrderDetails() as $orderDetail) {
            if ($orderDetail->getProductIllustration() === 'cours_image.jpg') {
                continue; // Skip stock check for courses
            }

            if ($orderDetail->getProductIllustration() !== $orderDetail->getProductName()) {
                $variant = $entityManager->getRepository(\App\Entity\ProductVariant::class)
                    ->find($orderDetail->getProductId());
                if ($variant && $variant->getAvailableQuantity() < $orderDetail->getProductQuantity()) {
                    $unavailableProducts[] = [
                        'name' => $variant->getProduct()->getName(),
                        'requested' => $orderDetail->getProductQuantity(),
                        'available' => $variant->getAvailableQuantity()
                    ];
                }
            }
        }

        if (!empty($unavailableProducts)) {
            foreach ($unavailableProducts as $unavailable) {
                $this->addFlash('error', sprintf(
                    'Stock insuffisant pour %s : %d demandé(s), %d disponible(s)',
                    $unavailable['name'],
                    $unavailable['requested'],
                    $unavailable['available']
                ));
            }
            return $this->redirectToRoute('cart');
        }

        // Reserve stock
        $this->reserveStockForOrder($order, $entityManager);

        $products_for_stripe = [];

        foreach ($order->getOrderDetails() as $orderDetail) {
            $isCours = $orderDetail->getProductIllustration() === 'cours_image.jpg';

            // Format options as a string
            $optionsString = '';
            if (!empty($orderDetail->getOptions())) {
                $optionsString = ' (' . implode(', ', array_map(
                    fn($option) => ucfirst($option['name']) . ': ' . $option['value'],
                    $orderDetail->getOptions()
                )) . ')';
            }

            // Add product to Stripe line items
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($orderDetail->getProductPrice() * 100, 0, '', ''),
                    'product_data' => [
                        'name' => $orderDetail->getProductName() . $optionsString . ($isCours ? ' (Cours)' : ''),
                        'images' => [
                            $_ENV['DOMAIN'] . '/uploads/products/' .
                            ($isCours ? 'cours_image.jpg' : $orderDetail->getProductIllustration())
                        ],
                        'metadata' => [
                            'requires_shipping' => $isCours ? 'false' : 'true',
                        ],
                    ],
                ],
                'quantity' => $orderDetail->getProductQuantity(),
            ];
        }

        // Add shipping cost as a separate line item
        if ($shippingCost > 0) {
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => number_format($shippingCost * 100, 0, '', ''),
                    'product_data' => [
                        'name' => 'Frais de livraison',
                    ],
                ],
                'quantity' => 1,
            ];
        }

        /** @var User $user */
        $user = $this->getUser();

        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(),
            'line_items' => $products_for_stripe,
            'mode' => 'payment',
            'success_url' => $_ENV['DOMAIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['DOMAIN'] . '/commande/annulation/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success(
        $stripe_session_id,
        OrderRepository $orderRepository,
        EntityManagerInterface $entityManager,
        Cart $cart
    ): Response {
        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\User) {
            $this->addFlash('danger', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }
        $order = $orderRepository->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user' => $user
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        if ($order->getState() == 1) {
            // Confirm stock for physical products
            $this->confirmStockForOrder($order, $entityManager);

            // Persist courses in the user_cours table
            foreach ($order->getOrderDetails() as $orderDetail) {
                if ($orderDetail->getProductIllustration() === 'cours_image.jpg') {
                    // Find the course entity
                    $cours = $entityManager->getRepository(\App\Entity\Cours::class)
                        ->find($orderDetail->getProductId());

                    if ($cours) {
                        // Create a new UserCours entity
                        $userCours = new \App\Entity\UserCours();
                        $userCours->setUser($user);
                        $userCours->setCours($cours);
                        $userCours->setFirstname($user->getFirstname()); // Or another field if needed
                        $userCours->setCreatedAt(new \DateTimeImmutable());

                        // Persist the UserCours entity
                        $entityManager->persist($userCours);
                    }
                }
            }

            // Update the order state to "Paid"
            $order->setState(2);
            $cart->reset();
            $entityManager->flush();
        }

        return $this->render('payment/success.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/commande/annulation/{stripe_session_id}', name: 'app_payment_cancel')]
    public function cancel($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user) {
            $order = $orderRepository->findOneBy([
                'stripe_session_id' => $stripe_session_id,
                'user' => $user
            ]);

            if ($order && $order->getState() == 1) {
                // Paiement annulé : libérer le stock réservé
                $this->releaseReservedStockForOrder($order, $entityManager);
            }
        }

        $this->addFlash('error', 'Votre paiement a été annulé.');
        
        return $this->render('payment/cancel.html.twig', [
            'message' => 'Le paiement a été annulé',
        ]);
    }

    private function reserveStockForOrder(Order $order, EntityManagerInterface $entityManager): void
    {
        foreach ($order->getOrderDetails() as $orderDetail) {
            if ($orderDetail->getProductIllustration() === 'cours_image.jpg') {
                // Skip stock reservation for cours
                continue;
            }

            if ($orderDetail->getProductIllustration() !== $orderDetail->getProductName()) {
                // Handle ProductVariant
                $variant = $entityManager->getRepository(\App\Entity\ProductVariant::class)
                    ->find($orderDetail->getProductId());
                if ($variant) {
                    $this->stockManager->reserveStock($variant, $orderDetail->getProductQuantity());
                }
            }
        }
    }

    private function confirmStockForOrder(Order $order, EntityManagerInterface $entityManager): void
    {
        foreach ($order->getOrderDetails() as $orderDetail) {
            if ($orderDetail->getProductIllustration() === 'cours_image.jpg') {
                // Skip stock confirmation for cours
                continue;
            }

            if ($orderDetail->getProductIllustration() !== $orderDetail->getProductName()) {
                // Handle ProductVariant
                $variant = $entityManager->getRepository(\App\Entity\ProductVariant::class)
                    ->find($orderDetail->getProductId());
                if ($variant) {
                    $this->stockManager->confirmStock($variant, $orderDetail->getProductQuantity());
                }
            }
        }
    }

    private function releaseReservedStockForOrder(Order $order, EntityManagerInterface $entityManager): void
    {
        foreach ($order->getOrderDetails() as $orderDetail) {
            if ($orderDetail->getProductIllustration() === 'cours_image.jpg') {
                // Skip stock release for cours
                continue;
            }

            if ($orderDetail->getProductIllustration() !== $orderDetail->getProductName()) {
                // Handle ProductVariant
                $variant = $entityManager->getRepository(\App\Entity\ProductVariant::class)
                    ->find($orderDetail->getProductId());
                if ($variant) {
                    $this->stockManager->releaseStock($variant, $orderDetail->getProductQuantity());
                }
            }
        }
    }
}