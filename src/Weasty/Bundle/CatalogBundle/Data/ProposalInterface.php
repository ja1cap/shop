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
     * @return integer
     */
    public function getCategoryId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return int|null
     */
    public function getImageId();

    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface
     */
    public function getImage();

    /**
     * @return \Sonata\MediaBundle\Model\MediaInterface[]
     */
    public function getImages();

} 