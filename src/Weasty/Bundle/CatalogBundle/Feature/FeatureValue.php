<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Class FeatureValue
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
class FeatureValue implements FeatureValueInterface {

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var FeatureInterface
     */
    protected $feature;

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return FeatureInterface
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param FeatureInterface $feature
     * @return $this
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
        return $this;
    }

    function __toString()
    {
        return (string)$this->getValue();
    }

} 