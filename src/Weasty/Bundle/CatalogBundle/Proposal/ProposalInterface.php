<?php
namespace Weasty\Bundle\CatalogBundle\Proposal;

use Weasty\Resource\Routing\RoutableInterface;

/**
 * Interface ProposalInterface
 * @package Weasty\Bundle\CatalogBundle\Proposal
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
     * @return \Weasty\Bundle\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory();

    /**
     * Get defaultContractorId
     *
     * @return integer
     */
    public function getDefaultContractorId();

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