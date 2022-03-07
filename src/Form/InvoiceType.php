<?php

namespace App\Form;

use App\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "votre prénom : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre prénom ici",
                ],
            ])
            ->add('name', TextType::class, [
                'label' => "votre nom : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre nom ici",
                ],
            ])
            ->add('adress', TextType::class, [
                'label' => "votre adresse : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre numéro et rue ici",
                ],
            ])
            ->add('postCode', TextType::class, [
                'label' => "votre code postal : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre code postal ici",
                ],
            ])
            ->add('town', TextType::class, [
                'label' => "votre ville : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre ville ici",
                ],
            ])
            ->add('country', TextType::class, [
                'label' => "votre pays : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre ville ici",
                ],
            ])
            
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
