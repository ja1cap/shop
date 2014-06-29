<?php
namespace Shop\CatalogBundle\Filter;

/**
 * Class ParameterFilter
 * @package Shop\CatalogBundle\Filter
 */
class ParameterFilter extends Filter
    implements ParameterFilterInterface
{

    /**
     * @var int
     */
    public $parameterId;

    /**
     * @return int
     */
    public function getParameterId()
    {
        return $this->parameterId;
    }

    /**
     * @param int $parameterId
     * @return $this
     */
    public function setParameterId($parameterId)
    {
        $this->parameterId = $parameterId;
        return $this;
    }

} 