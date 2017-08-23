<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 17.08.2017
 * Time: 13:57
 */

namespace AppBundle\Security\Core\User;


use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBProvider;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserInterface as FOSUI;

class MyFOSUBUserProvider extends BaseFOSUBProvider
{
    /**
     * {@inheritDoc}
     */
     public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'ID';
        $setter_token = $setter.'AccessToken';
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'ID';
            $setter_token = $setter.'AccessToken';
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            if ($service == 'vkontakte'){
                $user->setUsername($response->getRealName());
                $user->setEmail($response->getEmail());
            }else {
                $user->setUsername($username);
                $user->setEmail($username);
            }
            $user->setLoginType($service);
            $user->setPassword($username);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);
            return $user;
        }
        $user = parent::loadUserByOAuthUserResponse($response);
        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
        $user->$setter($response->getAccessToken());
        return $user;
    }


}