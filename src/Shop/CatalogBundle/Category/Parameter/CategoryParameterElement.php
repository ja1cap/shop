<?php
namespace Shop\CatalogBundle\Category\Parameter;

use Shop\CatalogBundle\Entity\CategoryParameter;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class CategoryParameterElement
 * @package Shop\CatalogBundle\Category\Parameter
 */
class CategoryParameterElement extends CacheCollectionElement
    implements CategoryParameterInterface
{

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return array
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        if($entity instanceof CategoryParameter){
            $data['name'] = $entity->getName();
        }

        return $data;

    }

    /**
     * @return int
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
     * @return boolean
     */
    public function getIsComparable()
    {
        return $this->data['isComparable'];
    }

    /**
     * @return int
     */
    public function getParameterId()
    {
        return $this->data['parameterId'];
    }

    /**
     * Get groupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->data['groupId'];
    }

} 