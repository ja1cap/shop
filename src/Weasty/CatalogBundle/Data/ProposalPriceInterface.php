<?php
namespace Weasty\CatalogBundle\Data;

use Weasty\MoneyBundle\Data\PriceInterface;

/**
 * Interface PriceInterface
 * @package Weasty\CatalogBundle\Data
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
     * @return \Weasty\CatalogBundle\Data\CategoryInterface
     */
    public function getCategory();

    /**
     * @return integer
     */
    public function getProposalId();

    /**
     * @return \Weasty\CatalogBundle\Data\ProposalInterface
     */
    public function getProposal();

} 