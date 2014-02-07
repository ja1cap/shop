<?php
namespace Shop\MainBundle\Entity;
use Shop\MainBundle\Form\Type\BedBaseType;

/**
 * Class BedBase
 * @package Shop\MainBundle\Entity
 */
class BedBase extends Proposal {

    const TYPE = 3;
    const ROUTE = 'shop_bed_base';

    const PROPOSAL_TYPE_SINGLE_NAME = 'Основание';
    const PROPOSAL_TYPE_MULTIPLE_NAME = 'Основания';

    const MANUFACTURER_VEGAS = 1;

    /**
     * @var array
     */
    public static $manufacturers = array(
        self::MANUFACTURER_VEGAS => 'Vegas',
    );

    /**
     * @return \Shop\MainBundle\Entity\AbstractPrice
     */
    public function createPrice()
    {
        return new BedBasePrice();
    }

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm()
    {
        return new BedBaseType();
    }

    /**
     * @var integer
     */
    private $manufacturerId;


    /**
     * Set manufacturerId
     *
     * @param integer $manufacturerId
     * @return BedBase
     */
    public function setManufacturerId($manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;

        return $this;
    }

    /**
     * Get manufacturerId
     *
     * @return integer 
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return string
     */
    public function getRoute(){
        return self::ROUTE;
    }

}
