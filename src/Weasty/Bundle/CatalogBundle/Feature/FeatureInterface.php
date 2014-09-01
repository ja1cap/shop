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
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @param int $weight
     * @return $this
     */
    public function setWeight($weight);

    /**
     * @return int
     */
    public function getWeight();

    /**
     * @return string
     */
    public function __toString();

} 