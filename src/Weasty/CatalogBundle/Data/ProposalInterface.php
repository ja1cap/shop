<?php
namespace Weasty\CatalogBundle\Data;

/**
 * Interface ProposalInterface
 * @package Weasty\CatalogBundle\Data
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