<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StockType extends AbstractType
{

    public const STATUS = [
        'Utilisable' => 'Utilisable',
        'Inutilisable' => 'Inutilisable',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('SKU', TextType::class, [
                'label' => 'Numéro SKU',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez le numéro SKU du produit',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Fournisseur / Fabriquant',
                ]
            ])
            ->add('size1', NumberType::class, [
                'label' => 'Dimension 1',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez une dimension du produit',
                ]
            ])
            ->add('size2', NumberType::class, [
                'label' => 'Dimension 2',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez l\'autre dimension du produit',
                ]
            ])
            ->add('size1Unit', TextType::class, [
                'label' => 'Unité de la dimension 1',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez l\'unité de la 1ère dimension du produit',
                ]
            ])
            ->add('size2Unit', TextType::class, [
                'label' => 'Unité de la dimension 2',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez l\'unité de la 2nde dimension du produit',
                ]
            ])
            ->add('size1Name', TextType::class, [
                'label' => 'Nom de la dimension 1',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez le nom de la 1ère dimension du produit',
                ]
            ])
            ->add('size2Name', TextType::class, [
                'label' => 'Nom de la dimension 2',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez le nom de la 2nde dimension du produit',
                ]
            ])
            ->add('resultUnit', TextType::class, [
                'label' => 'Résultat du calcul',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez le résultat en m2 du calcul des deux dimensions',
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez le prix au mètre carré du produit',
                ]
            ])
            ->add('productFamily', TextType::class, [
                'label' => 'Famille de produit',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez la famille de produit auquel appartient le produit',
                ]
            ])
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'attr' => [
                    'class' => 'border-primary',
                    'placeholder' => 'Rentrez la référence du produit',
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'attr' => [
                    'class' => 'border-primary',
                ],
                'choices' => self::STATUS,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}
