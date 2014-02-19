<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * Class CustomerOrderProposal
 * @package Shop\CatalogBundle\Entity
 */
class CustomerOrderProposal extends AbstractEntity
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
    private $priceId;

    /**
     * @var integer
     */
    private $amount;

    /**
     * @var string
     */
    private $priceValue;


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
     * @return CustomerOrderProposal
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
     * Set priceId
     *
     * @param integer $priceId
     * @return CustomerOrderProposal
     */
    public function setPriceId($priceId)
    {
        $this->priceId = $priceId;

        return $this;
    }

    /**
     * Get priceId
     *
     * @return integer 
     */
    public function getPriceId()
    {
        return $this->priceId;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return CustomerOrderProposal
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set priceValue
     *
     * @param string $priceValue
     * @return CustomerOrderProposal
     */
    public function setPriceValue($priceValue)
    {
        $this->priceValue = $priceValue;

        return $this;
    }

    /**
     * Get priceValue
     *
     * @return string 
     */
    public function getPriceValue()
    {
        return $this->priceValue;
    }
    /**
     * @var integer
     */
    private $orderId;

    /**
     * @var \Shop\CatalogBundle\Entity\CustomerOrder
     */
    private $order;

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    private $proposal;

    /**
     * @var \Shop\CatalogBundle\Entity\Price
     */
    private $price;


    /**
     * Set orderId
     *
     * @param integer $orderId
     * @return CustomerOrderProposal
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set order
     *
     * @param \Shop\CatalogBundle\Entity\CustomerOrder $order
     * @return CustomerOrderProposal
     */
    public function setOrder(CustomerOrder $order = null)
    {
        $this->order = $order;
        $this->orderId = $order->getId();
        return $this;
    }

    /**
     * Get order
     *
     * @return \Shop\CatalogBundle\Entity\CustomerOrder 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set proposal
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposal
     * @return CustomerOrderProposal
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
     * @return \Shop\CatalogBundle\Entity\Proposal 
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * Set price
     *
     * @param \Shop\CatalogBundle\Entity\Price $price
     * @return CustomerOrderProposal
     */
    public function setPrice(Price $price = null)
    {
        $this->price = $price;
        $this->proposalId = $price->getId();
        $this->priceValue = $price->getValue();
        return $this;
    }

    /**
     * Get price
     *
     * @return \Shop\CatalogBundle\Entity\Price 
     */
    public function getPrice()
    {
        return $this->price;
    }
}
