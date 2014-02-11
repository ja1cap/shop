<?php

namespace Shop\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use php_rutils\RUtils;
use Shop\MainBundle\Entity\AbstractEntity;
use Shop\MainBundle\Utils\WordInflector;

/**
 * Class Category
 * @package Shop\CatalogBundle\Entity
 */
class Category extends AbstractEntity
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
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $singularName;

    /**
     * @var string
     */
    private $singularGenitiveName;

    /**
     * @var string
     */
    private $multipleName;

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
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->setSlug(RUtils::translit()->slugify($name));
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
     * Set slug
     *
     * @param string $slug
     * @return Category
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
     * Set singularName
     *
     * @param string $singularName
     * @return Category
     */
    public function setSingularName($singularName)
    {
        $this->singularName = $singularName;
        $this->setSingularGenitiveName(WordInflector::inflect($singularName, WordInflector::CASE_GENITIVE));
        return $this;
    }

    /**
     * Get singularName
     *
     * @return string 
     */
    public function getSingularName()
    {
        return $this->singularName;
    }

    /**
     * @param string $singularGenitiveName
     */
    public function setSingularGenitiveName($singularGenitiveName)
    {
        $this->singularGenitiveName = $singularGenitiveName;
    }

    /**
     * @return string
     */
    public function getSingularGenitiveName()
    {
        return $this->singularGenitiveName ?: WordInflector::inflect($this->getSingularName(), WordInflector::CASE_GENITIVE);
    }

    /**
     * Set multipleName
     *
     * @param string $multipleName
     * @return Category
     */
    public function setMultipleName($multipleName)
    {
        $this->multipleName = $multipleName;

        return $this;
    }

    /**
     * Get multipleName
     *
     * @return string 
     */
    public function getMultipleName()
    {
        return $this->multipleName;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parameters;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->parameters = new ArrayCollection();
    }

    /**
     * Add parameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $parameters
     * @return Category
     */
    public function addParameter(CategoryParameter $parameters)
    {
        $this->parameters[] = $parameters;
        $parameters->setCategory($this);
        return $this;
    }

    /**
     * Remove parameters
     *
     * @param \Shop\CatalogBundle\Entity\CategoryParameter $parameters
     */
    public function removeParameter(CategoryParameter $parameters)
    {
        $this->parameters->removeElement($parameters);
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $proposals;


    /**
     * Add proposals
     *
     * @param \Shop\CatalogBundle\Entity\Proposal $proposals
     * @return Category
     */
    public function addProposal(Proposal $proposals)
    {
        $this->proposals[] = $proposals;
        $proposals->setCategory($this);
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $additionalCategories;


    /**
     * Add additionalCategories
     *
     * @param \Shop\CatalogBundle\Entity\Category $additionalCategories
     * @return Category
     */
    public function addAdditionalCategory(Category $additionalCategories)
    {
        $this->additionalCategories[] = $additionalCategories;

        return $this;
    }

    /**
     * Remove additionalCategories
     *
     * @param \Shop\CatalogBundle\Entity\Category $additionalCategories
     */
    public function removeAdditionalCategory(Category $additionalCategories)
    {
        $this->additionalCategories->removeElement($additionalCategories);
    }

    /**
     * Get additionalCategories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdditionalCategories()
    {
        return $this->additionalCategories;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
    }

}
