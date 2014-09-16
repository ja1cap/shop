<?php
namespace Weasty\Bundle\CatalogBundle\Contractor\Currency;

/**
 * Interface ContractorCurrencyInterface
 * @package Weasty\Bundle\CatalogBundle\Contractor\Currency
 */
interface ContractorCurrencyInterface {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get numericCode
     *
     * @return integer
     */
    public function getNumericCode();

    /**
     * Get value
     *
     * @return float
     */
    public function getValue();

    /**
     * Get contractorId
     *
     * @return integer
     */
    public function getContractorId();

} 