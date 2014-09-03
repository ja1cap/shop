<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Interface FeatureInterface
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
interface FeatureInterface extends \JsonSerializable, \ArrayAccess {

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @param $key
     * @param \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface $value
     * @return $this
     */
    public function addFeatureValue($key, $value);

    /**
     * @param $key
     * @return $this
     */
    public function removeFeatureValue($key);

    /**
     * @param $key
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface|null
     */
    public function getFeatureValue($key);

    /**
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureValueInterface[]
     */
    public function getFeatureValues();

        /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function __toString();

} 