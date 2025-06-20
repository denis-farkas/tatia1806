<?php


namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    

    public function __construct(private RequestStack $requestStack)
    {
    }

    public function addProduct($product, $quantity)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $id = $product->getId();

        if (empty($cart[$id])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $quantity
            ];
        } else {
            $cart[$id]['qty'] += $quantity;
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function increaseQuantity($product)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $id = $product->getId();

        $cart[$id]['qty']++;
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function decreaseQuantity($product)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $id = $product->getId();

        if ($cart[$id]['qty'] > 1) {
            $cart[$id]['qty']--;
        } else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function remove($product)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $id = $product->getId();

        unset($cart[$id]);
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function getTotalQuantity()
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $quantity = 0;

        foreach ($cart as $product) {
            $quantity += $product['qty'];
        }

        return $quantity;
    }

    public function getTotals(): array
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $coursCart = $this->requestStack->getSession()->get('cours_cart', []);
        $subtotal = 0;
        $shipping = 0;

        // Products
        foreach ($cart as $product) {
            $subtotal += $product['object']->getPrice() * $product['qty'];
            $shipping += 4.49 * $product['qty']; // 4.49€ per product
        }

        // Cours (no shipping)
        foreach ($coursCart as $item) {
            $subtotal += $item['cours']->getPrice() * $item['quantity'];
        }

        $total = $subtotal + $shipping;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ];
    }

    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    public function removeAll()
    {
        $this->requestStack->getSession()->remove('cart');
    }

    public function reset()
    {
        $this->requestStack->getSession()->remove('cart');
        $this->requestStack->getSession()->remove('cours_cart');
    }

    public function addCours($cours, $childFirstname, $childLastname)
    {
        $cart = $this->requestStack->getSession()->get('cours_cart', []);

        // Prevent duplicate for same cours and child
        foreach ($cart as $item) {
            if (
                $item['cours_id'] === $cours->getId() &&
                $item['child_firstname'] === $childFirstname &&
                $item['child_lastname'] === $childLastname
            ) {
                return;
            }
            // Prevent overlap for same child
            if (
                $item['child_firstname'] === $childFirstname &&
                $item['child_lastname'] === $childLastname &&
                $item['cours']->getDay() === $cours->getDay() &&
                $this->isTimeOverlap($item['cours'], $cours)
            ) {
                throw new \Exception("Ce créneau horaire est déjà pris pour {$childFirstname} {$childLastname}.");
            }
        }

        $cart[] = [
            'cours_id' => $cours->getId(),
            'child_firstname' => $childFirstname,
            'child_lastname' => $childLastname,
            'cours' => $cours,
            'quantity' => 1
        ];

        $this->requestStack->getSession()->set('cours_cart', $cart);
    }

    // Helper to check time overlap
    private function isTimeOverlap($coursA, $coursB)
    {
        $startA = $coursA->getStartHour()->format('H:i');
        $endA = $coursA->getEndHour()->format('H:i');
        $startB = $coursB->getStartHour()->format('H:i');
        $endB = $coursB->getEndHour()->format('H:i');

        return ($startA < $endB) && ($startB < $endA);
    }
}