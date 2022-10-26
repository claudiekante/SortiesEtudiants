<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PassewordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                // Ajouts pour le type Repeated
                // Doivent être placés au début
                'type' => PasswordType::class,
                'invalid_message' => 'Les valeurs pour les champs mots de passe doivent être identiques.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe*'],
                'second_options' => ['label' => 'Répétez le mot de passe*'],

                // Code généré par la commande 'make:registration-form'
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe obligatoire',
                    ]),
                    new Length([
                            'min' => 6,
                            'minMessage' => 'Le mot de passe doit avoir une longueur minimum de {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]
                    )
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
