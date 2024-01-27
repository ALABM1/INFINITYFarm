<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\Extension\Core\Type\TextType; // Add this line
use Symfony\Component\Form\Extension\Core\Type\TelType; // Add this line
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Add this line
use Symfony\Component\Validator\Constraints\Regex; // Add this line

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Form\Extension\Core\Type\FileType; // Ajoutez cette ligne





class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le nom ne doit contenir que des lettres.',
                ]),
            ],
        ])
        ->add('prenom', TextType::class, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[a-zA-Z]+$/',
                    'message' => 'Le prénom ne doit contenir que des lettres.',
                ]),
            ],
        ])
        ->add('email', EmailType::class, [
            'constraints' => [
                new Email([
                    'message' => 'L\'adresse e-mail "{{ value }}" n\'est pas valide.',
                ]),
            ],
        ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('numerotelephone', TelType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le numéro de téléphone doit contenir uniquement des chiffres.',
                    ]),
                ],
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'ADMIN' => 'ADMIN',
                    'AGRICULTEUR' => 'AGRICULTEUR',
                    'OUVRIER' => 'OUVRIER',
                    
                    // Ajoutez d'autres rôles si nécessaire
                ],
                'placeholder' => 'Sélectionnez un rôle',
            ])
            ->add('ville', ChoiceType::class, [
                'choices' => [
                    'TUNIS' => 'TUNIS',
                    'ARIANA' => 'ARIANA',
                    'BEN AROUS' => 'BEN AROUS',
                    'SFAX' => 'SFAX',
                    'BIZERTE' => 'BIZERTE',

                    // Ajoutez d'autres rôles si nécessaire
                ],
                'placeholder' => 'Sélectionnez un rôle',
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme',
                    // Ajoutez d'autres choix si nécessaire
                ],
                'placeholder' => 'Sélectionnez le sexe',
            ])
            ->add('profileImageFile', FileType::class, [
                'label' => 'Profile Image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
