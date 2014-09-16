<?php
namespace Shop\CatalogBundle\Parameter\Option;

use Shop\CatalogBundle\Entity\ParameterOption;
use Weasty\Bundle\CatalogBundle\Parameter\Option\ParameterOptionInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class ParameterOptionElement
 * @package Shop\CatalogBundle\Parameter\Option
 */
class ParameterOptionElement extends CacheCollectionElement
    implements  ParameterOptionInterface
{

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return array
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {
        $data = parent::buildData($collection, $entity);

        if($entity instanceof ParameterOption){
            $data['priority'] = $entity->getPriority();
        }

        return $data;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Get parameterId
     *
     * @return integer
     */
    public function getParameterId()
    {
        return $this->data['parameterId'];
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->data['position'];
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->data['priority'];
    }

} 