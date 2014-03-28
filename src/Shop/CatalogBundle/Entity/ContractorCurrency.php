<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * ContractorCurrency
 */
class ContractorCurrency extends AbstractEntity
{

    const BLR_CURRENCY_NAME = 'Белорусский рубль';
    const BLR_CURRENCY_SHORT_NAME = 'руб';
    const BLR_CURRENCY_ALPHABETIC_CODE = 'BYR';
    const BLR_CURRENCY_NUMERIC_CODE = 974;

    const RUB_CURRENCY_NAME = 'Российский рубль';
    const RUB_CURRENCY_SHORT_NAME = 'руб';
    const RUB_CURRENCY_ALPHABETIC_CODE = 'RUB';
    const RUB_CURRENCY_NUMERIC_CODE = 643;

    const USD_CURRENCY_NAME = 'Доллар США';
    const USD_CURRENCY_SHORT_NAME = '$';
    const USD_CURRENCY_ALPHABETIC_CODE = 'USD';
    const USD_CURRENCY_NUMERIC_CODE = 840;

    const EURO_CURRENCY_NAME = 'Евро';
    const EURO_CURRENCY_SHORT_NAME = '€';
    const EURO_CURRENCY_ALPHABETIC_CODE = 'EUR';
    const EURO_CURRENCY_NUMERIC_CODE = 978;

    /**
     * @var array
     */
    public static $currenciesNumericCodes = array(
        self::BLR_CURRENCY_NUMERIC_CODE,
        self::RUB_CURRENCY_NUMERIC_CODE,
        self::USD_CURRENCY_NUMERIC_CODE,
        self::EURO_CURRENCY_NUMERIC_CODE,
    );

    /**
     * @var array
     */
    public static $currenciesAlphabeticCodes = array(
        self::BLR_CURRENCY_ALPHABETIC_CODE,
        self::RUB_CURRENCY_ALPHABETIC_CODE,
        self::USD_CURRENCY_ALPHABETIC_CODE,
        self::EURO_CURRENCY_ALPHABETIC_CODE,
    );

    /**
     * @var array
     */
    public static $currenciesAlphabeticCodesNumericCodes = array(
        self::BLR_CURRENCY_ALPHABETIC_CODE => self::BLR_CURRENCY_NUMERIC_CODE,
        self::RUB_CURRENCY_ALPHABETIC_CODE => self::RUB_CURRENCY_NUMERIC_CODE,
        self::USD_CURRENCY_ALPHABETIC_CODE => self::USD_CURRENCY_NUMERIC_CODE,
        self::EURO_CURRENCY_ALPHABETIC_CODE => self::EURO_CURRENCY_NUMERIC_CODE,
    );

    /**
     * @var array
     */
    public static $currenciesNumericCodesAlphabeticCodes = array(
        self::BLR_CURRENCY_NUMERIC_CODE => self::BLR_CURRENCY_ALPHABETIC_CODE,
        self::RUB_CURRENCY_NUMERIC_CODE => self::RUB_CURRENCY_ALPHABETIC_CODE,
        self::USD_CURRENCY_NUMERIC_CODE => self::USD_CURRENCY_ALPHABETIC_CODE,
        self::EURO_CURRENCY_NUMERIC_CODE => self::EURO_CURRENCY_ALPHABETIC_CODE,
    );

    /**
     * @var array
     */
    public static $currencyNames = array(
        self::BLR_CURRENCY_NUMERIC_CODE => self::BLR_CURRENCY_NAME,
        self::RUB_CURRENCY_NUMERIC_CODE => self::RUB_CURRENCY_NAME,
        self::USD_CURRENCY_NUMERIC_CODE => self::USD_CURRENCY_NAME,
        self::EURO_CURRENCY_NUMERIC_CODE => self::EURO_CURRENCY_NAME,
    );

    /**
     * @var array
     */
    public static $currencyShortNames = array(
        self::BLR_CURRENCY_NUMERIC_CODE => self::BLR_CURRENCY_SHORT_NAME,
        self::RUB_CURRENCY_NUMERIC_CODE => self::RUB_CURRENCY_SHORT_NAME,
        self::USD_CURRENCY_NUMERIC_CODE => self::USD_CURRENCY_SHORT_NAME,
        self::EURO_CURRENCY_NUMERIC_CODE => self::EURO_CURRENCY_SHORT_NAME,
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $contractorId;

    /**
     * @var integer
     */
    private $numericCode;

    /**
     * @var float
     */
    private $value;


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
     * Set contractorId
     *
     * @param integer $contractorId
     * @return ContractorCurrency
     */
    public function setContractorId($contractorId)
    {
        $this->contractorId = $contractorId;

        return $this;
    }

    /**
     * Get contractorId
     *
     * @return integer 
     */
    public function getContractorId()
    {
        return $this->contractorId;
    }

    /**
     * Set numericCode
     *
     * @param integer $numericCode
     * @return ContractorCurrency
     */
    public function setNumericCode($numericCode)
    {
        $this->numericCode = $numericCode;

        return $this;
    }

    /**
     * Get numericCode
     *
     * @return integer 
     */
    public function getNumericCode()
    {
        return $this->numericCode;
    }

    /**
     * @return bool
     */
    public function getName(){
        if(!isset(self::$currencyNames[$this->getNumericCode()])){
            return false;
        }
        return self::$currencyNames[$this->getNumericCode()];
    }

    /**
     * @return bool
     */
    public function getShortName(){
        if(!isset(self::$currencyShortNames[$this->getNumericCode()])){
            return false;
        }
        return self::$currencyShortNames[$this->getNumericCode()];
    }

    /**
     * Set value
     *
     * @param float $value
     * @return ContractorCurrency
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @var \Shop\CatalogBundle\Entity\Contractor
     */
    private $contractor;

    /**
     * Set contractor
     *
     * @param \Shop\CatalogBundle\Entity\Contractor $contractor
     * @return ContractorCurrency
     */
    public function setContractor(Contractor $contractor = null)
    {
        $this->contractor = $contractor;
        $this->contractorId = $contractor->getId();
        return $this;
    }

    /**
     * Get contractor
     *
     * @return \Shop\CatalogBundle\Entity\Contractor 
     */
    public function getContractor()
    {
        return $this->contractor;
    }
}
