<?php
// TerrainSearchType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerrainSearchType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('search', TextType::class, [
'label' => 'Search by Terrain Name',
'required' => false,
]);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
// Configure your form options if needed
]);
}
}
