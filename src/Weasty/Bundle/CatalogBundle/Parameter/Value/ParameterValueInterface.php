<?php
namespace Weasty\Bundle\CatalogBundle\Parameter\Value;

/**
 * Interface ParameterValueInterface
 * @package Weasty\Bundle\CatalogBundle\Parameter\Value
 */
interface ParameterValueInterface {

    /**
     * @return int
     */
    public function getId();

    /**
     * Get parameterId
     *
     * @return integer
     */
    public function getParameterId();

    /**
     * Get optionId
     *
     * @return integer
     */
    public function getOptionId();

    /**
     * Get option
     *
     * @return \Weasty\Bundle\CatalogBundle\Parameter\Option\ParameterOptionInterface
     */
    public function getOption();

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

} 