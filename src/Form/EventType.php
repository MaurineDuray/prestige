<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre", "Nom de l'événement'"))
            ->add('picture',FileType::class, [
                "required"=>false,
                'label'=>"Image de l'évènement (jpeg, png)"
            ])
            ->add('date')
            ->add('infos', TextareaType::class, $this->getConfiguration("Infos pratiques", "Date, heure, adresse"))
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Description de l'événement"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
