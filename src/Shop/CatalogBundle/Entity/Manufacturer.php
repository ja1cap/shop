<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Weasty\DoctrineBundle\Entity\AbstractEntity;

/**
 * Manufacturer
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
     * @var string
     */
    private $thumbImageFileName;

    /**
     * @var string
     */
    private $imageFileName;


    /**
     * Set thumbImageFileName
     *
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
     *
     * @return string 
     */
    public function getThumbImageFileName()
    {
        return $this->thumbImageFileName;
    }

    public function getThumbImage(){
        return $this->getFile('thumbImageFileName');
    }

    public function setThumbImage($file = null){
        return $this->setFile('thumbImageFileName', $file);
    }

    public function getThumbImageUrl(){
        return $this->getFileUrl($this->getThumbImageFileName()) ?: $this->getImageUrl();
    }

    public function getImage(){
        return $this->getFile('imageFileName');
    }

    public function setImage($file = null){
        return $this->setFile('imageFileName', $file);
    }

    public function getImageUrl(){
        return $this->getFileUrl($this->getImageFileName());
    }

    /**
     * Set imageFileName
     *
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
     *
     * @return string 
     */
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

}
