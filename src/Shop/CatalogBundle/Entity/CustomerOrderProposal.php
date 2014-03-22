<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * Class CustomerOrderProposal
 * @package Shop\CatalogBundle\Entity
 */
class CustomerOrderProposal extends AbstractEntity
    implements \Serializable
{
    /**
     * @var integer
     */
    private $id;

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
     * Set order
     *
     * @param \Shop\CatalogBundle\Entity\CustomerOrder $order
     * @return CustomerOrderProposal
     */
    public function setOrder(CustomerOrder $order = null)
    {
        $this->order = $order;
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
        $this->priceValue = $price->getExchangedValue();
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

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
            'orderId' => $this->getOrder()->getId(),
            'proposalId' => $this->getProposal()->getId(),
            'priceId' => $this->getPrice()->getId(),
            'priceValue' => $this->getPriceValue(),
            'amount' => $this->getAmount(),
        ));
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        // TODO: Implement unserialize() method.
    }

}
