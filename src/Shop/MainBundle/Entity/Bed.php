<?php
namespace Shop\MainBundle\Entity;
use Shop\MainBundle\Form\Type\BedType;

/**
 * Class Bed
 * @package Shop\MainBundle\Entity
 */
class Bed extends Proposal {

    const TYPE = 1;
    const ROUTE = 'shop_bed';

    const PROPOSAL_TYPE_SINGLE_NAME = 'Кровать';
    const PROPOSAL_TYPE_MULTIPLE_NAME = 'Кровати';

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm()
    {
        return new BedType();
    }

    /**
     * @return \Shop\MainBundle\Entity\AbstractPrice
     */
    public function createPrice()
    {
        return new BedPrice();
    }

    /**
     * @return string
     */
    public function getRoute(){
        return self::ROUTE;
    }

}
