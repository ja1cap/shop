<?php

namespace Weasty\Bundle\PageBundle\Entity;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class PageRow
 * @package Weasty\Bundle\PageBundle\Entity
 */
class PageRow extends AbstractEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $columnsAmount;

    /**
     * @var integer
     */
    private $pageId;

    /**
     * @var \Weasty\Bundle\PageBundle\Entity\Page
     */
    private $page;

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
     * Set columnsAmount
     *
     * @param integer $columnsAmount
     * @return PageRow
     */
    public function setColumnsAmount($columnsAmount)
    {
        $this->columnsAmount = $columnsAmount;

        return $this;
    }

    /**
     * Get columnsAmount
     *
     * @return integer 
     */
    public function getColumnsAmount()
    {
        return $this->columnsAmount;
    }

    /**
     * Set pageId
     *
     * @param integer $pageId
     * @return PageRow
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Get pageId
     *
     * @return integer 
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set page
     *
     * @param \Weasty\Bundle\PageBundle\Entity\Page $page
     * @return PageRow
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
        $this->pageId = $page->getId();
        return $this;
    }

    /**
     * Get page
     *
     * @return \Weasty\Bundle\PageBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}
