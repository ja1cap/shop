<?php
namespace Shop\CatalogBundle\Proposal\Price;

use Weasty\Bundle\CatalogBundle\Proposal\Price\ProposalPriceInterface as BaseProposalPriceInterface;

/**
 * Interface ProposalPriceInterface
 * @package Shop\CatalogBundle\Proposal\Price
 */
interface ProposalPriceInterface extends BaseProposalPriceInterface {

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    /**
     * Get currencyNumericCode
     *
     * @return integer
     */
    public function getCurrencyNumericCode();

    /**
     * Get contractorId
     *
     * @return integer
     */
    public function getContractorId();

    /**
     * @return \Shop\CatalogBundle\Contractor\ContractorInterface
     */
    public function getContractor();

    /**
     * Get parameterValues
     *
     * @return \Doctrine\Common\Collections\Collection|\Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface[]
     */
    public function getParameterValues();

} 