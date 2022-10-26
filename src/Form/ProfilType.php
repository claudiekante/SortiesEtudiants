<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label'=>'Pseudo*',
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom*',
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prenom*',
            ])
            ->add('campus', EntityType::class,[
                'label'=>'Campus*',
                'class' => Campus::class,
                'choice_label' =>'nom',
                'placeholder'=>'--choisi ton campus--'
            ])
            ->add('telephone', TextType::class, [
                'label'=>'Telephone*',
            ])
            ->add('email', TextType::class, [
                'label'=>'Email*',
            ])
//            ->add('agreeTerms', CheckboxType::class, [
//                'mapped' => false,
//                'constraints' => [
//                    new IsTrue([
//                        'message' => 'You should agree to our terms.',
//                    ]),
//                ],
//            ])

            ->add('avatar', FileType::class,
                [ 'mapped' => false,
                'required' => false,
                'label'=>'Photo du profil',
                'constraints' => [ new Image( ["mimeTypesMessage" => "Le format de fichier n'est pas autorisé."])
                    ]
            ])
        ;
        //--------------------------------------------------------
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
