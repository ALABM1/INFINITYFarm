<?php

// src/Form/Users1Type.php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class Users1Type extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->add('mail', EmailType::class, [
                'constraints' => [
                    new Callback([$this, 'validateUniqueEmail']),
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
                    'OUVRIER' => 'OUVRIER',
                    'AGRICULTEUR' => 'AGRICULTEUR',
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
            ->add('motdepasse')
            ->add('ville')
            ->add('profileImageFile', FileType::class, [
                'label' => 'Photo de profil',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }

    public function validateUniqueEmail($value, ExecutionContextInterface $context)
    {
        $existingUser = $this->entityManager->getRepository(Users::class)->findOneBy(['mail' => $value]);

        if ($existingUser) {
            $context->buildViolation('Cet email est déjà utilisé.')
                ->atPath('mail')
                ->addViolation();
        }
    }
}
