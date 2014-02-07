<?php
namespace Shop\MainBundle\Entity;
use Shop\MainBundle\Form\Type\PillowType;

/**
 * Class Pillow
 * @package Shop\MainBundle\Entity
 */
class Pillow extends Proposal {

    const TYPE = 4;
    const ROUTE = 'shop_pillow';

    const PROPOSAL_TYPE_SINGLE_NAME = 'Подушку';
    const PROPOSAL_TYPE_MULTIPLE_NAME = 'Подушки';

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm()
    {
        return new PillowType();
    }

    /**
     * @return \Shop\MainBundle\Entity\AbstractPrice
     */
    public function createPrice()
    {
        return new PillowPrice();
    }

    /**
     * @return string
     */
    public function getRoute(){
        return self::ROUTE;
    }

}
