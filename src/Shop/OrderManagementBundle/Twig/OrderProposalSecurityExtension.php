<?php
namespace Shop\OrderManagementBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrderProposalSecurityExtension
 * @package Shop\OrderManagementBundle\Twig
 */
class OrderProposalSecurityExtension extends \Twig_Extension {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Shop\OrderManagementBundle\Security\OrderProposalSecurityContext
     */
    protected $securityContext;

    /**
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('order_proposal_security_is_granted', array($this, 'isGranted')),
        );
    }

    /**
     * @param $attributes
     * @param $customerOrderProposal
     * @return bool
     */
    public function isGranted($attributes, $customerOrderProposal){
        return $this->getSecurityContext()->isGranted($attributes, $customerOrderProposal);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'order_proposal_security_context';
    }

    /**
     * @return \Shop\OrderManagementBundle\Security\OrderProposalSecurityContext
     */
    public function getSecurityContext()
    {
        if(!$this->securityContext){
            $this->securityContext = $this->container->get('order_proposal_security_context');
        }
        return $this->securityContext;
    }

} 