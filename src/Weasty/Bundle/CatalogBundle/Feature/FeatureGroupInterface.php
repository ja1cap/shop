<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Interface FeatureGroupInterface
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
interface FeatureGroupInterface extends \JsonSerializable, \ArrayAccess {

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
     * @return FeatureInterface[];
     */
    public function getFeatures();

    /**
     * @param int $id
     * @return FeatureInterface|null
     */
    public function getFeature($id);

    /**
     * @param FeatureInterface $feature
     * @return $this
     */
    public function addFeature(FeatureInterface $feature);

    /**
     * @param int $id
     * @return $this
     */
    public function removeFeature($id);

    /**
     * @return string
     */
    public function __toString();

} 