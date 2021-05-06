<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Campus;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('dateSortie')
            ->add('dateFinInscription')
            ->add('nombrePlaces')
            //->add('organisateur')
            //->add('etat', EntityType::class, ['class'=>Etat::class, 'choice_label' => 'label'])
            ->add('campus', EntityType::class, ['class'=>Campus::class, 'choice_label' => 'nom'])
            ->add('lieu', EntityType::class, ['class'=>Lieu::class, 'choice_label' => 'nom'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
