<?php
namespace Shop\CatalogBundle\Filter\Checkbox;

use Shop\CatalogBundle\Filter\Filter;

/**
 * Class CheckboxFilter
 * @package Shop\CatalogBundle\Filter\Checkbox
 */
class CheckboxFilter extends Filter implements CheckboxFilterInterface {

    function __construct()
    {
        $this->type = self::TYPE_CHECKBOX;
    }

} 