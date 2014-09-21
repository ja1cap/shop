<?php
namespace Shop\DiscountBundle\Gift;

use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;

/**
 * Class ActionConditionGiftElement
 * @package Shop\DiscountBundle\Gift
 */
class ActionConditionGiftElement extends CacheCollectionElement
    implements ActionConditionGiftInterface
{

    /**
     * @var \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    private $proposal;

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
     * Get proposalId
     *
     * @return integer
     */
    public function getProposalId()
    {
        return $this->data['proposalId'];
    }

    /**
     * Get proposal
     *
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal()
    {

        if(!$this->getProposalId()){
            return null;
        }

        if(!$this->proposal){
            $this->proposal = $this->getCollectionManager()->getCollection('ShopCatalogBundle:Proposal')->get($this->getProposalId());
        }

        return $this->proposal;

    }

    /**
     * Get conditionId
     *
     * @return integer
     */
    public function getConditionId()
    {
        return $this->data['conditionId'];
    }

} 