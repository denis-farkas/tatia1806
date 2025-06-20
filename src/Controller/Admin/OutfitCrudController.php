<?php

namespace App\Controller\Admin;

use App\Entity\Outfit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class OutfitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Outfit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('name'),
            TextEditorField::new('description'),
            NumberField::new('price')->setLabel('prix'),
            
            TextField::new('image')->onlyOnIndex(),            

            Field::new('imageFile')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
                
            TextField::new('cours'),
        ];
    }
}
