<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $builder
                ->add('email', null, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'Votre adresse email'
                ],
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'max' => 100,
                    ]),
                ],

            ])
                ->add('plainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'label' => 'Mot de Passe',
                    'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Votre mot de passe'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ])
                ->add('confirmPlainPassword', PasswordType::class, [
                    // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'mapped' => false,
                    'label' => 'Confirmez',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ]);
                break;
            case 2:
                $builder
                    ->add('username', null, [
                        'label'=>"Nom d'utilisateur",
                        'attr'=> ["placeholder"=>"Votre nom d'utilisateur"],
                        'constraints' => [
                            new NotNull(),
                            new Length([
                                'min' => 3,
                                'max' => 100,
                            ]),
                        ],
                    ])
                    ->add('phoneNumber',null, [
                        'label'=>"Numéro de téléphone",
                        'attr'=> ["placeholder"=>"Entrez votre Numéro de téléphone"],
                        'constraints' => [
                            new NotNull(),
                        ],
                    ])
                ;
                break;
            default:
                throw new \Exception('Unexpected value');
        }
    }

    public function getBlockPrefix() {
        return 'RegistrationFormType';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,]);
    }
}

