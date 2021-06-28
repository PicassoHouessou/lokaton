<?php

namespace App\Form;

use App\Form\Model\ResetPasswordFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password', 'placeholder' => 'Entrez votre mot de passe',
                    'class' => 'form-control mb-3',],
            ])
            ->add('confirmPlainPassword', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Confirmez votre mot de passe',
                    'class' => 'form-control mb-3',],

            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ResetPasswordFormModel::class,]);
    }
}
