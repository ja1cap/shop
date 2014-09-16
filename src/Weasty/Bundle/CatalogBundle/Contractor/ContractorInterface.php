<?php
namespace Weasty\Bundle\CatalogBundle\Contractor;

/**
 * Interface ContractorInterface
 * @package Weasty\Bundle\CatalogBundle\Contractor
 */
interface ContractorInterface {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get defaultCurrencyNumericCode
     *
     * @return integer
     */
    public function getDefaultCurrencyNumericCode();

    /**
     * Get currencies
     *
     * @return \Doctrine\Common\Collections\Collection|\Weasty\Bundle\CatalogBundle\Contractor\Currency\ContractorCurrencyInterface[]
     */
    public function getCurrencies();

}