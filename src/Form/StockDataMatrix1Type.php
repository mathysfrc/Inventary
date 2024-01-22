<?php

namespace App\Form;

use App\Entity\StockDataMatrix;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockDataMatrix1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('SKU')
            ->add('description')
            ->add('productFamily')
            ->add('reference')
            ->add('price')
            ->add('size1Name')
            ->add('size1')
            ->add('size1Unit')
            ->add('size2Name')
            ->add('size2')
            ->add('size2Unit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => StockDataMatrix::class,
        ]);
    }
}
