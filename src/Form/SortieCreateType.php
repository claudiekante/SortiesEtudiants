<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use phpDocumentor\Reflection\TypeResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Time;

class SortieCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut', DateTimeType::class,[
                'label'=>'Date et heure du début',
                'html5'=> true,
                'widget'=> 'single_text'
            ])
            ->add('duree', TimeType::class,[
                'label'=>'Durée',
                'html5'=> true,
                'widget'=> 'single_text'
            ])
            ->add('dateLimitInscription', DateType::class,[
               'label'=>'Date limite pour s\'inscrire',
                'html5'=> true,
                'widget'=> 'single_text'
            ])
//            ->add('nbInscriptionsMax')
            ->add('nbInscriptionsMax')
            ->add('infosSortie', TextareaType::class)
            ->add('campus', EntityType::class, [
               'label'=> 'campus',
               'class'=> Campus::class,
               'choice_label'=>'nom',
                'placeholder'=>'--choisir un campus--'
            ])
            ->add('etat', EntityType::class,[
                'label'=> 'etat',
                'class'=> Etat::class,
                'choice_label'=>'libelle',
                'placeholder'=>'--choisir un état--'
            ])
            ->add('lieu', EntityType::class,[
                'label'=> 'Lieu',
                'class'=> Lieu::class,
                'choice_label'=>'nom',
                'placeholder'=>'--choisir un lieu--'
            ])
                //'class'=> Lieu::class

            //->add('lieuRue',TextType::class,[
              //  'mapped'=>false,
             //   'label'=> 'rue',
                //'class'=> Lieu::class
           // ])
            //->add('ville', EntityType::class, [
                //'mapped'=>false,
               // 'label'=> 'ville',
               // 'class'=>Ville::class,
               // 'choice_label'=>'nom',
                //'placeholder'=>'--choisir une ville--'
           // ])


            //->add('lieu', EntityType::class,[
             //   'label'=> 'lieu',
             //   'class'=> Lieu::class,
            //    'choice_label' => 'nom',
             //   'placeholder'=>'--choisir un lieu--'
           // ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
