<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Repository\EtatRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\TypeResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('nbInscriptionsMax')
            ->add('infosSortie', TextareaType::class)
            ->add('etat', EntityType::class,[
                'label'=> 'etat',
                'class'=> Etat::class,
                'query_builder'=> function (EtatRepository $etatRepository) {
                    return $etatRepository->createQueryBuilder('e')
                        ->select('e')
                        ->andWhere('e.libelle = :libelle')
                        ->orWhere('e.libelle = :libelle2')
                        ->setParameter('libelle','Ouverte')
                        ->setParameter('libelle2','Créée');
                },
                'choice_label'=>'libelle',
                'placeholder'=>'--choisir un état--'


          ])

            ->add('lieu', EntityType::class,[
                'label'=> 'Lieu',
                'class'=> Lieu::class,
                'choice_label'=>'nom',
                'placeholder'=>'--choisir un lieu--'
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
