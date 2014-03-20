<?php
namespace Shop\UserBundle\Model;
use Shop\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * Class UserModel
 * @package Shop\MainBundle\Model
 */
class UserModel {

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var \Shop\UserBundle\Entity\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $plainPassword;

    /**
     * @param $encoderFactory
     */
    function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param \Shop\UserBundle\Entity\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \Shop\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $plainPassword
     * @throws \Exception
     */
    public function setPlainPassword($plainPassword)
    {

        if(!$plainPassword && !$this->getUser()->getId()){
            throw new \Exception('New user should have password', 500);
        }

        if($plainPassword){

            $this->plainPassword = $plainPassword;

            $factory = $this->getEncoderFactory();
            $user = $this->getUser();

            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);

        }

    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @return \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    public function getEncoderFactory()
    {
        return $this->encoderFactory;
    }

    /***
     * @param $roles
     * @return $this
     */
    public function setRoles($roles){
        if($this->getUser()){
            $this->getUser()->setRoles($roles);
        }
        return $this;
    }

    /**
     * @return array|\Shop\UserBundle\Entity\Role[]
     */
    public function getRoles(){
        return $this->getUser() ? $this->getUser()->getRoles() : array();
    }

} 