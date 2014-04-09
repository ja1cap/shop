<?php

namespace Weasty\MoneyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;
use Weasty\MoneyBundle\Data\CurrencyRateInterface;

/**
 * Class CurrencyRate
 * @package Weasty\MoneyBundle\Entity
 */
class CurrencyRate extends AbstractEntity
    implements CurrencyRateInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $sourceAlphabeticCode;

    /**
     * @var integer
     */
    private $sourceNumericCode;

    /**
     * @var string
     */
    private $destinationAlphabeticCode;

    /**
     * @var integer
     */
    private $destinationNumericCode;

    /**
     * @var float
     */
    private $rate;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sourceAlphabeticCode
     *
     * @param string $sourceAlphabeticCode
     * @return CurrencyRate
     */
    public function setSourceAlphabeticCode($sourceAlphabeticCode)
    {
        $this->sourceAlphabeticCode = $sourceAlphabeticCode;

        return $this;
    }

    /**
     * Get sourceAlphabeticCode
     *
     * @return string 
     */
    public function getSourceAlphabeticCode()
    {
        return $this->sourceAlphabeticCode;
    }

    /**
     * Set sourceNumericCode
     *
     * @param integer $sourceNumericCode
     * @return CurrencyRate
     */
    public function setSourceNumericCode($sourceNumericCode)
    {
        $this->sourceNumericCode = $sourceNumericCode;

        return $this;
    }

    /**
     * Get sourceNumericCode
     *
     * @return integer 
     */
    public function getSourceNumericCode()
    {
        return $this->sourceNumericCode;
    }

    /**
     * Set destinationAlphabeticCode
     *
     * @param string $destinationAlphabeticCode
     * @return CurrencyRate
     */
    public function setDestinationAlphabeticCode($destinationAlphabeticCode)
    {
        $this->destinationAlphabeticCode = $destinationAlphabeticCode;

        return $this;
    }

    /**
     * Get destinationAlphabeticCode
     *
     * @return string 
     */
    public function getDestinationAlphabeticCode()
    {
        return $this->destinationAlphabeticCode;
    }

    /**
     * Set destinationNumericCode
     *
     * @param integer $destinationNumericCode
     * @return CurrencyRate
     */
    public function setDestinationNumericCode($destinationNumericCode)
    {
        $this->destinationNumericCode = $destinationNumericCode;

        return $this;
    }

    /**
     * Get destinationNumericCode
     *
     * @return integer 
     */
    public function getDestinationNumericCode()
    {
        return $this->destinationNumericCode;
    }

    /**
     * Set rate
     *
     * @param float $rate
     * @return CurrencyRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return float 
     */
    public function getRate()
    {
        return $this->rate;
    }
}
