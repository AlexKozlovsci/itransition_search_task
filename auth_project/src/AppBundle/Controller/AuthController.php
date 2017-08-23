<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\LoginForm;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends Controller
{
    /**
     * @Route("/auth/login", name = "login")
     */
    public function loginAction()
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render(
            'auth/index.html.twig',
            array(
                'last_username' => $helper->getLastUsername(),
                'error'         => $helper->getLastAuthenticationError(),
            )
        );
    }

    /**
     * @Route("/auth/register", name="user_register")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(LoginForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $encoder = $this->get('security.password_encoder');
            $password = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // Set their role
            $user->addRole('ROLE_USER');
            $user->setLoginType('site');
            $user->setEnabled(true);
            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }
}