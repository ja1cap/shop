<?php
namespace Weasty\Bundle\CatalogBundle\Data;

use Weasty\Resource\Routing\RoutableInterface;

/**
 * Interface CategoryInterface
 * @package Weasty\Bundle\CatalogBundle\Data
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