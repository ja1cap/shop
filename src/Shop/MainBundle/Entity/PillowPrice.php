<?php
namespace Shop\MainBundle\Entity;

use Shop\MainBundle\Form\Type\PillowPriceType;

/**
 * Class PillowPrice
 * @package Shop\MainBundle\Entity
 */
class PillowPrice extends AbstractPrice {

    /**
     * @param $proposal
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm($proposal)
    {
        return new PillowPriceType();
    }

}
