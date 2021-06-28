<?php


namespace App\Form\Model;


use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordFormModel
{
    /**
     * @Assert\NotBlank(message="Veuillez entrer un mot de passe")
     * @Assert\Length (min=6, max=4096)
     */

    public $plainPassword;

    /**
     * @Assert\Sequentially(
     *     @Assert\NotBlank(),
     *     @Assert\Length(max="255"),
     *     @Assert\EqualTo(propertyPath="plainPassword", message="Cette valeur doit être égale au mot de passe")
     * )
     */
    public $confirmPlainPassword ;

}