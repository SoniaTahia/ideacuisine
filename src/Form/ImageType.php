<?php

namespace App\Form;

use App\Entity\Image;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Image1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Titre de la photo",
                'required' => true,
            ] )
            ->add('alt', TextType::class, [
                'label' => "Alt",
                'required' => true,
            ] )
            ->add('picture', FileType::class, [
                'label' => "Télecharger une photo du produit",
                'required' => false,
                'multiple' => false,
                'mapped' => false,
                'attr' => [
                    "placeholder" => "Télecharger une photo du produit"
                ],
                'constraints' =>[
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/jpg',
                            'image/png'
                        ],
                    ]),
                    
                ],
            ])   
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
