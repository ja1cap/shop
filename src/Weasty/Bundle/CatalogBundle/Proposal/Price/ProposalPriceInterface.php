<?php
namespace Weasty\Bundle\CatalogBundle\Proposal\Price;

use Weasty\Money\Price\PriceInterface;

/**
 * Interface ProposalPriceInterface
 * @package Weasty\Bundle\CatalogBundle\Proposal\Price
 */
interface ProposalPriceInterface extends PriceInterface {

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return integer
     */
    public function getCategoryId();

    /**
     * @return \Weasty\Bundle\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory();

    /**
     * @return integer
     */
    public function getProposalId();

    /**
     * @return \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal();

} 