<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Interface FeatureValueInterface
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
interface FeatureValueInterface {

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @return FeatureInterface
     */
    public function getFeature();

    /**
     * @param FeatureInterface $feature
     * @return $this
     */
    public function setFeature($feature);

    /**
     * @return string
     */
    public function __toString();

} 