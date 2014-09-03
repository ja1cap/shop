<?php
namespace Weasty\Bundle\CatalogBundle\Parameter\Option;

/**
 * Interface ParameterOptionInterface
 * @package Weasty\Bundle\CatalogBundle\Data
 */
interface ParameterOptionInterface {

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Get parameterId
     *
     * @return integer
     */
    public function getParameterId();

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition();

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority();

} 