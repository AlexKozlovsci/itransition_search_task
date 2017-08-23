<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 14.08.2017
 * Time: 14:48
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\User as FOSUBUser;

/**
 * @ORM\Entity
 */
class User extends FOSUBUser
{
    /**
     * @ORM\Id;
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40, name="login_type")
     */
    protected $loginType;

    /** @ORM\Column(name="vkontakte_id", type="string", length=255, nullable=true) */
    protected $vkontakte_id;

    /** @ORM\Column(name="vkontakte_access_token", type="string", length=255, nullable=true) */
    protected $vkontakte_access_token;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="twitter_id", type="string", length=255, nullable=true) */
    protected $twitter_id;

    /** @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true) */
    protected $twitter_access_token;


    public function __construct()
    {
        parent::__construct();
        $this->roles = ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getLoginType()
    {
        return $this->loginType;
    }

    public function setLoginType($loginType = 'site')
    {
        $this->loginType = $loginType;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSalt()
    {
        return null;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }
    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return $this->enabled;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return $this->enabled;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return $this->enabled;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->enabled   ;
    }

    /**
     * @param string $vkontakteId
     * @return User
     */
    public function setVkontakteID($vkontakteID)
    {
        $this->vkontakte_id = $vkontakteID;

        return $this;
    }

    /**
     * @return string
     */
    public function getVkontakteID()
    {
        return $this->vkontakte_id;
    }

    public function setVkontakteAccessToken($vkontakteAcessToken)
    {
        $this->vkontakte_access_token = $vkontakteAcessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getVkontakteAccessToken()
    {
        return $this->vkontakte_access_token;
    }


    /**
     * @param string $facebookId
     * @return User
     */
    public function setFacebookID($facebookID)
    {
        $this->facebook_id = $facebookID;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookID()
    {
        return $this->facebook_id;
    }

    public function setFacebookAccessToken($facebookAcessToken)
    {
        $this->facebook_access_token = $facebookAcessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * @param string $facebookId
     * @return User
     */
    public function setTwitterID($twitterID)
    {
        $this->twitter_id = $twitterID;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterID()
    {
        return $this->twitter_id;
    }

    public function setTwitterAccessToken($twitterAcessToken)
    {
        $this->twitter_access_token = $twitterAcessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitterAccessToken()
    {
        return $this->twitter_access_token;
    }



}