<?php
namespace Shop\CatalogBundle\Element;

use Weasty\Bundle\CatalogBundle\Data\CategoryInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class CategoryElement
 * @package Shop\CatalogBundle\CollectionElement
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

} 