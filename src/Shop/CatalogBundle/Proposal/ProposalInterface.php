<?php
namespace Shop\CatalogBundle\Proposal;

use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface as BaseProposalInterface;

/**
 * Interface ProposalInterface
 * @package Shop\CatalogBundle\Proposal
 */
interface ProposalInterface extends BaseProposalInterface {

    /**
     * @return \Shop\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory();

} 