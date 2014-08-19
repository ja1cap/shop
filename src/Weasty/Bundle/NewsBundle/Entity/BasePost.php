<?php

namespace Weasty\Bundle\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use php_rutils\RUtils;
use Weasty\Bundle\DoctrineBundle\Entity\AbstractEntity;

/**
 * Class BasePost
 * @package Weasty\Bundle\NewsBundle\Entity
 */
abstract class BasePost extends AbstractEntity
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    /**
     * @var array
     */
    public static $statuses = array(
        self::STATUS_ENABLED => 'Доступна',
        self::STATUS_DISABLED => 'Недоступна',
    );


    /**
     * @var integer
     */
    private $status;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \DateTime
     */
    private $createDate;

    /**
     * @var \DateTime
     */
    private $updateDate;

    /**
     * @return array
     */
    public function getRouteParameters()
    {
        return [
            'slug' => $this->getSlug() ?: $this->getId(),
        ];
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return BasePost
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
     * Set title
     *
     * @param string $title
     * @return BasePost
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return BasePost
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return BasePost
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return BasePost
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return BasePost
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
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return BasePost
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setSlug(RUtils::translit()->slugify($this->getTitle()));
        $this->setCreateDate(new \DateTime());
        $this->setUpdateDate(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setSlug(RUtils::translit()->slugify($this->getTitle()));
        $this->setUpdateDate(new \DateTime());
    }

}
