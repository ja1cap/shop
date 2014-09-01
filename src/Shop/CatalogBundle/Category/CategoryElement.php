<?php
namespace Shop\CatalogBundle\Category;

use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class CategoryElement
 * @package Shop\CatalogBundle\Category
 */
class CategoryElement extends CacheCollectionElement
    implements CategoryInterface
{

    /**
     * @return integer|null
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return $this
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {
        return parent::buildData($collection, $entity);
    }

    /**
     * @return array
     */
    public function getRouteParameters()
    {
        return [
            'slug' => $this->data['slug'],
        ];
    }

    /**
     * Get parameters
     *
     * @return \Doctrine\Common\Collections\Collection|array
     */
    public function getParameters()
    {
        // TODO: Implement getParameters() method.
    }

    /**
     * Get parameterGroups
     *
     * @return \Doctrine\Common\Collections\Collection|array
     */
    public function getParameterGroups()
    {
        // TODO: Implement getParameterGroups() method.
    }

} 