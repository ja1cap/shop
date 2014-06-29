<?php
namespace Weasty\Bundle\CatalogBundle\Data;

/**
 * Interface CategoryInterface
 * @package Weasty\Bundle\CatalogBundle\Data
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