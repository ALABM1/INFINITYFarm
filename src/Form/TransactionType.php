<?php

// src/Form/TransactionType.php

// src/Form/TransactionType.php

namespace App\Form;

use App\Entity\Transaction;
use App\Repository\CategTransRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TransactionType extends AbstractType
{
    private $categTransRepository;

    public function __construct(CategTransRepository $categTransRepository)
    {
        $this->categTransRepository = $categTransRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérez les éléments de la base de données depuis le CategTransRepository
        $categTransElements = $this->categTransRepository->findAllNomCateg();

        // Transformez les éléments récupérés en un tableau utilisable pour les choix
        $choices = [];
        foreach ($categTransElements as $element) {
            $choices[$element['nomCatTra']] = $element['nomCatTra'];
        }

        $builder
            ->add('categTra', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('typeTra', ChoiceType::class, [
                'choices' => [
                    'Revenu' => 'Revenu',
                    'Dépense' => 'Dépense',
                ],
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('dateTra')
            ->add('montant');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}

