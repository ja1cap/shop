<?php
namespace Weasty\Bundle\CatalogBundle\Data;

use Weasty\Money\Price\PriceInterface;

/**
 * Interface PriceInterface
 * @package Weasty\Bundle\CatalogBundle\Data
 */
interface ProposalPriceInterface extends \Weasty\Money\Price\PriceInterface {

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return integer
     */
    public function getCategoryId();

    /**
     * @return \Weasty\Bundle\CatalogBundle\Data\CategoryInterface
     */
    public function getCategory();

    /**
     * @return integer
     */
    public function getProposalId();

    /**
     * @return \Weasty\Bundle\CatalogBundle\Data\ProposalInterface
     */
    public function getProposal();

} 