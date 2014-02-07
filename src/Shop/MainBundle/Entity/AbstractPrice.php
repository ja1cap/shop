<?php
namespace Shop\MainBundle\Entity;

/**
 * Class AbstractPrice
 * @package Shop\MainBundle\Entity
 */
abstract class AbstractPrice extends AbstractEntity {

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = preg_replace("/([^0-9\\.])/i", "", $value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @var \Shop\MainBundle\Entity\Proposal
     */
    private $proposal;


    /**
     * Set proposal
     *
     * @param \Shop\MainBundle\Entity\Proposal $proposal
     * @return AbstractPrice
     */
    public function setProposal(Proposal $proposal = null)
    {
        $this->proposal = $proposal;
        $this->proposalId = $proposal->getId();
        return $this;
    }

    /**
     * Get proposal
     *
     * @return \Shop\MainBundle\Entity\Proposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }
    /**
     * @var integer
     */
    private $id;


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
     * @param Proposal $proposal
     * @return \Symfony\Component\Form\AbstractType
     */
    abstract public function getForm($proposal);

    /**
     * @var integer
     */
    private $proposalId;


    /**
     * Set proposalId
     *
     * @param integer $proposalId
     * @return AbstractPrice
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
     * @return string
     */
    function __toString()
    {
        return $this->getValue();
    }

}
