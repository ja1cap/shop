<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Shop\MainBundle\Entity\AbstractEntity;

/**
 * Class CustomerOrder
 * @package Shop\CatalogBundle\Entity
 */
class CustomerOrder extends AbstractEntity
{

    const STATUS_NEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_DENIED = 3;
    const STATUS_COMPLETE = 4;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $customerName;

    /**
     * @var string
     */
    private $customerPhone;

    /**
     * @var string
     */
    private $customerEmail;

    /**
     * @var string
     */
    private $customerComment;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $acceptDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @var \DateTime
     */
    private $denyDate;

    /**
     * @var string
     */
    private $denyComment;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set status
     *
     * @param integer $status
     * @return CustomerOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set customerName
     *
     * @param string $customerName
     * @return CustomerOrder
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * Get customerName
     *
     * @return string 
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Set customerPhone
     *
     * @param string $customerPhone
     * @return CustomerOrder
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    /**
     * Get customerPhone
     *
     * @return string 
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * Set customerEmail
     *
     * @param string $customerEmail
     * @return CustomerOrder
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }

    /**
     * Get customerEmail
     *
     * @return string 
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Set customerComment
     *
     * @param string $customerComment
     * @return CustomerOrder
     */
    public function setCustomerComment($customerComment)
    {
        $this->customerComment = $customerComment;

        return $this;
    }

    /**
     * Get customerComment
     *
     * @return string 
     */
    public function getCustomerComment()
    {
        return $this->customerComment;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return CustomerOrder
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set acceptDate
     *
     * @param \DateTime $acceptDate
     * @return CustomerOrder
     */
    public function setAcceptDate($acceptDate)
    {
        $this->acceptDate = $acceptDate;

        return $this;
    }

    /**
     * Get acceptDate
     *
     * @return \DateTime 
     */
    public function getAcceptDate()
    {
        return $this->acceptDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return CustomerOrder
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set denyDate
     *
     * @param \DateTime $denyDate
     * @return CustomerOrder
     */
    public function setDenyDate($denyDate)
    {
        $this->denyDate = $denyDate;

        return $this;
    }

    /**
     * Get denyDate
     *
     * @return \DateTime 
     */
    public function getDenyDate()
    {
        return $this->denyDate;
    }

    /**
     * Set denyComment
     *
     * @param string $denyComment
     * @return CustomerOrder
     */
    public function setDenyComment($denyComment)
    {
        $this->denyComment = $denyComment;

        return $this;
    }

    /**
     * Get denyComment
     *
     * @return string 
     */
    public function getDenyComment()
    {
        return $this->denyComment;
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $proposals;

    /**
     * @var \Shop\CatalogBundle\Entity\Action
     */
    private $action;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

    /**
     * Add proposals
     *
     * @param \Shop\CatalogBundle\Entity\CustomerOrderProposal $proposals
     * @return CustomerOrder
     */
    public function addProposal(CustomerOrderProposal $proposals)
    {
        $this->proposals[] = $proposals;
        $proposals->setOrder($this);
        return $this;
    }

    /**
     * Remove proposals
     *
     * @param \Shop\CatalogBundle\Entity\CustomerOrderProposal $proposals
     */
    public function removeProposal(CustomerOrderProposal $proposals)
    {
        $this->proposals->removeElement($proposals);
    }

    /**
     * Get proposals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProposals()
    {
        return $this->proposals;
    }

    /**
     * Set action
     *
     * @param \Shop\CatalogBundle\Entity\Action $action
     * @return CustomerOrder
     */
    public function setAction(Action $action = null)
    {
        $this->action = $action;
        $this->actionId = $action->getId();
        return $this;
    }

    /**
     * Get action
     *
     * @return \Shop\CatalogBundle\Entity\Action 
     */
    public function getAction()
    {
        return $this->action;
    }
}
