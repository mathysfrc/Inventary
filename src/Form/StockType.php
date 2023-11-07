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

    public const UNIT1 = [
        'mm' => 'mm',
        'unité' => 'unité',

    ];

    public const UNIT2 = [
        'm' => 'm',
        'mm' => 'mm',
        'mL' => 'mL',
    ];

    public const FAMILY = [
        'Adhésif découpe' => 'Adhésif découpe',
        'Support rigide' => 'Support rigide',
        'Support souple' => 'Support souple',
        'Adhésif impression' => 'Adhésif impression',
        'Film de lamination' => 'Film de lamination',
        'Liquide' => 'Liquide',
    ];

    public const DIMENSION1 = [
        '1' => '1',
        '610' => '610',
        '1000' => '1000',
        '1050' => '1050',
        '1230' => '1230',
        '1250' => '1250',
        '1370' => '1370',
        '1520' => '1520',
        '1600' => '1600',
        '2000' => '2000',
        '3000' => '3000',
    ];

    public const DIMENSION2 = [
        '1.20' => '1.20',
        '1.50' => '1.50',
        '2' => '2',
        '3' => '3',
        '2.50' => '2.50',
        '5' => '5',
        '15' => '15',
        '30' => '30',
        '50' => '50',
        '1500' => '1500',
        '5000' => '5000',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('SKU', TextType::class, [
                'label' => 'Numéro SKU',
                'required' => false,
                'attr' => [
                    'class' => 'border-tertiary',
                    'placeholder' => '(000)0118',
                ]
            ])
            ->add('productFamily', ChoiceType::class, [
                'label' => 'Famille de produit',
                'choices' => self::FAMILY,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'border-tertiary',
                    'placeholder' => 'Ex : Produit',
                ]
            ])
            ->add('size1', ChoiceType::class, [
                'label' => 'Dimension 1',
                'empty_data' => '',
                'choices' => self::DIMENSION1,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size2', ChoiceType::class, [
                'label' => 'Dimension 2',
                'empty_data' => '',
                'choices' => self::DIMENSION2,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size1Unit', ChoiceType::class, [
                'label' => 'Unité de la dimension 1',
                'empty_data' => '',
                'choices' => self::UNIT1,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size2Unit', ChoiceType::class, [
                'label' => 'Unité de la dimension 2',
                'empty_data' => '',
                'choices' => self::UNIT2,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size1Name', TextType::class, [
                'label' => 'Nom de la dimension 1',
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size2Name', TextType::class, [
                'label' => 'Nom de la dimension 2',
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            // ->add('resultUnit', TextType::class, [
            //     'label' => 'Résultat du calcul',
            //     'attr' => [
            //         'class' => 'border-tertiary',
            //         'placeholder' => 'Rentrez le résultat en m2 du calcul des deux dimensions',
            //     ]
            // ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => [
                    'class' => 'border-tertiary',
                    'placeholder' => 'Ex : 8.50 €',
                ]
            ])

            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'attr' => [
                    'class' => 'border-tertiary',
                    'placeholder' => 'Ex: Produit',
                    'required' => false,
                ]
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'attr' => [
                    'class' => 'border-tertiary',
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
