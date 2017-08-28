<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 14.08.2017
 * Time: 14:28
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;



class UserController extends Controller
{
    private function renderPage($page, $data)
    {
        $templating = $this->container->get('templating');
        $html = $templating->render($page, array('users' => $data));
        return $html;
    }

    /**
     * @Route("/users", name = "users")
     */
    public function showUsers()
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();
        return new Response($this->renderPage('users/index.html.twig', $users));
    }

    /**
     * @Route("/users/block/{id}")
     */
    public function blockUser($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($user->getEnabled() == 1) {
            $user->setEnabled(false);
        }else{
            $user->setEnabled(true);
        }
        $em->flush();
        $currUser = $this->get('security.token_storage')->getToken()->getUser();
        $currUser = $currUser->getUsername();
        if ($currUser == $user->getUsername())
            return $this->redirectToRoute('logout');
        return $this->redirectToRoute('users');
    }

    /**
     * @Route("/users/delete/{id}")
     */
    public function deleteUser($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('users');
    }

}