<?php

namespace App\Form;

use App\Controller\ParcController;
use App\Entity\Materiel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\ParcRepository;

class MaterielType extends AbstractType
{
    private $parcRepository;

    public function __construct(ParcRepository $parcRepository)
    {
        $this->parcRepository = $parcRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $parcNames = $this->parcRepository->getAllParcNames();
        $builder
            ->add('nommat')
            ->add('etatmat', ChoiceType::class, [
                'choices' => [
                    'On marche' => 'On marche',
                    'On panne' => 'On panne',
                ],
            ])
            ->add('quantitemat')
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
        ]);
    }
}


