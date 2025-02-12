<?php 
namespace App\Form;

use App\Entity\Car;
use App\Entity\CarMaintenance;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarMaintenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options ): void
    {

        $user = $options['user']; 

        $builder
            ->add('maintenanceDate', null, [
                'widget' => 'single_text',
                'label' => 'Date de maintenance'
            ])
            ->add('description', null, [
                'label' => 'Description'
            ])
            ->add('cost', null, [
                'label' => 'Coût'
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Terminé' => 'Terminé',
                    'Annulé' => 'Annulé',
                ],
                'placeholder' => 'Choisissez un statut',
                'label' => 'Statut de maintenance', 
            ])
            ->add('car', EntityType::class, [
                'class' => Car::class,
                'choice_label' => 'model', // Display the car model instead of ID
                'label' => 'Voiture',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('c')
                        ->where('c.User = :user')
                        ->setParameter('user', $user);
                },
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarMaintenance::class,
            'user' => null, 
        ]);
    }
}
