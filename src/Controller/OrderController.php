<?php

namespace App\Controller;

use App\Entity\User;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /*
     * Choix de l'adresse de livraison 
     */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {
        
        /** @var User $user */
        $user = $this->getUser();
       
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $addresses = $user->getAddresses();
      

        if (!$addresses || count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary')
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        $coursCart = $request->getSession()->get('cours_cart', []);
        $products = $cart->getCart();
        $totals = $cart->getTotals();
        $totalQuantity = $cart->getTotalQuantity();

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $user->getAddresses(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Création de la chaîne adresse
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname().' '.$addressObj->getLastname().'<br/>';
            $address .= $addressObj->getAddress().'<br/>';
            $address .= $addressObj->getPostal().' '.$addressObj->getCity().'<br/>';
            $address .= $addressObj->getCountry().'<br/>';
            $address .= $addressObj->getPhone();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTimeImmutable());
            $order->setState(1);
            $order->setTotalPrice($totals['total']);

            $order->setDelivery($address);

            // Produits physiques
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getImage1());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductQuantity($product['qty']);
                $orderDetail->setProductId($product['object']->getId());
                $order->addOrderDetail($orderDetail);
                $entityManager->persist($orderDetail);
            }

            // Cours (depuis la session)
            $coursCart = $request->getSession()->get('cours_cart', []);
            foreach ($coursCart as $item) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($item['cours']->getName());
                $orderDetail->setProductIllustration('cours_image.jpg'); // ou une image par défaut
                $orderDetail->setProductPrice($item['cours']->getPrice());
                $orderDetail->setProductQuantity($item['quantity']);
                $orderDetail->setProductId($item['cours']->getId());
                $orderDetail->setChildFirstname($item['child_firstname'] ?? null);
                $order->addOrderDetail($orderDetail);
                $entityManager->persist($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'cours_cart' => $coursCart,
            'order' => $order,
            'totals' => $totals,
            'totalQuantity' => $totalQuantity
        ]);
    }
}
