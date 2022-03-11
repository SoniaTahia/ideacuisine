<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom du produit :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrer le nom du produit",
                    "class" => "bg-red"
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => "le nom du produit est obligatoire",
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 254,
                        'minMessage' => "le nom du produit doit contenir au minimum {{ limit }} caractères",
                        'maxMessage' => "le nom du produit doit contenir au maximum {{ limit }} caractères"
                        ])
                ],
            ] )
            ->add('reference', TextType::class, [
                'label' => "Référence du produit :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrer la référence du produit"
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => "Slug :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrer le slug",
                ],
                'constraints' =>[
                    new Length([
                        'min' => 5,
                        'max' => 254,
                        'minMessage' => "le nom du produit doit contenir au minimum {{ limit }} caractères",
                        'maxMessage' => "le nom du produit doit contenir au maximum {{ limit }} caractères"
                        ])
                ],
            ] )
            ->add('description', TextareaType::class, [
                'label' => "Description du produit :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrer la description du produit",
                    "row" => 4,
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => "la description du produit est obligatoire",
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 10000,
                        'minMessage' => "le nom du produit doit contenir au minimum {{ limit }} caractères",
                        'maxMessage' => "le nom du produit doit contenir au maximum {{ limit }} caractères"
                        ])
                ],
            ])
            ->add('price', IntegerType::class, [
                'label' => "Prix du produit :",
                'required' => false,
                'attr' => [
                    "placeholder" => "entrer le prix du produit"
                ]
            ])
            ->add('stock', IntegerType::class, [
                'label' => "Stock disponible :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrer le stock disponible"
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => "Choisir la catégorie :",
                'placeholder' => "-- sélectionner la catégorie --",
                'class' => Category::class,
                "choice_label" => "name",
                'required' => true,
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
