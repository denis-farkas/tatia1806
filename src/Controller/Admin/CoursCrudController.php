<?php

namespace App\Controller\Admin;

use App\Entity\Cours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;


class CoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cours::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
    return [
        TextField::new('name')->setLabel('nom'),
        TextField::new('age')->setLabel('âge'),
        TextField::new('day')->setLabel('jour'),
        TextField::new('schedule')->setLabel('horaire'),
        TextField::new('salle')->setLabel('salle'),
        NumberField::new('price')->setLabel('prix'),
        TextareaField::new('description')->setLabel('description'),
        TimeField::new('startHour')->setLabel('Début du cours'),
        TimeField::new('endHour')->setLabel('Fin du cours'),
    ];
    }
}
