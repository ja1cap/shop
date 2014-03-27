<?php
namespace Shop\OrderManagementBundle\Security;

use Shop\CatalogBundle\Entity\CustomerOrderProposal;
use Shop\UserBundle\Entity\Admin;
use Shop\UserBundle\Entity\Manager;
use Shop\UserBundle\Entity\ManagerContractor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class OrderProposalSecurityContext
 * @package Shop\OrderManagementBundle\Security
 */
class OrderProposalSecurityContext
    implements SecurityContextInterface
{
    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @param SecurityContext $securityContext
     */
    function __construct(SecurityContext $securityContext)
    {
        $this->token = $securityContext->getToken();
    }

    /**
     * Returns the current security token.
     *
     * @return TokenInterface|null A TokenInterface instance or null if no authentication information is available
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the authentication token.
     *
     * @param TokenInterface $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }

    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     *
     * @param mixed $attributes
     * @param mixed $object
     *
     * @return Boolean
     */
    public function isGranted($attributes, $object = null)
    {

        if(!is_array($attributes)){

            if(is_string($attributes)){
                $attributes = array($attributes);
            } else {
                $attributes = array();
            }

        }

        $isGranted = false;

        if(!$attributes || !$object instanceof CustomerOrderProposal){
            return $isGranted;
        }

        $grantedAttributes = array();
        $user = $this->getToken()->getUser();

        //Check admin
        if($user instanceof Admin){

            $grantedAttributes[] = '*';

        }
        //Check manager
        elseif($user instanceof Manager){

            $grantedAttributes[] = 'view';

            if($object->getManagerId() == $user->getId()){

                $grantedAttributes[] = 'edit';

            } elseif(!$object->getManagerId()) {

                foreach($user->getContractors() as $managerContractor){

                    if($managerContractor instanceof ManagerContractor){

                        $isGrantedContractor = (!$managerContractor->getContractorId() || $object->getPrice()->getContractorId() == $managerContractor->getContractorId());
                        $isGrantedCategory = (!$managerContractor->getCategoriesIds() || in_array($object->getProposal()->getCategoryId(), $managerContractor->getCategoriesIds()));

                        if($isGrantedContractor && $isGrantedCategory){

                            $grantedAttributes[] = 'set manager';
                            break;

                        }

                    }

                }

            }

        }

        if(in_array('*', $grantedAttributes)){

            $isGranted = true;

        } else {

            $isGranted = count(array_intersect($attributes, $grantedAttributes)) == count($attributes);

        }

        return $isGranted;

    }

} 