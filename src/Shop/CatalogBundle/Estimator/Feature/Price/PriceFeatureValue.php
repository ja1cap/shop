<?php
namespace Shop\CatalogBundle\Estimator\Feature\Price;

use Shop\CatalogBundle\Estimator\Feature\EstimatedFeatureValue;


/**
 * Class PriceFeatureValue
 * @package Shop\CatalogBundle\Estimator\Feature\Price
 */
class PriceFeatureValue extends EstimatedFeatureValue {

    /**
     * @var \Weasty\Money\Price\PriceInterface
     */
    public $price;

    /**
     * @return \Weasty\Money\Price\PriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param \Weasty\Money\Price\PriceInterface $price
     * @return $this
     */
    public function setPrice($price)
    {

        $this->price = $price;
        $this->value = $price->getValue();
        $this->priority = $price->getValue();

        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        //@TODO throw exception - set price property instead of value
        return $this;
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority)
    {
        //@TODO throw exception - set price property instead of priority
        return $this;
    }

} 