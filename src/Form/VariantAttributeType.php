<?php
namespace App\Form;

use App\Entity\VariantAttribute;
use App\Entity\Attribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VariantAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attribute', EntityType::class, [
                'class' => Attribute::class,
                'choice_label' => 'name', // Display the name of the attribute
                'label' => 'Attribut (e.g., Taille, Couleur)',
                'placeholder' => 'Choisir un attribut',
            ])
            ->add('value', TextType::class, [
                'label' => 'Valeur (e.g., M, Rouge)',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VariantAttribute::class,
        ]);
    }
}