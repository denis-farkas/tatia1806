<?php
namespace App\Form;

use App\Entity\ProductVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductVariantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
            ])
            ->add('minQuantity', IntegerType::class, [
                'label' => 'Quantité minimale',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false, // Allow it to be unchecked
                'help' => 'Cochez pour activer ou désactiver cette variante.',
                'mapped' => true, // Explicitly map to the isActive property
            ])
            ->add('attributes', CollectionType::class, [
                'entry_type' => VariantAttributeType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductVariant::class,
        ]);
    }
}