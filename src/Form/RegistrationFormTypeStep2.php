<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class RegistrationFormTypeStep2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
    }
    public function getBlockPrefix()
    {
        return 'RegistrationStep2';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
