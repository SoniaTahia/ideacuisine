<?php

namespace App\Form;

use App\Form\Builder\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
             ->add('email', EmailType::class, [
                'label' => "Votre adresse email :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrez votre adresse email"
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => "le sujet du message est obligatoire",
                    ]),
                    new Email([
                        'message' => "l'adresse email doit être valide"
                    ])
                ],
             ])
            ->add('subject', TextType::class, [
                'label' => "Sujet de votre message :",
                'required' => true,
                'attr' => [
                    "placeholder" => "entrez le sujet de votre message"
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => "le sujet du message est obligatoire",
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 100,
                        'maxMessage' => "le sujet ne doit pas excéder {{ limit }} caractères"
                        ])
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => "Votre message : ",
                'required' => true,
                'attr' => [
                    "placeholder" => "écrivez votre message",
                    "row" => 4
                ],
                'constraints' =>[
                    new NotBlank([
                        'message' => "le sujet du message est obligatoire",
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 1000,
                        'maxMessage' => "le sujet ne doit pas excéder {{ limit }} caractères"
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
