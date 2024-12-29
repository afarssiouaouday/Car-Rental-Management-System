<?php

namespace App\Form;

use App\Entity\Car;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', null, [
                'label' => 'Marque',
            ])
            ->add('model', null, [
                'label' => 'Modèle',
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image (JPEG, PNG)',
                'required' => false,
            ])
            ->add('price', null, [
                'label' => 'Prix',
            ])
            ->add('year', null, [
                'label' => 'Année',
            ])
            ->add('licenseplate', null, [
                'label' => 'Plaque d\'immatriculation',
            ])
            ->add('fueltype', ChoiceType::class, [
                'choices' => [
                    'Essence' => 'Essence',
                    'Diesel' => 'Diesel',
                    'Électrique' => 'Électrique',
                    'Hybride' => 'Hybride',
                ],
                'placeholder' => 'Choisissez un type de carburant',
                'label' => 'Type de carburant'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
