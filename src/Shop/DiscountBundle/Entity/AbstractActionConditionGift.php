<?php
namespace Shop\DiscountBundle\Entity;

use Shop\CatalogBundle\Entity\Proposal;
use Shop\DiscountBundle\Gift\ActionConditionGiftElement;
use Shop\DiscountBundle\Gift\ActionConditionGiftInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollectionEntityInterface;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class AbstractActionConditionGift
 * @package Shop\DiscountBundle\Entity
 */
abstract class AbstractActionConditionGift extends AbstractEntity
    implements  ActionConditionGiftInterface,
                CacheCollectionEntityInterface
{

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var integer
     */
    protected $conditionId;

    /**
     * @var integer
     */
    protected $proposalId;

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    protected $proposal;

    /**
     * @param $collection \Weasty\Doctrine\Cache\Collection\CacheCollection
     * @return \Weasty\Doctrine\Cache\Collection\CacheCollectionElementInterface
     */
    public function createCollectionElement($collection)
    {
        return new ActionConditionGiftElement($collection, $this);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set proposalId
     *
     * @param integer $proposalId
     * @return ActionConditionGift
     */
    public function setProposalId($proposalId)
    {
        $this->proposalId = $proposalId;

        return $this;
    }

    /**
     * Get proposalId
     *
     * @return integer
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * Set conditionId
     *
     * @param integer $conditionId
     * @return ActionConditionGift
     */
    public function setConditionId($conditionId)
    {
        $this->conditionId = $conditionId;

        return $this;
    }

    /**
     * Get conditionId
     *
     * @return integer
     */
    public function getConditionId()
    {
        return $this->conditionId;
    }

    /**
     * Set proposal
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposal
     * @return AbstractActionConditionGift
     */
    public function setProposal(Proposal $proposal = null)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * Get proposal
     *
     * @return \Shop\CatalogBundle\Entity\Proposal 
     */
    public function getProposal()
    {
        return $this->proposal;
    }
}
