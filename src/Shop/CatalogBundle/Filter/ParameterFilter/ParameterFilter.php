<?php
namespace Shop\CatalogBundle\Filter\ParameterFilter;

use Shop\CatalogBundle\Filter\OptionsFilter\OptionsFilter;

/**
 * Class ParameterFilter
 * @package Shop\CatalogBundle\Filter\ParameterFilter
 */
class ParameterFilter extends OptionsFilter
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