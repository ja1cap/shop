<?php
namespace Shop\MainBundle\Entity;
use Shop\MainBundle\Form\Type\MattressType;

/**
 * Class Mattress
 * @package Shop\MainBundle\Entity
 */
class Mattress extends Proposal {

    const TYPE = 2;
    const ROUTE = 'shop_mattress';

    const PROPOSAL_TYPE_SINGLE_NAME = 'Матрас';
    const PROPOSAL_TYPE_MULTIPLE_NAME = 'Матрасы';

    const MANUFACTURER_VEGAS = 1;
    const MANUFACTURER_KONDOR = 2;
    const MANUFACTURER_SIESTA = 3;

    /**
     * @var array
     */
    public static $manufacturers = array(
        self::MANUFACTURER_SIESTA => 'Siesta',
        self::MANUFACTURER_KONDOR => 'Kondor',
        self::MANUFACTURER_VEGAS => 'Vegas',
    );

    /**
     * @var integer
     */
    protected $manufacturerId;

    /**
     * @param int $manufacturerId
     */
    public function setManufacturerId($manufacturerId)
    {
        $this->manufacturerId = $manufacturerId;
    }

    /**
     * @return int
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return mixed
     */
    public function getManufacturer(){
        return self::$manufacturers[$this->getManufacturerId()];
    }

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm()
    {
        return new MattressType();
    }

    /**
     * @return \Shop\MainBundle\Entity\AbstractPrice
     */
    public function createPrice()
    {
        return new MattressPrice();
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $bedBases;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bedBases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add bedBases
     *
     * @param \Shop\MainBundle\Entity\BedBase $bedBases
     * @return Mattress
     */
    public function addBedBase(\Shop\MainBundle\Entity\BedBase $bedBases)
    {
        $this->bedBases[] = $bedBases;

        return $this;
    }

    /**
     * Remove bedBases
     *
     * @param \Shop\MainBundle\Entity\BedBase $bedBases
     */
    public function removeBedBase(\Shop\MainBundle\Entity\BedBase $bedBases)
    {
        $this->bedBases->removeElement($bedBases);
    }

    /**
     * Get bedBases
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBedBases()
    {
        return $this->bedBases;
    }

    /**
     * @return string
     */
    public function getRoute(){
        return self::ROUTE;
    }

}
