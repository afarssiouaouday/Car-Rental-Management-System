<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom',
                'attr' => ['novalidate' => true], // Pas de validation HTML
            ])
            ->add('cin', null, [
                'label' => 'CIN',
                'attr' => ['novalidate' => true],
            ])
            ->add('address', null, [
                'label' => 'Adresse',
                'attr' => ['novalidate' => true],
            ])
            ->add('city', null, [
                'label' => 'Ville',
                'attr' => ['novalidate' => true],
            ])
            ->add('phone', null, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['novalidate' => true], // Supprime les validations HTML
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'attr' => ['novalidate' => 'novalidate'], // Désactive la validation globale HTML5
        ]);
    }
}
