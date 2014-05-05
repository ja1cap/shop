<?php
namespace Weasty\CatalogBundle\Data;

/**
 * Interface CategoryInterface
 * @package Weasty\CatalogBundle\Data
 */
interface CategoryInterface {

    const STATUS_ON = 1;
    const STATUS_OFF = 2;

    /**
     * @return integer|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

} 