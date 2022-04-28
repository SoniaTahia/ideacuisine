<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Votre adresse email : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre adresse de courriel",
                ],

            ])
            // ->add('agreeTerms', CheckboxType::class, [
            //     'mapped' => false,
            //     'constraints' => [
            //         new IsTrue([
            //             'message' => 'You should agree to our terms.',
            //         ]),
            //     ],
            // ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Votre mot de passe :',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'entrez votre mot de passe ici',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} characteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => "Votre prénom : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre prénom ici",
                ],
            ])
            ->add('name', TextType::class, [
                'label' => "Votre nom : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre nom ici",
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => "Votre numéro de téléphone : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre numéro ici",
                   
                ],
                'row_attr' => [
                   
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre numéro de téléphone doit contenir {{ limit }} characteres',
                        // max length allowed by Symfony for security reasons
                    ]),
                ],
            ])
            ->add('adress', TextType::class, [
                'label' => "Votre adresse : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre numéro et rue ici",
                ],
            ])
            ->add('postCode', TextType::class, [
                'label' => "Votre code postal : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre code postal ici",
                ],
            ])
            ->add('town', TextType::class, [
                'label' => "Votre ville : ",
                'required' => true,
                'attr' => [
                    'placeholder' => "entrez votre ville ici",
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
