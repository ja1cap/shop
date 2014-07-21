<?php
namespace Shop\DiscountBundle\Twig;
use Shop\DiscountBundle\Entity\Action;

/**
 * Class ActionExtension
 * @package Shop\DiscountBundle\Twig
 */
class ActionExtension extends \Twig_Extension {

    /**
     * @var \Shop\DiscountBundle\Entity\ActionRepository
     */
    protected $actionRepository;

    /**
     * @param \Shop\DiscountBundle\Entity\ActionRepository $actionRepository
     */
    function __construct($actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('shop_discount_actions', array($this, 'getActions')),
        );
    }

    /**
     * @return \Shop\DiscountBundle\Entity\Action[]
     */
    public function getActions(){
        return $this->actionRepository->findBy(
            array(
                'status' => Action::STATUS_ON,
            ),
            array(
                'position' => 'ASC',
            )
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_discount_action';
    }

} 