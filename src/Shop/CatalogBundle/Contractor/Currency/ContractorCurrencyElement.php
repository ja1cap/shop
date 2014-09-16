<?php
namespace Shop\CatalogBundle\Contractor\Currency;

use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;

/**
 * Class ContractorCurrencyElement
 * @package Shop\CatalogBundle\Contractor\Currency
 */
class ContractorCurrencyElement extends CacheCollectionElement
    implements  ContractorCurrencyInterface
{

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Get numericCode
     *
     * @return integer
     */
    public function getNumericCode()
    {
        return $this->data['numericCode'];
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->data['value'];
    }

    /**
     * Get contractorId
     *
     * @return integer
     */
    public function getContractorId()
    {
        return $this->data['contractorId'];
    }

} 