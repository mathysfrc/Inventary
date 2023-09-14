<?php

namespace App\Form;

use App\Entity\Tracking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrackingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('size1')
            ->add('size2')
            ->add('size1Unit')
            ->add('size2Unit')
            ->add('size1Name')
            ->add('size2Name')
            ->add('resultUnit')
            ->add('price')
            ->add('productFamily')
            ->add('reference')
            ->add('free')
            ->add('comment')
            ->add('timestamp')
            ->add('movementType')
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tracking::class,
        ]);
    }
}
