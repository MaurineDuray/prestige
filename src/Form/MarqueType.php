<?php

namespace App\Form;

use App\Entity\Marques;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MarqueType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, $this->getConfiguration("Marque", "Nom de la marque"))
            ->add('link', TextType::class,[
                "required"=>false,
                'label'=>"Lien du site web"
            ])
            ->add('logo',FileType::class, [
                "required"=>true,
                'label'=>"Logo de la marque (jpeg, png)"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marques::class,
        ]);
    }
}
