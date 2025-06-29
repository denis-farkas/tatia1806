<?php

namespace App\Controller\Admin;

use App\Entity\Gala;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class GalaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gala::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(), // Hide the ID field in forms
            TextField::new('name', 'Nom du Gala'), // Display the name field
            TextareaField::new('description', 'Description'), // Use TextareaField to avoid HTML tags
            DateTimeField::new('date', 'Date du Gala'), // Display the date field
        ];
    }
}
