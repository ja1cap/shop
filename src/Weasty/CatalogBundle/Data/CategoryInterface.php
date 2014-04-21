<?php
namespace Weasty\CatalogBundle\Data;

/**
 * Interface CategoryInterface
 * @package Weasty\CatalogBundle\Data
 */
interface CategoryInterface {

    /**
     * @return integer|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

} 