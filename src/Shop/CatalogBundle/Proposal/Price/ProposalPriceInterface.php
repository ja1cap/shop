<?php
namespace Shop\CatalogBundle\Proposal\Price;

use Weasty\Bundle\CatalogBundle\Proposal\Price\ProposalPriceInterface as BaseProposalPriceInterface;

/**
 * Interface ProposalPriceInterface
 * @package Shop\CatalogBundle\Proposal\Price
 */
interface ProposalPriceInterface extends BaseProposalPriceInterface {

    /**
     * Get parameterValues
     *
     * @return \Doctrine\Common\Collections\Collection|array
     */
    public function getParameterValues();

} 