<?php
namespace Weasty\Bundle\CatalogBundle\Data;
use Weasty\Resource\Routing\RoutableInterface;

/**
 * Interface ProposalInterface
 * @package Weasty\Bundle\CatalogBundle\Data
 */
interface ProposalInterface extends RoutableInterface {

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

} 