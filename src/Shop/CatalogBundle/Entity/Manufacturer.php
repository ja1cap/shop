<?php

namespace Shop\CatalogBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class Manufacturer
 * @package Shop\CatalogBundle\Entity
 */
class Manufacturer extends AbstractEntity
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $imageId;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $image;

    /**
     * @deprecated
     * @var string
     */
    private $thumbImageFileName;

    /**
     * @deprecated
     * @var string
     */
    private $imageFileName;

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
     * Set name
     *
     * @param string $name
     * @return Manufacturer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $proposals;

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
     * @param \Shop\CatalogBundle\Entity\Proposal $proposals
     * @return Manufacturer
     */
    public function addProposal(Proposal $proposals)
    {
        $this->proposals[] = $proposals;

        return $this;
    }

    /**
     * Remove proposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposals
     */
    public function removeProposal(Proposal $proposals)
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
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

    /**
     * Set imageId
     *
     * @param integer $imageId
     * @return Manufacturer
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;

        return $this;
    }

    /**
     * Get imageId
     *
     * @return integer 
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Set thumbImageFileName
     * @deprecated
     * @param string $thumbImageFileName
     * @return Manufacturer
     */
    public function setThumbImageFileName($thumbImageFileName)
    {
        $this->thumbImageFileName = $thumbImageFileName;

        return $this;
    }

    /**
     * Get thumbImageFileName
     * @deprecated
     * @return string 
     */
    public function getThumbImageFileName()
    {
        return $this->thumbImageFileName;
    }

    /**
     * Set imageFileName
     * @deprecated
     * @param string $imageFileName
     * @return Manufacturer
     */
    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * Get imageFileName
     * @deprecated
     * @return string 
     */
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Manufacturer
     */
    public function setImage(Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }
}
