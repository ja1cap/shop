<?php
namespace Shop\CatalogBundle\Category\Parameter;

use Shop\CatalogBundle\Entity\CategoryParameterGroup;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class CategoryParameterGroupElement
 * @package Shop\CatalogBundle\Category\Parameter
 */
class CategoryParameterGroupElement extends CacheCollectionElement
    implements CategoryParameterGroupInterface
{

    /**
     * @var array
     */
    public $parameterIds = [];

    /**
     * @var \Doctrine\Common\Collections\Collection|\Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface[]
     */
    private $parameters = [];

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return $this
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        if($entity instanceof CategoryParameterGroup){

            $categoryParameterCollection = $collection->getCollectionManager()->getCollection('ShopCatalogBundle:CategoryParameter');

            foreach($entity->getParameters() as $categoryParameter){

                $categoryParameterElement = $categoryParameterCollection->saveElement($categoryParameter);
                if(!$categoryParameterElement){
                    continue;
                }

                $this->parameterIds[] = $categoryParameter->getId();
                $this->parameters[] = $categoryParameterElement;

            }

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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
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

} 