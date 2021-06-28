<?php

namespace App\EventSubscriber;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Event\PostBindFlowEvent;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationFormFlowSubscriber extends FormFlow implements EventSubscriberInterface
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * This method is only needed when _not_ using autoconfiguration. If it's there even with autoconfiguration enabled,
     * the `removeSubscriber` call ensures that subscribed events won't occur twice.
     * (You can remove the `removeSubscriber` call if you'll definitely never use autoconfiguration for that flow.)
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        parent::setEventDispatcher($dispatcher);
        $dispatcher->removeSubscriber($this);
        $dispatcher->addSubscriber($this);
    }

    public static function getSubscribedEvents()
    {
        return [
            FormFlowEvents::PRE_BIND => 'onPreBind',
            FormFlowEvents::GET_STEPS => 'onGetSteps',
            FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
            FormFlowEvents::POST_BIND_FLOW => 'onPostBindFlow',
            FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
            FormFlowEvents::POST_VALIDATE => 'onPostValidate',
        ];
    }

    public function onPreBind(PreBindEvent $event)
    {
        // ...
    }

    public function onGetSteps(GetStepsEvent $event)
    {
        // ...
    }

    public function onPostBindSavedData(PostBindSavedDataEvent $event)
    {
        // ...
    }

    public function onPostBindFlow(PostBindFlowEvent $event)
    {
        // ...
    }

    public function onPostBindRequest(PostBindRequestEvent $event)
    {
        // ...
    }

    public function onPostValidate(PostValidateEvent $event)
    {
        $flow = $event->getFlow();
        if ($flow->getCurrentStepNumber() != 1) {
            return;
        }
        $request = $flow->getRequest();
        $routeName = $request->attributes->get('_route');
        if ($routeName === "app_register") {
            $data = $request->request->get('RegistrationFormType');
            $request->getSession()->set('currentHash', $this->passwordHasher->hashPassword($flow->getFormData(), $data['plainPassword']));
        }
    }

    // ...

}
