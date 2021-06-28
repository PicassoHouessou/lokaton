<?php

namespace App\Form;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class RegistrationFormFlow extends FormFlow {
    protected $allowDynamicStepNavigation = false;

    protected function loadStepsConfig() {
        return [
            [
                //'label' => 'Compte',
                'form_type' => RegistrationFormType::class,
            ],
            [
                //'label' => 'Compte step 2',
                'form_type' => RegistrationFormType::class,
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
            //return true;
            //return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
            return $estimatedCurrentStepNumber > 1 && !$flow->getFormData();
                },
            ],
        ];
    }

}