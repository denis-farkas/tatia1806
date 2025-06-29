<?php


namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    // Add a ProductVariant to the cart
    public function addVariant($variant, $quantity)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $id = $variant->getId();

        if (empty($cart[$id])) {
            $cart[$id] = [
                'variant' => $variant,
                'qty' => $quantity,
                'price' =>  $variant->getProduct()->getPrice(),
                'attributes' => $variant->getAttributesAsArray(), // Include attributes as an array
            ];
        } else {
            $cart[$id]['qty'] += $quantity;
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    // Remove a ProductVariant from the cart
    public function removeVariant($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);

        dump($cart); // Debug the cart structure before removing the item

        if (isset($cart[$id])) {
            unset($cart[$id]); // Remove the variant from the cart
        }

        dump($cart); // Debug the cart structure after removing the item

        $this->requestStack->getSession()->set('cart', $cart);
    }

    // Add an image to the images cart
    public function addImage($image, $quantity)
    {
        $imagesCart = $this->requestStack->getSession()->get('images_cart', []);
        $id = $image->getId();

        if (empty($imagesCart[$id])) {
            $imagesCart[$id] = [
                'image' => $image,
                'qty' => $quantity,
            ];
        } else {
            $imagesCart[$id]['qty'] += $quantity;
        }

        $this->requestStack->getSession()->set('images_cart', $imagesCart);
    }

    // Remove an image from the images cart
    public function removeImage($image)
    {
        $imagesCart = $this->requestStack->getSession()->get('images_cart', []);
        $id = $image->getId();

        unset($imagesCart[$id]);
        $this->requestStack->getSession()->set('images_cart', $imagesCart);
    }

    // Get the images cart
    public function getImagesCart()
    {
        return $this->requestStack->getSession()->get('images_cart', []);
    }

    // Get the total quantity of items in the cart
    public function getTotalQuantity()
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $coursCart = $this->requestStack->getSession()->get('cours_cart', []);
        $imagesCart = $this->requestStack->getSession()->get('images_cart', []);
        $quantity = 0;

        foreach ($cart as $item) {
            $quantity += $item['qty'];
        }

        foreach ($coursCart as $item) {
            $quantity += $item['quantity'];
        }

        foreach ($imagesCart as $item) {
            $quantity += $item['qty'];
        }

        return $quantity;
    }


    // Get the totals for the cart
    public function getTotals(): array
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $coursCart = $this->requestStack->getSession()->get('cours_cart', []);
        $imagesCart = $this->requestStack->getSession()->get('images_cart', []);
        $subtotal = 0;
        $shipping = 0;

        // Products
        foreach ($cart as $item) {
            $subtotal += (int) $item['price'] * $item['qty']; // Use the stored price directly

            // Example shipping cost logic
            $shipping += 6 + (1.5 * $item['qty']);
        }

        // Cours (no shipping)
        foreach ($coursCart as $item) {
            $subtotal += $item['cours']->getPrice() * $item['quantity'];
        }
        // Images (no shipping)
        foreach ($imagesCart as $item) {
            $subtotal += $item['image']->getPrice() * $item['qty'];
        }

        $total = $subtotal + $shipping;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ];
    }

    // Get the cart
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    // Remove all items from the cart
    public function removeAll()
    {
        $this->requestStack->getSession()->remove('cart');
    }

    // Reset both product, cours, and images carts
    public function reset()
    {
        $this->requestStack->getSession()->remove('cart');
        $this->requestStack->getSession()->remove('cours_cart');
        $this->requestStack->getSession()->remove('images_cart');
    }

    // Add a cours to the cours cart
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

    // Get the cours cart
    public function getCoursCart(): array
    {
        return $this->requestStack->getSession()->get('cours_cart', []);
    }
}