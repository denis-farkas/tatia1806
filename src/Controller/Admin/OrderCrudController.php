<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Action::INDEX, Action::DETAIL) // Enable the "detail" action
            ->disable(Action::DELETE); // Optional: Disable delete if needed
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('user.email', 'User Email')->onlyOnIndex(), // Add email field
            TextEditorField::new('delivery', 'Delivery Address')->onlyOnDetail(),
            TextEditorField::new('orderDetails', 'Order Details')
                ->formatValue(function ($value, $entity) {
                    $details = $entity->getOrderDetails();
                    $content = '<ul>';
                    foreach ($details as $detail) {
                        $content .= sprintf(
                            '<li>%s x%d - %s €</li>',
                            $detail->getProductName(),
                            $detail->getProductQuantity(),
                            number_format($detail->getProductPrice(), 2, ',', ' ')
                        );

                        if (!empty($detail->getOptions())) {
                            $content .= '<ul>';
                            foreach ($detail->getOptions() as $option) {
                                $content .= sprintf('<li>%s: %s</li>', $option['name'], $option['value']);
                            }
                            $content .= '</ul>';
                        }
                    }
                    $content .= '</ul>';
                    return $content;
                })
                ->onlyOnDetail(),
            ChoiceField::new('state', 'Order Status')
                ->setChoices([
                    'En attente de paiement' => 1,
                    'Payée' => 2,
                    'En cours de préparation' => 3,
                    'Expédiée' => 4,
                    'Annulée' => 5,
                ])
                ->renderAsBadges() // Use badges for better UI
                ->setTemplatePath('admin/state.html.twig'), // Use the custom template
        ];
    }
}
