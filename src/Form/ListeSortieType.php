<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListeSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('campus', EntityType::class,[
                'label'=> 'Campus',
                'class'=> Campus::class,
                'choice_label'=>'nom',
                'required' => false
            ])
            ->add('search', SearchType::class, [
                'label' => 'Le nom contient',
                'required' => false
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Entre',
                'html5'=> true,
                'widget'=> 'single_text',
                'required' => false
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'et',
                'html5'=> true,
                'widget'=> 'single_text',
                'required' => false
            ])
            ->add('ouvertes', CheckboxType::class, [
                'label'=>'Sorties ouvertes',
                'required'=>false
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit(e)',
                'required' => false
            ])
            ->add('pasInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit(e)',
                'required' => false
            ])
            ->add('dejaPassee', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
