<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        'Structure' => 'Structure',
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
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size2', ChoiceType::class, [
                'label' => 'Dimension 2',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size1Unit', ChoiceType::class, [
                'label' => 'Unité de la dimension 1',
                'required' => false,
                'empty_data' => '',
                'choices' => self::UNIT1,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size2Unit', ChoiceType::class, [
                'label' => 'Unité de la dimension 2',
                'required' => false,
                'empty_data' => '',
                'choices' => self::UNIT2,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size1Name', TextType::class, [
                'label' => 'Nom de la dimension 1',
                'required' => false,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])
            ->add('size2Name', TextType::class, [
                'label' => 'Nom de la dimension 2',
                'required' => false,
                'attr' => [
                    'class' => 'border-tertiary',
                ]
            ])

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
                    'placeholder' => 'Ex : Produit',
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
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
                $form = $event->getForm();
                $size1 = $event->getData()['size1'];
                if($size1){
                    $form->add('size1', ChoiceType::class, ['choices' => [$size1 => $size1]]);
                }
             })
             ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
                $form = $event->getForm();
                $size2 = $event->getData()['size2'];
                if($size2){
                    $form->add('size2', ChoiceType::class, ['choices' => [$size2 => $size2]]);
                }
             })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
            'csrf_protection' => false,
        ]);
    }
}