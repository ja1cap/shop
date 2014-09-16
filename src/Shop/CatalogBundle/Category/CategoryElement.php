<?php
namespace Shop\CatalogBundle\Category;

use Shop\CatalogBundle\Category\Parameter\CategoryParameterGroupInterface;
use Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface;
use Shop\CatalogBundle\Entity\Category;
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
     * @var array
     */
    public $parameterIds;

    /**
     * @var \Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface[]
     */
    private $parameters;

    /**
     * @var array
     */
    public $parameterGroupIds;

    /**
     * @var \Shop\CatalogBundle\Category\Parameter\CategoryParameterGroupInterface[]
     */
    private $parameterGroups;

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return $this
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        if($entity instanceof Category){

            $categoryParameterCollection = $collection->getCollectionManager()->getCollection('ShopCatalogBundle:CategoryParameter');
            $categoryParameterGroupCollection = $collection->getCollectionManager()->getCollection('ShopCatalogBundle:CategoryParameterGroup');

            foreach($entity->getParameters() as $categoryParameter){

                $categoryParameterElement = $categoryParameterCollection->saveElement($categoryParameter);
                if(!$categoryParameterElement){
                    continue;
                }

                $this->parameterIds[] = $categoryParameter->getId();
                $this->parameters[] = $categoryParameterElement;

            }

            foreach($entity->getParameterGroups() as $categoryParameterGroup){

                $categoryParameterGroupElement = $categoryParameterGroupCollection->saveElement($categoryParameterGroup);
                if(!$categoryParameterGroupElement){
                    continue;
                }

                $this->parameterGroupIds[] = $categoryParameterGroup->getId();
                $this->parameterGroups[] = $categoryParameterGroupElement;

            }

        }

        return $data;

    }

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
     * @return \Doctrine\Common\Collections\Collection|\Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface[]
     */
    public function getParameters()
    {
        if(!$this->parameters && $this->parameterIds){

            $categoryParameterCollection = $this->getCollectionManager()->getCollection('ShopCatalogBundle:CategoryParameter');

            foreach($this->parameterIds as $parameterId){

                $categoryParameter = $categoryParameterCollection->get($parameterId);

                if($categoryParameter instanceof CategoryParameterInterface){
                    $this->parameters[] = $categoryParameter;
                }

            }

        }

        return $this->parameters;
    }

    /**
     * Get parameterGroups
     *
     * @return \Doctrine\Common\Collections\Collection|\Shop\CatalogBundle\Category\Parameter\CategoryParameterGroupInterface[]
     */
    public function getParameterGroups()
    {

        if(!$this->parameterGroups && $this->parameterGroupIds){

            $parameterGroupCollection = $this->getCollectionManager()->getCollection('ShopCatalogBundle:ParameterValue');

            foreach($this->parameterGroupIds as $parameterGroupId){

                $parameterGroup = $parameterGroupCollection->get($parameterGroupId);

                if($parameterGroup instanceof CategoryParameterGroupInterface){
                    $this->parameterGroups[] = $parameterGroup;
                }

            }

        }

        return $this->parameterGroups;

    }

} 