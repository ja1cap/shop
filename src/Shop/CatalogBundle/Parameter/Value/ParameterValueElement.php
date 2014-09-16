<?php
namespace Shop\CatalogBundle\Parameter\Value;

use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface;

/**
 * Class ParameterValueElement
 * @package Shop\CatalogBundle\Proposal\Parameter
 */
class ParameterValueElement extends CacheCollectionElement
    implements ParameterValueInterface
{

    /**
     * @var \Weasty\Bundle\CatalogBundle\Parameter\Option\ParameterOptionInterface
     */
    private $option;

    /**
     * @return int
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
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->data['value'];
    }

    /**
     * Get optionId
     *
     * @return integer
     */
    public function getOptionId()
    {
        return $this->data['optionId'];
    }

    /**
     * Get option
     *
     * @return \Weasty\Bundle\CatalogBundle\Parameter\Option\ParameterOptionInterface
     */
    public function getOption()
    {
        if(!$this->option){
            $this->option = $this->getCollectionManager()->getCollection('ShopCatalogBundle:ParameterOption')->get($this->getOptionId());
        }
        return $this->option;
    }

}