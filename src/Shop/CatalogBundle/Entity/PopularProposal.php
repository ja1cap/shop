<?php

namespace Shop\CatalogBundle\Entity;

use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class PopularProposal
 * @package Shop\CatalogBundle\Entity
 */
class PopularProposal extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $proposalId;

    /**
     * @var integer
     */
    private $position;


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
     * @return PopularProposal
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
     * Set position
     *
     * @param integer $position
     * @return PopularProposal
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
}
