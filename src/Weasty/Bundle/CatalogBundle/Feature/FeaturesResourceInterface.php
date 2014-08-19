<?php
namespace Weasty\Bundle\CatalogBundle\Feature;

/**
 * Interface FeaturesResourceInterface
 * @package Weasty\Bundle\CatalogBundle\Feature
 */
interface FeaturesResourceInterface extends \JsonSerializable, \ArrayAccess {

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
     * @return FeatureGroupInterface[]
     */
    public function getGroups();

    /**
     * @param int $id
     * @return FeatureGroupInterface|null
     */
    public function getGroup($id);

    /**
     * @param FeatureGroupInterface $featureGroup
     * @return $this
     */
    public function addGroup(FeatureGroupInterface $featureGroup);

    /**
     * @param int $id
     * @return $this
     */
    public function removeGroup($id);

    /**
     * @return array
     */
    public function toArray();

} 