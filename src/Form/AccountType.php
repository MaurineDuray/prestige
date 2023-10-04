<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, $this->getConfiguration("Nom", "Nom"))
            ->add('firstname', TextType::class, $this->getConfiguration("Prénom", "Prénom"))
            ->add('entreprise', TextType::class, $this->getConfiguration("Entreprise", "Nom de l'entreprise"))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Email"))
            ->add('phone', TextType::class, $this->getConfiguration("Numéro de téléphone", "numéro"))
            ->add('profession',TextType::class, $this->getConfiguration("Profession", "Profession"))
            ->add('city',TextType::class, $this->getConfiguration("Ville", "Ville"))
            ->add('zipcode',TextType::class, $this->getConfiguration("Code postal", "Code postal"))
            ->add('street', TextType::class, $this->getConfiguration("Rue", "Rue"))
            ->add('tva', TextType::class, $this->getConfiguration("Numéro de TVA", "TVA",[
                "required"=>false
            ]))
            ->add('link',TextType::class, $this->getConfiguration("Site web", "Site web de l'entreprise"))
            ->add('description',TextareaType::class, $this->getConfiguration("Présentation", "Brève présentation"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
