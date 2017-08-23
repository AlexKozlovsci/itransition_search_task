<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 18.08.2017
 * Time: 10:53
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function redirectAction()
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('users');
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/login")
     */
    public function redirectActionLogin()
    {
        return $this->redirectToRoute('login');
    }
}