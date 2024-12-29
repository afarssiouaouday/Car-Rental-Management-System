<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Car;
use App\Entity\Customer;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',  
                    'Confirmée' => 'confirmee',    
                    'Annulée' => 'annulee',       
                ],
                'label' => 'Statut de la réservation',
                'placeholder' => 'Choisir un statut',  
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'name',
                'label' => 'Client', 
            ])
            ->add('amount_paid', null, [
                'label' => 'Montant payé', 
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer', 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
