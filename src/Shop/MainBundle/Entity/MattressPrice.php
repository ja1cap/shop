<?php
namespace Shop\MainBundle\Entity;

use Shop\MainBundle\Form\Type\MattressPriceType;

/**
 * Class MattressPrice
 * @package Shop\MainBundle\Entity
 */
class MattressPrice extends AbstractPrice {

    /**
     * @var array
     */
    public static $sizes = array(
        1 => "80x190",
        2 => "80x195",
        3 => "80x200",
        4 => "90x190",
        5 => "90x195",
        6 => "90x200",
        7 => "100x190",
        8 => "100x195",
        9 => "100x200",
        10 => "110x190",
        11 => "110x195",
        12 => "110x200",
        13 => "120x190",
        14 => "120x195",
        15 => "120x200",
        16 => "130x190",
        17 => "130x195",
        18 => "130x200",
        19 => "140x190",
        20 => "140x195",
        21 => "140x200",
        22 => "150x190",
        23 => "150x195",
        24 => "150x200",
        25 => "158x198",
        26 => "160x190",
        27 => "160x195",
        28 => "160x200",
        29 => "170x190",
        30 => "170x195",
        31 => "170x200",
        32 => "180x190",
        33 => "180x195",
        34 => "180x200",
        35 => "190x190",
        36 => "190x195",
        37 => "190x200",
        38 => "195x200",
        39 => "200x190",
        40 => "200x195",
        41 => "200x200",
        42 => "42x82 (овал)",
        43 => "42x83",
        44 => "44x88",
        45 => "54x115",
        46 => "55x115",
        47 => "60x120",
        48 => "65x125",
        49 => "70x140",
    );

    /**
     * @var integer
     */
    protected $sizeId;

    /**
     * @param int $sizeId
     */
    public function setSizeId($sizeId)
    {
        $this->sizeId = $sizeId;
    }

    /**
     * @return int
     */
    public function getSizeId()
    {
        return $this->sizeId;
    }

    /**
     * @return mixed
     */
    public function getSize(){
        return self::$sizes[$this->sizeId];
    }

    /**
     * @return null|string
     */
    public function getDescription(){
        return $this->sizeId ? $this->getSize() . ' (см x см)' : null;
    }

    /**
     * @param Proposal $proposal
     * @return \Symfony\Component\Form\AbstractType
     */
    public function getForm($proposal)
    {
        return new MattressPriceType($proposal);
    }

}
