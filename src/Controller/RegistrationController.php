<?php

namespace App\Controller;

use App\Form\RegistrationFormFlow;
use App\Entity\User;
use App\Form\RegistrationFormType2;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $noReplyEmail;

    public function __construct(EmailVerifier $emailVerifier, $noReplyEmail)
    {
        $this->emailVerifier = $emailVerifier;
        $this->noReplyEmail = $noReplyEmail;
    }

    /**
     * @Route("/register", name="app_register", options={"sitemap" = true})
     */
    public function register(UserPasswordHasherInterface $userPasswordHasher, RegistrationFormFlow $flow, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $flow->bind($user);

        // form of the current step
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            /*
                        $data = $flow->getRequest()->request->get('RegistrationStep1') ;
                                    // encode the plain password
                        $flow->getFormData()->setPassword(
                                        $userPasswordHasher->hashPassword(
                                            $user,
                                            $data['plainPassword']
                                        )
                                    );
                        */

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm();

            } else {

                $passwordHash = $flow->getRequest()->getSession()->get('currentHash');
                $flow->getRequest()->getSession()->remove('currentHash');

                $user = $flow->getFormData();

                $user->setPassword($passwordHash);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $flow->reset(); // remove step data from the session

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address($this->noReplyEmail, 'No reply'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                return $this->redirectToRoute("app_register_confirm");
            }
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'flow' => $flow,
        ]);
    }

    /**
     * @Route("/register/complete", name="app_register_confirm", methods={"get"})
     */
    public function registrationComplete()
    {

        return $this->render('registration/complete.html.twig', [
            "user" => $this->getUser(),
        ]);
    }
    /**
     * @Route("/register/resend-email", name="app_register_resend_email")
     */
    public function resendEmail()
    {
        $user = $this->getUser();
        if ($user === null) {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }


        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($this->noReplyEmail, 'No reply DMV'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        return $this->redirectToRoute('app_register_confirm');
    }

    public function register2(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator2 $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType2::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address($this->noReplyEmail, 'No reply'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}