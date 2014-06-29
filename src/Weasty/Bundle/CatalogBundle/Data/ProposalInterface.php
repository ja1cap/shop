<?php
namespace Weasty\Bundle\CatalogBundle\Data;

/**
 * Interface ProposalInterface
 * @package Weasty\Bundle\CatalogBundle\Data
 */
interface ProposalInterface {

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

} 