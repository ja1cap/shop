<?php
namespace Weasty\Bundle\CatalogBundle\Proposal\Feature;

use Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface;

/**
 * Interface ProposalFeatureValueInterface
 * @package Weasty\Bundle\CatalogBundle\Proposal\Feature
 */
interface ProposalFeatureValueInterface extends FeatureValueInterface {

    /**
     * @return int|null
     */
    public function getPriceId();

    /**
     * @param int $priceId
     * @return $this
     */
    public function setPriceId($priceId);

    /**
     * @return int|null
     */
    public function getProposalId();

    /**
     * @param int $proposalId
     * @return $this
     */
    public function setProposalId($proposalId);

}