<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductVariantType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Informations de base
            FormField::addPanel('Informations générales')->setIcon('fa fa-info-circle'),
            TextField::new('name', 'Nom du produit')
                ->setRequired(true)
                ->setColumns(6),
            
            TextareaField::new('description', 'Description')
                ->setRequired(true)
                ->setColumns(12)
                ->setNumOfRows(4),
            
            NumberField::new('price', 'Prix (€)')
                ->setNumDecimals(2)
                ->setRequired(true)
                ->setColumns(6)
                ->setHelp('Prix en euros (ex: 16.00)'),
            
            // Images
            FormField::addPanel('Gestion des images')->setIcon('fa fa-image'),
            TextField::new('image1File', 'Télécharger Image 1')
                ->setFormType(VichImageType::class)
                ->onlyOnForms()
                ->setColumns(6),
                
            ImageField::new('image1', 'Aperçu Image 1')
                ->setBasePath('/uploads/products/')
                ->onlyOnIndex(),
            
            TextField::new('image2File', 'Télécharger Image 2')
                ->setFormType(VichImageType::class)
                ->onlyOnForms()
                ->setColumns(6),
            
            ImageField::new('image2', 'Aperçu Image 2')
                ->setBasePath('/uploads/products/')
                ->onlyOnIndex(),

            // Variantes
            FormField::addPanel('Variantes')->setIcon('fa fa-list'),
            CollectionField::new('variants', 'Variantes')
                ->allowAdd()
                ->allowDelete()
                ->setEntryIsComplex(true)
                ->setEntryType(ProductVariantType::class),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Product $product */
        $product = $entityInstance;

        // Définir les dates
        if (!$product->getCreatedAt()) {
            $product->setCreatedAt(new \DateTimeImmutable());
        }
        $product->setUpdatedAt(new \DateTimeImmutable());

        // Persister le produit
        parent::persistEntity($entityManager, $entityInstance);

        // Persister les variantes
        foreach ($product->getVariants() as $variant) {
            $variant->setProduct($product);
            $entityManager->persist($variant);
        }

        $entityManager->flush();
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Product $product */
        $product = $entityInstance;

        // Mettre à jour la date
        $product->setUpdatedAt(new \DateTimeImmutable());

        // Mettre à jour le produit
        parent::updateEntity($entityManager, $entityInstance);

        // Mettre à jour les variantes
        foreach ($product->getVariants() as $variant) {
            $variant->setProduct($product);
            $entityManager->persist($variant);
        }

        $entityManager->flush();
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Product $product */
        $product = $entityInstance;

        // Supprimer les variantes
        foreach ($product->getVariants() as $variant) {
            $entityManager->remove($variant);
        }

        // Supprimer le produit
        parent::deleteEntity($entityManager, $entityInstance);

        $entityManager->flush();
    }
}