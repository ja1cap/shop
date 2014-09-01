<?php
namespace Weasty\Bundle\CatalogBundle\Category;

use Weasty\Resource\Routing\RoutableInterface;

/**
 * Interface CategoryInterface
 * @package Weasty\Bundle\CatalogBundle\Category
 */
interface CategoryInterface extends RoutableInterface {

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